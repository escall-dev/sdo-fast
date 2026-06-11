<?php
/**
 * Transaction Pagination & Filtering API for SDO FAST.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces authorization

if ($fastPDO === null) {

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}

$userRole = $_SESSION['user_role'];
$userId = $_SESSION['user_id'];

// Retrieve parameters with defaults
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = isset($_GET['per_page']) ? min(100, max(5, (int)$_GET['per_page'])) : 20;
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$dateStart = isset($_GET['date_start']) ? trim($_GET['date_start']) : '';
$dateEnd = isset($_GET['date_end']) ? trim($_GET['date_end']) : '';
$requestorId = isset($_GET['requestor_id']) ? (int)$_GET['requestor_id'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sortBy = isset($_GET['sort_by']) ? trim($_GET['sort_by']) : 'created_at';
$sortOrder = isset($_GET['sort_order']) && strtolower($_GET['sort_order']) === 'asc' ? 'ASC' : 'DESC';

// Map sortable columns
$allowedSortColumns = [
    'tracking_number' => 't.tracking_number',
    'amount' => 't.amount',
    'status' => 't.current_status',
    'created_at' => 't.created_at'
];
$sortCol = $allowedSortColumns[$sortBy] ?? 't.created_at';

// Build Dynamic SQL Where clause
$whereClauses = [];
$params = [];

// Enforce view_bactrack permission check
if ($type === 'BACtrack' && !hasPermission('view_bactrack')) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Forbidden: Your role does not permit access to BACtrack Transactions.'
    ]);
    exit;
}

// 1. Data visibility scope filter
$whereClauses[] = get_data_scope_filter($userRole, $userId, 't');

if (!hasPermission('view_bactrack')) {
    $whereClauses[] = "t.transaction_type != 'BACtrack'";
}

if ($requestorId > 0) {
    $whereClauses[] = "t.requestor_id = :filter_req_id";
    $params['filter_req_id'] = $requestorId;
}

// 2. Transaction Type Filter
if (!empty($type)) {
    $whereClauses[] = "t.transaction_type = :type";
    $params['type'] = $type;
}

// 3. Status Filter
if (!empty($status)) {
    $whereClauses[] = "t.current_status = :status";
    $params['status'] = $status;
}

// 4. Date Range Filters
if (!empty($dateStart)) {
    $whereClauses[] = "t.created_at >= :date_start";
    $params['date_start'] = $dateStart . ' 00:00:00';
}
if (!empty($dateEnd)) {
    $whereClauses[] = "t.created_at <= :date_end";
    $params['date_end'] = $dateEnd . ' 23:59:59';
}

// 5. Keyword Search (Tracking number or Event Name)
if (!empty($search)) {
    $whereClauses[] = "(t.tracking_number LIKE :search OR t.event_name LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

// Combine where clauses
$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = ' WHERE ' . implode(' AND ', $whereClauses);
}

try {
    // 1. Query Total matching count
    $countSql = "SELECT COUNT(*) FROM transactions t" . $whereSql;
    $countStmt = $fastPDO->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = (int)$countStmt->fetchColumn();

    // 2. Query Paginated Records
    $offset = ($page - 1) * $perPage;
    $dataSql = "
        SELECT t.*, u.full_name as requestor_name, u.email as requestor_email, 
               d.dv_number, d.bir_2307_number, d.tax_type,
               cad.category as cash_advance_category
        FROM transactions t
        LEFT JOIN users u ON t.requestor_id = u.id
        LEFT JOIN document_details d ON t.id = d.transaction_id
        LEFT JOIN cash_advance_details cad ON t.id = cad.transaction_id
        " . $whereSql . "
        ORDER BY " . $sortCol . " " . $sortOrder . "
        LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;
        
    $dataStmt = $fastPDO->prepare($dataSql);
    $dataStmt->execute($params);
    $transactions = $dataStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'message' => 'Transactions retrieved successfully.',
        'data' => [
            'transactions' => $transactions,
            'total_count' => $totalCount,
            'page' => $page,
            'per_page' => $perPage
        ]
    ]);

} catch (PDOException $e) {
    error_log("Transactions query error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected database error occurred while querying transactions.'
    ]);
}
