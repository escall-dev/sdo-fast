<?php
/**
 * List coverage categories for System Settings admin UI.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../services/CoverageCategoryService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if ($fastPDO === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

if (!hasPermission('configure_system')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: configure_system permission required.']);
    exit;
}

try {
    $categories = CoverageCategoryService::getAllForAdmin($fastPDO);

    $aliasOptions = [];
    foreach ($categories as $cat) {
        $aliasOptions[] = [
            'id' => $cat['id'],
            'transaction_type' => $cat['transaction_type'],
            'name' => $cat['name'],
            'label' => $cat['display_label'] ?: $cat['name'],
        ];
    }

    echo json_encode([
        'success' => true,
        'categories' => $categories,
        'alias_options' => $aliasOptions,
    ]);
} catch (Throwable $e) {
    error_log('list-categories failed: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to load coverage categories.']);
}
