<?php
/**
 * Tax Settings Modification API for SDO FAST.
 * Restricts updates to Super Admin and audits mutations.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth
require_once __DIR__ . '/../../services/AuditLogService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$adminId = $_SESSION['user_id'];

if (!hasPermission('configure_system')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only users with configure_system permission can modify settings.']);
    exit;
}

$taxTypes = $_POST['tax_type'] ?? [];
$taxPercentages = $_POST['tax_percentage'] ?? [];
$isActiveFlags = $_POST['is_active'] ?? [];

if (!is_array($taxTypes) || !is_array($taxPercentages) || !is_array($isActiveFlags)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid tax configuration payload.']);
    exit;
}

try {
    $rowCount = count($taxTypes);
    if ($rowCount === 0 || $rowCount !== count($taxPercentages) || $rowCount !== count($isActiveFlags)) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Incomplete tax rows detected.']);
        exit;
    }

    $normalizedRows = [];
    $seenTypes = [];
    for ($i = 0; $i < $rowCount; $i++) {
        $taxType = trim((string)$taxTypes[$i]);
        $percentageRaw = $taxPercentages[$i];
        $isActive = ((int)$isActiveFlags[$i]) === 1 ? 1 : 0;

        if ($taxType === '' || strlen($taxType) > 50) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Each tax label is required and must be at most 50 characters.']);
            exit;
        }

        $normalizedKey = strtolower($taxType);
        if (isset($seenTypes[$normalizedKey])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Tax labels must be unique.']);
            exit;
        }
        $seenTypes[$normalizedKey] = true;

        if (!is_numeric($percentageRaw)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Tax percentage must be numeric.']);
            exit;
        }
        $percentage = (float)$percentageRaw;
        if ($percentage < 0 || $percentage > 100) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Tax percentage must be between 0 and 100.']);
            exit;
        }

        $normalizedRows[] = [
            'tax_type' => $taxType,
            'tax_percentage' => $percentage,
            'is_active' => $isActive
        ];
    }

    $oldConfigs = $fastPDO->query("SELECT tax_type, tax_percentage, is_active FROM tax_configurations ORDER BY tax_type ASC")->fetchAll(PDO::FETCH_ASSOC);

    $fastPDO->beginTransaction();

    // Reset all rows inactive first, then upsert provided rows.
    $fastPDO->exec("UPDATE tax_configurations SET is_active = 0");
    $upsertStmt = $fastPDO->prepare("
        INSERT INTO tax_configurations (tax_type, tax_percentage, is_active)
        VALUES (:tax_type, :tax_percentage, :is_active)
        ON DUPLICATE KEY UPDATE
            tax_percentage = VALUES(tax_percentage),
            is_active = VALUES(is_active)
    ");
    foreach ($normalizedRows as $row) {
        $upsertStmt->execute($row);
    }

    $newConfigs = $fastPDO->query("SELECT tax_type, tax_percentage, is_active FROM tax_configurations ORDER BY tax_type ASC")->fetchAll(PDO::FETCH_ASSOC);
    AuditLogService::log(
        $fastPDO, 
        $adminId, 
        "Updated dynamic system tax configurations", 
        $oldConfigs, 
        $newConfigs
    );

    $fastPDO->commit();
    echo json_encode(['success' => true, 'message' => 'Tax configurations updated successfully.']);

} catch (PDOException $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    error_log("Tax settings update failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred while updating settings.']);
}
