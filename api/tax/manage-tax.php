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

$userRole = $_SESSION['user_role'] ?? '';
$adminId = $_SESSION['user_id'];

if ($userRole !== 'Super Admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Only Super Admins can modify settings.']);
    exit;
}

$taxGoods = isset($_POST['tax_goods']) ? (float)$_POST['tax_goods'] : -1.0;
$taxFoods = isset($_POST['tax_foods']) ? (float)$_POST['tax_foods'] : -1.0;
$taxServices = isset($_POST['tax_services']) ? (float)$_POST['tax_services'] : -1.0;

if ($taxGoods < 0 || $taxFoods < 0 || $taxServices < 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Tax percentages must be non-negative values.']);
    exit;
}

try {
    // Fetch old values for audits
    $oldConfigs = $fastPDO->query("SELECT tax_type, tax_percentage FROM tax_configurations")->fetchAll(PDO::FETCH_KEY_PAIR);

    $fastPDO->beginTransaction();

    // 1. Update Goods
    $goodsStmt = $fastPDO->prepare("UPDATE tax_configurations SET tax_percentage = :percentage WHERE tax_type = 'Goods'");
    $goodsStmt->execute(['percentage' => $taxGoods]);

    // 2. Update Foods
    $foodsStmt = $fastPDO->prepare("UPDATE tax_configurations SET tax_percentage = :percentage WHERE tax_type = 'Foods'");
    $foodsStmt->execute(['percentage' => $taxFoods]);

    // 3. Update Services
    $servicesStmt = $fastPDO->prepare("UPDATE tax_configurations SET tax_percentage = :percentage WHERE tax_type = 'Services'");
    $servicesStmt->execute(['percentage' => $taxServices]);

    // 4. Log Auditing
    $newConfigs = [
        'Goods' => $taxGoods,
        'Foods' => $taxFoods,
        'Services' => $taxServices
    ];
    AuditLogService::log(
        $fastPDO, 
        $adminId, 
        "Updated system tax configurations", 
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
