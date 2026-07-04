<?php
/**
 * Create, update, or deactivate coverage categories.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../services/AuditLogService.php';
require_once __DIR__ . '/../../services/CoverageCategoryService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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

$adminId = (int)$_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'deactivate' || $action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Valid category id is required.']);
        exit;
    }

    $existing = CoverageCategoryService::getCategoryById($fastPDO, $id);
    if ($existing === null) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Category not found.']);
        exit;
    }

    try {
        if ($action === 'delete') {
            CoverageCategoryService::deleteCategory($fastPDO, $id);
            AuditLogService::log(
                $fastPDO,
                $adminId,
                'Deleted coverage category: ' . $existing['name'],
                $existing,
                null
            );
            echo json_encode(['success' => true, 'message' => 'Category deleted successfully.']);
        } else {
            CoverageCategoryService::deactivateCategory($fastPDO, $id);
            $updated = CoverageCategoryService::getCategoryById($fastPDO, $id);
            AuditLogService::log(
                $fastPDO,
                $adminId,
                'Deactivated coverage category: ' . $existing['name'],
                $existing,
                $updated
            );
            echo json_encode(['success' => true, 'message' => 'Category deactivated successfully.', 'data' => $updated]);
        }
    } catch (InvalidArgumentException $e) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } catch (Throwable $e) {
        error_log("manage-category $action failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => "Failed to $action category."]);
    }
    exit;
}

if ($action === 'save') {
    $payload = [
        'id' => $_POST['id'] ?? null,
        'transaction_type' => $_POST['transaction_type'] ?? '',
        'name' => $_POST['name'] ?? '',
        'display_label' => $_POST['display_label'] ?? '',
        'sort_order' => $_POST['sort_order'] ?? 0,
        'is_active' => $_POST['is_active'] ?? 0,
        'field_config' => [],
        'alias' => [
            'transaction_type' => $_POST['alias_transaction_type'] ?? null,
            'category_id' => $_POST['alias_category_id'] ?? null,
            'note' => $_POST['alias_note'] ?? null,
            'source_label' => $_POST['alias_source_label'] ?? null,
        ],
        'documents' => [],
    ];

    $txType = $payload['transaction_type'];
    $fieldKeys = CoverageCategoryService::getFieldKeysForType($txType);
    foreach ($fieldKeys as $key) {
        $payload['field_config'][$key] = isset($_POST['field_config'][$key]) && (int)$_POST['field_config'][$key] === 1;
    }

    $docTitles = $_POST['doc_title'] ?? [];
    $docRequired = $_POST['doc_required'] ?? [];
    $docConditions = $_POST['doc_condition'] ?? [];
    $docSections = $_POST['doc_section'] ?? [];
    $docModes = $_POST['doc_modes_of_travel'] ?? [];
    $docStages = $_POST['doc_stage'] ?? [];

    if (is_array($docTitles)) {
        foreach ($docTitles as $i => $title) {
            $title = trim((string)$title);
            if ($title === '') {
                continue;
            }
            
            $modes = null;
            if (!empty($docModes[$i])) {
                $decoded = json_decode($docModes[$i], true);
                if (is_array($decoded) && !empty($decoded)) {
                    $modes = $decoded;
                }
            }

            $payload['documents'][] = [
                'title' => $title,
                'is_required' => isset($docRequired[$i]) && (int)$docRequired[$i] === 1,
                'condition_text' => trim((string)($docConditions[$i] ?? '')),
                'section_title' => trim((string)($docSections[$i] ?? '')),
                'sort_order' => $i,
                'modes_of_travel' => $modes,
                'stage' => isset($docStages[$i]) && in_array($docStages[$i], ['submission', 'liquidation']) ? $docStages[$i] : 'submission',
            ];
        }
    }

    $existing = null;
    if (!empty($payload['id'])) {
        $existing = CoverageCategoryService::getCategoryById($fastPDO, (int)$payload['id']);
    }

    try {
        $id = CoverageCategoryService::saveCategory($fastPDO, $payload);
        $saved = CoverageCategoryService::getCategoryById($fastPDO, $id);
        AuditLogService::log(
            $fastPDO,
            $adminId,
            $existing ? 'Updated coverage category: ' . $saved['name'] : 'Created coverage category: ' . $saved['name'],
            $existing,
            $saved
        );
        echo json_encode([
            'success' => true,
            'message' => $existing ? 'Category updated successfully.' : 'Category created successfully.',
            'data' => $saved,
        ]);
    } catch (InvalidArgumentException $e) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    } catch (Throwable $e) {
        error_log('manage-category save failed: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save category.']);
    }
    exit;
}

http_response_code(422);
echo json_encode(['success' => false, 'message' => 'Invalid action. Use save or deactivate.']);
