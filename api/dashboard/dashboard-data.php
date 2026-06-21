<?php
/**
 * Dashboard Data API for SDO FAST.
 * Returns filtered dashboard statistics, chart data, and recent transactions.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

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

// ── Retrieve filter parameters ──────────────────────────────────────────────
$annual     = isset($_GET['annual']) ? trim($_GET['annual']) : '';
$monthly    = isset($_GET['monthly']) ? trim($_GET['monthly']) : '';
$type       = isset($_GET['type']) ? trim($_GET['type']) : '';
$status     = isset($_GET['status']) ? trim($_GET['status']) : '';
$dateStart  = isset($_GET['date_start']) ? trim($_GET['date_start']) : '';
$dateEnd    = isset($_GET['date_end']) ? trim($_GET['date_end']) : '';

// ── Build WHERE clauses ─────────────────────────────────────────────────────
// All queries use "transactions t" alias — single consistent WHERE builder
$whereClauses = [];
$params = [];

// Data scope filter (with 't' alias for all queries)
$whereClauses[] = get_data_scope_filter($userRole, $userId, 't');

// BACtrack permission
if (!hasPermission('view_bactrack')) {
    $whereClauses[] = "t.transaction_type != 'BACtrack'";
}

// Annual filter (YEAR)
if (!empty($annual) && is_numeric($annual)) {
    $whereClauses[] = "YEAR(t.created_at) = :annual";
    $params['annual'] = (int)$annual;
}

// Monthly filter (MONTH)
if (!empty($monthly) && is_numeric($monthly)) {
    $whereClauses[] = "MONTH(t.created_at) = :monthly";
    $params['monthly'] = (int)$monthly;
}

// Transaction Type filter
if (!empty($type)) {
    $whereClauses[] = "t.transaction_type = :type";
    $params['type'] = $type;
}

// Status filter
if (!empty($status)) {
    $whereClauses[] = "t.current_status = :status";
    $params['status'] = $status;
}

// Date range filters
if (!empty($dateStart)) {
    $whereClauses[] = "t.created_at >= :date_start";
    $params['date_start'] = $dateStart . ' 00:00:00';
}
if (!empty($dateEnd)) {
    $whereClauses[] = "t.created_at <= :date_end";
    $params['date_end'] = $dateEnd . ' 23:59:59';
}

$whereSQL = 'WHERE ' . implode(' AND ', $whereClauses);

// ── Execute Queries ─────────────────────────────────────────────────────────
try {
    // 1. Total Transactions
    $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions t " . $whereSQL);
    $stmt->execute($params);
    $totalTransactions = (int)$stmt->fetchColumn();

    // 2. Pending Approvals
    $stmt = $fastPDO->prepare(
        "SELECT COUNT(*) FROM transactions t " . $whereSQL .
        " AND t.current_status IN ('Pending Requestor','Pending Budget','Pending Accounting Support','Pending Signatory Approval','For Payment')"
    );
    $stmt->execute($params);
    $pendingApprovals = (int)$stmt->fetchColumn();

    // 3. Approved (Released)
    $stmt = $fastPDO->prepare(
        "SELECT COUNT(*) FROM transactions t " . $whereSQL . " AND t.current_status = 'Released'"
    );
    $stmt->execute($params);
    $approvedTransactions = (int)$stmt->fetchColumn();

    // 4. Disapproved (Rejected)
    $stmt = $fastPDO->prepare(
        "SELECT COUNT(*) FROM transactions t " . $whereSQL . " AND t.current_status = 'Rejected'"
    );
    $stmt->execute($params);
    $rejectedTransactions = (int)$stmt->fetchColumn();

    // 5. Returned
    $stmt = $fastPDO->prepare(
        "SELECT COUNT(*) FROM transactions t " . $whereSQL . " AND t.current_status = 'Returned'"
    );
    $stmt->execute($params);
    $returnedTransactions = (int)$stmt->fetchColumn();

    // 6. Total Disbursed (net_amount of Released)
    $stmt = $fastPDO->prepare(
        "SELECT COALESCE(SUM(t.net_amount), 0) FROM transactions t " . $whereSQL . " AND t.current_status = 'Released'"
    );
    $stmt->execute($params);
    $totalDisbursed = (float)$stmt->fetchColumn();

    // 7. Total Tax Deducted
    $stmt = $fastPDO->prepare(
        "SELECT COALESCE(SUM(t.tax_amount), 0) FROM transactions t " . $whereSQL . " AND t.current_status = 'Released'"
    );
    $stmt->execute($params);
    $totalTaxDeducted = (float)$stmt->fetchColumn();

    // 8. Recent Transactions (latest 10)
    $recentQuery = "
        SELECT t.*, u.full_name as requestor_name 
        FROM transactions t 
        LEFT JOIN users u ON t.requestor_id = u.id
        " . $whereSQL . "
        ORDER BY t.created_at DESC LIMIT 10
    ";
    $stmt = $fastPDO->prepare($recentQuery);
    $stmt->execute($params);
    $recentTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 9. Monthly Chart Data — determine which year to use
    $chartYear = !empty($annual) ? (int)$annual : (int)date('Y');
    $monthlyParams = [];
    // Rebuild WHERE without annual/monthly but keep other filters for monthly aggregation
    $monthlyWhere = [];
    $monthlyWhere[] = get_data_scope_filter($userRole, $userId, 't');
    if (!hasPermission('view_bactrack')) {
        $monthlyWhere[] = "t.transaction_type != 'BACtrack'";
    }
    if (!empty($type)) {
        $monthlyWhere[] = "t.transaction_type = :mtype";
        $monthlyParams['mtype'] = $type;
    }
    if (!empty($status)) {
        $monthlyWhere[] = "t.current_status = :mstatus";
        $monthlyParams['mstatus'] = $status;
    }
    if (!empty($dateStart)) {
        $monthlyWhere[] = "t.created_at >= :mdate_start";
        $monthlyParams['mdate_start'] = $dateStart . ' 00:00:00';
    }
    if (!empty($dateEnd)) {
        $monthlyWhere[] = "t.created_at <= :mdate_end";
        $monthlyParams['mdate_end'] = $dateEnd . ' 23:59:59';
    }
    $monthlyWhere[] = "YEAR(t.created_at) = :myear";
    $monthlyParams['myear'] = $chartYear;

    $monthlyQuery = "
        SELECT MONTH(t.created_at) as month_num, 
               SUM(t.amount) as total_amount, 
               SUM(t.tax_amount) as total_tax, 
               SUM(t.net_amount) as total_net 
        FROM transactions t
        WHERE " . implode(' AND ', $monthlyWhere) . "
        GROUP BY MONTH(t.created_at) ORDER BY month_num
    ";
    $monthlyStmt = $fastPDO->prepare($monthlyQuery);
    $monthlyStmt->execute($monthlyParams);
    $monthlyRaw = $monthlyStmt->fetchAll(PDO::FETCH_ASSOC);

    $monthlyData = array_fill(1, 12, ['amount' => 0.0, 'tax' => 0.0, 'net' => 0.0]);
    foreach ($monthlyRaw as $row) {
        $m = (int)$row['month_num'];
        if ($m >= 1 && $m <= 12) {
            $monthlyData[$m] = [
                'amount' => (float)$row['total_amount'],
                'tax'    => (float)$row['total_tax'],
                'net'    => (float)$row['total_net']
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'data' => [
            'total_transactions'    => $totalTransactions,
            'pending_approvals'     => $pendingApprovals,
            'approved_transactions' => $approvedTransactions,
            'rejected_transactions' => $rejectedTransactions,
            'returned_transactions' => $returnedTransactions,
            'total_disbursed'       => $totalDisbursed,
            'total_tax_deducted'    => $totalTaxDeducted,
            'recent_transactions'   => $recentTransactions,
            'monthly_data'          => $monthlyData,
            'chart_year'            => $chartYear
        ]
    ]);

} catch (PDOException $e) {
    error_log("Dashboard API query failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching dashboard data.',
        'debug'   => $e->getMessage()
    ]);
}
