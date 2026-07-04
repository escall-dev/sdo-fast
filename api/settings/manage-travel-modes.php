<?php
/**
 * Travel Modes API
 * Handles fetching and updating mode of travel configurations.
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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt = $fastPDO->query("SELECT id, name, is_active FROM modes_of_travel ORDER BY id ASC");
        $modes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'modes' => $modes
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch travel modes.']);
    }
    exit;
}

if ($method === 'POST') {
    $userRole = $_SESSION['user_role'] ?? '';
    if ($userRole !== 'Super Admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden: Only Super Admin can modify travel modes.']);
        exit;
    }

    $names = $_POST['mode_name'] ?? [];
    $is_active = $_POST['is_active'] ?? [];

    if (!is_array($names) || empty($names)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'At least one travel mode must be provided.']);
        exit;
    }

    try {
        $fastPDO->beginTransaction();
        
        // Delete existing modes not in the submitted list
        $fastPDO->exec("DELETE FROM modes_of_travel");
        
        $insertStmt = $fastPDO->prepare("INSERT INTO modes_of_travel (name, is_active) VALUES (:name, :is_active)");
        
        foreach ($names as $i => $name) {
            $name = trim($name);
            if (empty($name)) continue;
            
            $active = isset($is_active[$i]) && $is_active[$i] == '1' ? 1 : 0;
            
            $insertStmt->execute([
                'name' => $name,
                'is_active' => $active
            ]);
        }
        
        AuditLogService::log(
            $fastPDO, 
            $_SESSION['user_id'],
            "Updated travel modes configuration",
            null,
            null
        );
        
        $fastPDO->commit();
        
        echo json_encode([
            'success' => true,
            'message' => 'Travel modes configuration saved successfully.'
        ]);
    } catch (PDOException $e) {
        if ($fastPDO->inTransaction()) {
            $fastPDO->rollBack();
        }
        error_log("Update travel modes error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error during travel modes update.']);
    }
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
