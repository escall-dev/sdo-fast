<?php
/**
 * User Management Controller API for SDO FAST.
 * Restricts modifications to Super Admins only and logs mutations.
 * Supports positions with auto-role mapping.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth
require_once __DIR__ . '/../../services/AuditLogService.php';

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$userRole = $_SESSION['user_role'] ?? '';
$adminId = $_SESSION['user_id'];

// Restrict to users with manage_users permission
if (!hasPermission('manage_users')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: You do not have permissions to manage users.']);
    exit;
}
function getMappedPositionId($roleId, $positionText) {
    switch ((int)$roleId) {
        case 1: // Super Admin
            return 7;
        case 2: // Accounting Staff
            return 2; // Accountant
        case 3: // Budget Officer
            return 4; // Budget Officer
        case 4: // Approver
            if (stripos($positionText, 'SDS') !== false && stripos($positionText, 'ASDS') === false) {
                return 6; // SDS
            }
            return 5; // ASDS
        case 8: // Cashier
            return 8; // Cashier
        case 6: // Admin
            return 2; // Accountant
        case 5: // Requestor
        case 7: // User
        default:
            return 1; // Personnel
    }
}

$action = trim($_GET['action'] ?? '');

try {
    if ($action === 'list') {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? min(50, max(5, (int)$_GET['per_page'])) : 10;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $roleId = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        $office = isset($_GET['office']) ? trim($_GET['office']) : '';
        $unitSection = isset($_GET['unit_section']) ? trim($_GET['unit_section']) : '';

        $whereConds = [];
        $params = [];

        if (!empty($search)) {
            $whereConds[] = "(u.full_name LIKE :search OR u.email LIKE :search OR u.username LIKE :search OR u.employee_no LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($roleId > 0) {
            $whereConds[] = "ur.role_id = :role_id";
            $params['role_id'] = $roleId;
        }

        if (!empty($status)) {
            $whereConds[] = "u.status = :status";
            $params['status'] = $status;
        }

        if (!empty($office)) {
            $whereConds[] = "u.office = :office";
            $params['office'] = $office;
        }

        if (!empty($unitSection)) {
            $whereConds[] = "u.unit_section = :unit_section";
            $params['unit_section'] = $unitSection;
        }

        $whereSql = '';
        if (!empty($whereConds)) {
            $whereSql = " WHERE " . implode(" AND ", $whereConds);
        }

        // Count
        $countSql = "
            SELECT COUNT(DISTINCT u.id) 
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            {$whereSql}
        ";
        $countStmt = $fastPDO->prepare($countSql);
        $countStmt->execute($params);
        $totalCount = (int)$countStmt->fetchColumn();

        // Data
        $offset = ($page - 1) * $perPage;
        $dataSql = "
            SELECT u.id, u.uuid, u.full_name, u.email, u.username, u.position_id, u.status, u.created_at,
                   u.office, u.unit_section, u.employee_no, u.position,
                   r.id as role_id, r.role_name,
                   p.position_name,
                   (SELECT MAX(login_at) FROM login_logs WHERE user_id = u.id) as last_login
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            LEFT JOIN positions p ON u.position_id = p.id
            {$whereSql}
            ORDER BY u.created_at DESC
            LIMIT " . (int)$perPage . " OFFSET " . (int)$offset;

        $stmt = $fastPDO->prepare($dataSql);
        $stmt->execute($params);
        $users = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => [
                'users' => $users,
                'total_count' => $totalCount,
                'page' => $page,
                'per_page' => $perPage
            ]
        ]);
        exit;

    } elseif ($action === 'create') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit;
        }

        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 0);
        $office = trim($_POST['office'] ?? '');
        $unitSection = trim($_POST['unit_section'] ?? '');
        $employeeNo = trim($_POST['employee_no'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $status = isset($_POST['is_active']) && $_POST['is_active'] == '1' ? 'active' : 'inactive';

        if (empty($name) || empty($email) || empty($password) || $roleId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Full name, email, password, and role selection are required.']);
            exit;
        }

        if (strlen($password) < 8) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
            exit;
        }

        if ($password !== $confirmPassword) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Password and Confirm Password do not match.']);
            exit;
        }

        // Validate duplicates
        $checkStmt = $fastPDO->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $checkStmt->execute(['email' => $email]);
        if ($checkStmt->fetchColumn() > 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Email address is already registered.']);
            exit;
        }

        if (!empty($employeeNo)) {
            $checkEmpStmt = $fastPDO->prepare("SELECT COUNT(*) FROM users WHERE employee_no = :employee_no");
            $checkEmpStmt->execute(['employee_no' => $employeeNo]);
            if ($checkEmpStmt->fetchColumn() > 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Employee number is already registered.']);
                exit;
            }
        }

        // Auto-generate username from email prefix
        $baseUsername = explode('@', $email)[0];
        $username = $baseUsername;
        $counter = 1;
        while (true) {
            $stmt = $fastPDO->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            if (!$stmt->fetch()) {
                break;
            }
            $username = $baseUsername . $counter;
            $counter++;
        }

        $positionId = getMappedPositionId($roleId, $position);

        $fastPDO->beginTransaction();
        
        $uuid = bin2hex(random_bytes(16));
        $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
        $hashPass = password_hash($password, PASSWORD_DEFAULT);

        // Insert User with all details
        $stmt = $fastPDO->prepare("
            INSERT INTO users (uuid, full_name, email, username, password, office, unit_section, employee_no, position, position_id, status) 
            VALUES (:uuid, :full_name, :email, :username, :password, :office, :unit_section, :employee_no, :position, :position_id, :status)
        ");
        $stmt->execute([
            'uuid' => $uuid,
            'full_name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => $hashPass,
            'office' => $office ?: null,
            'unit_section' => $unitSection ?: null,
            'employee_no' => $employeeNo ?: null,
            'position' => $position ?: null,
            'position_id' => $positionId,
            'status' => $status
        ]);
        $newUserId = $fastPDO->lastInsertId();

        // Assign Role
        $roleInsertStmt = $fastPDO->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
        $roleInsertStmt->execute(['user_id' => $newUserId, 'role_id' => $roleId]);

        // Audit Log
        AuditLogService::log($fastPDO, $adminId, "Created user account: {$username} ({$email})", null, [
            'full_name' => $name, 
            'office' => $office,
            'unit_section' => $unitSection,
            'employee_no' => $employeeNo,
            'position' => $position,
            'status' => $status
        ]);

        $fastPDO->commit();
        echo json_encode(['success' => true, 'message' => "User account for '{$name}' created successfully."]);
        exit;

    } elseif ($action === 'update') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $roleId = (int)($_POST['role_id'] ?? 0);
        $office = trim($_POST['office'] ?? '');
        $unitSection = trim($_POST['unit_section'] ?? '');
        $employeeNo = trim($_POST['employee_no'] ?? '');
        $position = trim($_POST['position'] ?? '');
        $status = isset($_POST['is_active']) && $_POST['is_active'] == '1' ? 'active' : 'inactive';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($userId <= 0 || empty($name) || empty($email) || $roleId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Full name, email, and role selection are required.']);
            exit;
        }

        if ($password !== '') {
            if ($userRole !== 'Super Admin') {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Forbidden: Only the Super Admin is allowed to change user passwords.']);
                exit;
            }
            if (strlen($password) < 8) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
                exit;
            }
            if ($password !== $confirmPassword) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Password and Confirm Password do not match.']);
                exit;
            }
        }

        // Fetch current user row for logs
        $currStmt = $fastPDO->prepare("
            SELECT u.*, ur.role_id, r.role_name as old_role
            FROM users u 
            LEFT JOIN user_roles ur ON u.id = ur.user_id 
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.id = :id LIMIT 1
        ");
        $currStmt->execute(['id' => $userId]);
        $oldUser = $currStmt->fetch();

        if (!$oldUser) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User record not found.']);
            exit;
        }

        // Validate duplicates
        $checkStmt = $fastPDO->prepare("SELECT COUNT(*) FROM users WHERE email = :email AND id != :id");
        $checkStmt->execute(['email' => $email, 'id' => $userId]);
        if ($checkStmt->fetchColumn() > 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Email already used by another account.']);
            exit;
        }

        if (!empty($employeeNo)) {
            $checkEmpStmt = $fastPDO->prepare("SELECT COUNT(*) FROM users WHERE employee_no = :employee_no AND id != :id");
            $checkEmpStmt->execute(['employee_no' => $employeeNo, 'id' => $userId]);
            if ($checkEmpStmt->fetchColumn() > 0) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Employee number already used by another account.']);
                exit;
            }
        }

        $positionId = getMappedPositionId($roleId, $position);

        $fastPDO->beginTransaction();

        // Update profile
        $updateQuery = "
            UPDATE users 
            SET full_name = :full_name, email = :email, office = :office, unit_section = :unit_section, 
                employee_no = :employee_no, position = :position, position_id = :position_id, status = :status
        ";
        $updateParams = [
            'full_name' => $name,
            'email' => $email,
            'office' => $office ?: null,
            'unit_section' => $unitSection ?: null,
            'employee_no' => $employeeNo ?: null,
            'position' => $position ?: null,
            'position_id' => $positionId,
            'status' => $status,
            'id' => $userId
        ];

        if ($password !== '') {
            $updateQuery .= ", password = :password";
            $updateParams['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $updateQuery .= " WHERE id = :id";

        $updateStmt = $fastPDO->prepare($updateQuery);
        $updateStmt->execute($updateParams);

        // Update Role based on selection/mapping
        if ($roleId > 0) {
            // Delete old role assignments to avoid duplicate roles
            $roleDeleteStmt = $fastPDO->prepare("DELETE FROM user_roles WHERE user_id = :user_id");
            $roleDeleteStmt->execute(['user_id' => $userId]);

            $roleInsertStmt = $fastPDO->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (:user_id, :role_id)");
            $roleInsertStmt->execute(['user_id' => $userId, 'role_id' => $roleId]);
        }

        // Get new role name for log
        $newRoleName = $fastPDO->query("SELECT role_name FROM roles WHERE id = {$roleId}")->fetchColumn();

        // Audit Log
        $auditMessage = "Updated profile details and role for user: {$oldUser['username']}";
        $auditNew = [
            'full_name' => $name, 
            'email' => $email, 
            'office' => $office,
            'unit_section' => $unitSection,
            'employee_no' => $employeeNo,
            'position' => $position,
            'role' => $newRoleName,
            'status' => $status
        ];

        if ($password !== '') {
            $auditMessage = "Updated profile details, password, and role for user: {$oldUser['username']}";
            $auditNew['password_changed'] = true;
        }

        AuditLogService::log(
            $fastPDO, 
            $adminId, 
            $auditMessage, 
            [
                'full_name' => $oldUser['full_name'], 
                'email' => $oldUser['email'], 
                'office' => $oldUser['office'] ?? 'N/A', 
                'unit_section' => $oldUser['unit_section'] ?? 'N/A',
                'employee_no' => $oldUser['employee_no'] ?? 'N/A',
                'position' => $oldUser['position'] ?? 'N/A',
                'role' => $oldUser['old_role'] ?? 'N/A',
                'status' => $oldUser['status']
            ],
            $auditNew
        );

        $fastPDO->commit();
        echo json_encode(['success' => true, 'message' => 'User profile updated successfully.']);
        exit;

    } elseif ($action === 'status') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit;
        }

        $targetUserId = (int)($_POST['user_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        if ($targetUserId <= 0 || !in_array($status, ['active', 'inactive'])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid status toggle parameters.']);
            exit;
        }

        if ($targetUserId === $adminId) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Conflict: You cannot deactivate your own admin session account.']);
            exit;
        }

        // Fetch old state
        $oldStatus = $fastPDO->query("SELECT status FROM users WHERE id = {$targetUserId}")->fetchColumn();

        $stmt = $fastPDO->prepare("UPDATE users SET status = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $targetUserId]);

        // Audit
        AuditLogService::log(
            $fastPDO, 
            $adminId, 
            "Changed account status: User #{$targetUserId}", 
            ['status' => $oldStatus], 
            ['status' => $status]
        );

        echo json_encode(['success' => true, 'message' => "Account status changed to '{$status}'."]);
        exit;

    } elseif ($action === 'reset') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit;
        }

        $targetUserId = (int)($_POST['user_id'] ?? 0);

        // Fetch user email
        $userRow = $fastPDO->query("SELECT email, full_name FROM users WHERE id = {$targetUserId} LIMIT 1")->fetch();
        if (!$userRow) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User not found.']);
            exit;
        }

        // Generate Token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+60 minutes'));

        $fastPDO->beginTransaction();
        
        // Invalidate old tokens
        $fastPDO->exec("UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = {$targetUserId} AND used_at IS NULL");
        
        // Insert new token
        $stmt = $fastPDO->prepare("INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
        $stmt->execute(['user_id' => $targetUserId, 'token' => $token, 'expires_at' => $expiresAt]);

        // Log audit
        AuditLogService::log($fastPDO, $adminId, "Triggered password reset link for user: {$userRow['email']}", null, ['expires_at' => $expiresAt]);

        $fastPDO->commit();

        // Return the reset link directly to the admin so they can provide it or test it
        $resetLink = env('APP_URL') . "/views/reset-password.php?token=" . $token;
        echo json_encode([
            'success' => true,
            'message' => "Password reset link generated successfully for {$userRow['full_name']}.",
            'data' => [
                'reset_link' => $resetLink
            ]
        ]);
        exit;

    } elseif ($action === 'history') {
        $targetUserId = (int)($_GET['user_id'] ?? 0);
        if ($targetUserId <= 0) {
            http_response_code(422); exit;
        }

        // Query login logs
        $stmt = $fastPDO->prepare("
            SELECT * FROM login_logs 
            WHERE user_id = :user_id 
            ORDER BY login_at DESC 
            LIMIT 10
        ");
        $stmt->execute(['user_id' => $targetUserId]);
        $logs = $stmt->fetchAll();

        echo json_encode(['success' => true, 'data' => $logs]);
        exit;

    } elseif ($action === 'list_positions') {
        // Return all available positions for dropdown population
        $stmt = $fastPDO->query("SELECT p.*, (SELECT COUNT(*) FROM users u WHERE u.position_id = p.id) as user_count FROM positions p ORDER BY p.id ASC");
        $positions = $stmt->fetchAll();

        // Also fetch roles!
        $rolesStmt = $fastPDO->query("SELECT id, role_name FROM roles ORDER BY id ASC");
        $roles = $rolesStmt->fetchAll();

        echo json_encode([
            'success' => true, 
            'data' => [
                'positions' => $positions,
                'roles' => $roles
            ]
        ]);
        exit;

    } elseif ($action === 'add_position') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit;
        }

        $positionName = trim($_POST['position_name'] ?? '');
        $mappedRole = trim($_POST['mapped_role'] ?? '');

        // Fetch valid roles from DB dynamically
        $validRoles = $fastPDO->query("SELECT role_name FROM roles")->fetchAll(PDO::FETCH_COLUMN);

        if (empty($positionName) || !in_array($mappedRole, $validRoles)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Position name and a valid system role are required.']);
            exit;
        }

        // Check duplicate
        $checkStmt = $fastPDO->prepare("SELECT COUNT(*) FROM positions WHERE position_name = :name");
        $checkStmt->execute(['name' => $positionName]);
        if ($checkStmt->fetchColumn() > 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => "Position '{$positionName}' already exists."]);
            exit;
        }

        $stmt = $fastPDO->prepare("INSERT INTO positions (position_name, mapped_role, is_default) VALUES (:name, :role, 0)");
        $stmt->execute(['name' => $positionName, 'role' => $mappedRole]);

        AuditLogService::log($fastPDO, $adminId, "Added new position: {$positionName} (mapped to {$mappedRole})", null, [
            'position_name' => $positionName,
            'mapped_role' => $mappedRole
        ]);

        echo json_encode(['success' => true, 'message' => "Position '{$positionName}' added successfully."]);
        exit;

    } elseif ($action === 'delete_position') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit;
        }

        $positionId = (int)($_POST['position_id'] ?? 0);
        if ($positionId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid position ID.']);
            exit;
        }

        // Fetch position info
        $posStmt = $fastPDO->prepare("SELECT * FROM positions WHERE id = :id LIMIT 1");
        $posStmt->execute(['id' => $positionId]);
        $pos = $posStmt->fetch();

        if (!$pos) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Position not found.']);
            exit;
        }

        // Don't allow deleting default positions
        if ((int)$pos['is_default'] === 1) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Cannot delete a default system position.']);
            exit;
        }

        // Check if any users are assigned
        $userCountStmt = $fastPDO->prepare("SELECT COUNT(*) FROM users WHERE position_id = :id");
        $userCountStmt->execute(['id' => $positionId]);
        if ($userCountStmt->fetchColumn() > 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Cannot delete position: Users are still assigned to it. Reassign them first.']);
            exit;
        }

        $fastPDO->prepare("DELETE FROM positions WHERE id = :id")->execute(['id' => $positionId]);

        AuditLogService::log($fastPDO, $adminId, "Deleted position: {$pos['position_name']}", ['position_name' => $pos['position_name']], null);

        echo json_encode(['success' => true, 'message' => "Position '{$pos['position_name']}' deleted successfully."]);
        exit;

    } elseif ($action === 'delete') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); exit;
        }

        $targetUserId = (int)($_POST['user_id'] ?? 0);

        if ($targetUserId <= 0) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
            exit;
        }

        if ($targetUserId === $adminId) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Conflict: You cannot delete your own admin session account.']);
            exit;
        }

        // Fetch user info for logs
        $currStmt = $fastPDO->prepare("SELECT username, email, full_name FROM users WHERE id = :id LIMIT 1");
        $currStmt->execute(['id' => $targetUserId]);
        $userRow = $currStmt->fetch();

        if (!$userRow) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'User account not found.']);
            exit;
        }

        $fastPDO->beginTransaction();

        // Check if user has transactions (RESTRICT constraint)
        $txCountStmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE requestor_id = :id");
        $txCountStmt->execute(['id' => $targetUserId]);
        if ($txCountStmt->fetchColumn() > 0) {
            $fastPDO->rollBack();
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Cannot delete user: They are requestors for existing transaction records. Consider deactivating/suspending their account instead.']);
            exit;
        }

        // Check if user has status change logs (RESTRICT constraint)
        $logCountStmt = $fastPDO->prepare("SELECT COUNT(*) FROM transaction_status_logs WHERE changed_by = :id");
        $logCountStmt->execute(['id' => $targetUserId]);
        if ($logCountStmt->fetchColumn() > 0) {
            $fastPDO->rollBack();
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Cannot delete user: They have action logs (approvals/status changes) on transaction records. Consider suspending their account instead.']);
            exit;
        }

        // Delete user (dependencies user_roles, password_reset_tokens, login_logs cascade delete)
        $fastPDO->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $targetUserId]);

        // Log audit
        AuditLogService::log($fastPDO, $adminId, "Deleted user account: {$userRow['username']} ({$userRow['email']})", ['full_name' => $userRow['full_name']], null);

        $fastPDO->commit();
        echo json_encode(['success' => true, 'message' => "User account for '{$userRow['full_name']}' deleted successfully."]);
        exit;

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action parameter.']);
        exit;
    }
} catch (Exception $e) {
    if (isset($fastPDO) && $fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("User management controller error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'System error during user management.']);
}
