<?php
/**
 * Login Logs Controller API for SDO FAST.
 * Retrieves paginated login history with user details.
 * Restricted to users with configure_system permission (Super Admin).
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Restrict to users with configure_system permission
if (!hasPermission('configure_system')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: You do not have permission to view login logs.']);
    exit;
}

$action = trim($_GET['action'] ?? '');

try {
    if ($action === 'list') {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? min(100, max(5, (int)$_GET['per_page'])) : 20;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
        $dateFrom = isset($_GET['date_from']) ? trim($_GET['date_from']) : '';
        $dateTo = isset($_GET['date_to']) ? trim($_GET['date_to']) : '';

        $whereConds = [];
        $params = [];

        if (!empty($search)) {
            $whereConds[] = "(u.full_name LIKE :search OR u.email LIKE :search OR u.username LIKE :search OR ll.ip_address LIKE :search OR ll.device_info LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($userId > 0) {
            $whereConds[] = "ll.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        if (!empty($dateFrom)) {
            $whereConds[] = "ll.login_at >= :date_from";
            $params['date_from'] = $dateFrom . ' 00:00:00';
        }

        if (!empty($dateTo)) {
            $whereConds[] = "ll.login_at <= :date_to";
            $params['date_to'] = $dateTo . ' 23:59:59';
        }

        $whereSql = '';
        if (!empty($whereConds)) {
            $whereSql = " WHERE " . implode(" AND ", $whereConds);
        }

        // Count total
        $countSql = "
            SELECT COUNT(*) 
            FROM login_logs ll
            LEFT JOIN users u ON ll.user_id = u.id
            {$whereSql}
        ";
        $countStmt = $fastPDO->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = (int)$countStmt->fetchColumn();

        // Fetch paginated data
        $offset = ($page - 1) * $perPage;
        $dataSql = "
            SELECT ll.id, ll.user_id, ll.ip_address, ll.device_info, ll.login_at,
                   u.full_name, u.email, u.username, u.office
            FROM login_logs ll
            LEFT JOIN users u ON ll.user_id = u.id
            {$whereSql}
            ORDER BY ll.login_at DESC
            LIMIT :limit OFFSET :offset
        ";
        $dataStmt = $fastPDO->prepare($dataSql);
        foreach ($params as $key => $val) {
            $dataStmt->bindValue(":{$key}", $val);
        }
        $dataStmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $dataStmt->execute();
        $logs = $dataStmt->fetchAll();

        // Format login_at to Manila timezone
        $manilaTz = new DateTimeZone('Asia/Manila');
        foreach ($logs as &$log) {
            if (!empty($log['login_at'])) {
                $dt = new DateTime($log['login_at'], $manilaTz);
                $log['login_at'] = $dt->format('Y-m-d h:i A');
            }
        }
        unset($log);

        // Get unique users for filter dropdown
        $usersStmt = $fastPDO->query("
            SELECT DISTINCT u.id, u.full_name, u.email
            FROM login_logs ll
            JOIN users u ON ll.user_id = u.id
            ORDER BY u.full_name ASC
        ");
        $users = $usersStmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => $logs,
            'users' => $users,
            'total' => $totalCount,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($totalCount / $perPage)
        ]);
        exit;
    }

    // Invalid action
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    exit;

} catch (PDOException $e) {
    error_log("Login logs error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred while fetching login logs.']);
    exit;
}
