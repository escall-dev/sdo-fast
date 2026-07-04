<?php
/**
 * System Settings API
 * Handles fetching and updating global system settings.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../services/AuditLogService.php';

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// 1. Ensure the table exists and initialize default settings
try {
    $fastPDO->exec("
        CREATE TABLE IF NOT EXISTS system_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value VARCHAR(255) NOT NULL,
            description TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    $fastPDO->exec("
        INSERT IGNORE INTO system_settings (setting_key, setting_value, description) 
        VALUES ('enable_bir_number', '0', 'Enable BIR 2307 Number Field')
    ");
    $fastPDO->exec("
        INSERT IGNORE INTO system_settings (setting_key, setting_value, description) 
        VALUES ('enable_signatory_tracker', '1', 'Enable Approval & Signatures in Progress Tracker')
    ");
} catch (PDOException $e) {
    error_log("Failed to initialize system_settings table: " . $e->getMessage());
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $fastPDO->query("SELECT setting_key, setting_value, description FROM system_settings");
        $settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $settings[$row['setting_key']] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'settings' => $settings
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch settings.']);
    }
    exit;
}

if ($method === 'POST') {
    // Check permission for POST
    $userRole = $_SESSION['user_role'] ?? '';
    if ($userRole !== 'Super Admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden: Only Super Admin can update system settings.']);
        exit;
    }

    $inputData = defined('TEST_MODE') ? ($GLOBALS['TEST_INPUT'] ?? []) : json_decode(file_get_contents('php://input'), true);
    
    if (empty($inputData) && !empty($_POST)) {
        $inputData = $_POST;
    }

    if (!isset($inputData['settings']) || !is_array($inputData['settings'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid settings payload.']);
        exit;
    }

    try {
        $fastPDO->beginTransaction();
        
        $updateStmt = $fastPDO->prepare("UPDATE system_settings SET setting_value = :val WHERE setting_key = :key");
        
        $changes = [];
        foreach ($inputData['settings'] as $key => $value) {
            $updateStmt->execute([
                'val' => (string)$value,
                'key' => (string)$key
            ]);
            $changes[] = "$key => $value";
        }
        
        AuditLogService::log(
            $fastPDO, 
            $_SESSION['user_id'],
            "Updated system settings: " . implode(', ', $changes),
            [],
            $inputData['settings']
        );
        
        $fastPDO->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'System settings updated successfully.'
        ]);
    } catch (PDOException $e) {
        if ($fastPDO->inTransaction()) {
            $fastPDO->rollBack();
        }
        error_log("Update system settings error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error during settings update.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
