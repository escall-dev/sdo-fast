<?php
/**
 * Dynamic Tax Computation Engine API for SDO FAST.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php'; // Enforces auth & CSRF protection

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed.'
    ]);
    exit;
}

$amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0.00;
$taxType = isset($_POST['tax_type']) ? trim($_POST['tax_type']) : '';

if ($amount <= 0 || empty($taxType)) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'message' => 'Gross amount and tax type are required.'
    ]);
    exit;
}

try {
    // Query tax percentage dynamically from tax_configurations
    $stmt = $fastPDO->prepare("
        SELECT tax_percentage 
        FROM tax_configurations 
        WHERE tax_type = :tax_type AND is_active = 1 
        LIMIT 1
    ");
    $stmt->execute(['tax_type' => $taxType]);
    $taxConfig = $stmt->fetch();

    if (!$taxConfig) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => "Invalid or inactive tax type selected: '{$taxType}'."
        ]);
        exit;
    }

    $percentage = (float)$taxConfig['tax_percentage'];
    
    // Tax computation logic
    $taxAmount = $amount * ($percentage / 100);
    $netAmount = $amount - $taxAmount;

    echo json_encode([
        'success' => true,
        'message' => 'Tax computed successfully.',
        'data' => [
            'tax_amount' => round($taxAmount, 2),
            'net_amount' => round($netAmount, 2),
            'tax_percentage' => $percentage
        ]
    ]);

} catch (PDOException $e) {
    error_log("Tax engine failure: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An unexpected database error occurred during tax calculation.'
    ]);
}
