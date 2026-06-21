<?php
/**
 * Helpers for DM 214 document checklists per transaction type and coverage category.
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../services/CoverageCategoryService.php';

function getDocumentChecklistConfig(): array
{
    global $fastPDO;
    static $config = null;

    if ($config === null) {
        if ($fastPDO === null) {
            $config = ['Cash Advance' => [], 'Reimbursement' => []];
        } else {
            try {
                $config = CoverageCategoryService::getChecklistConfig($fastPDO);
            } catch (Throwable $e) {
                error_log('Failed to load checklist config from DB: ' . $e->getMessage());
                $config = ['Cash Advance' => [], 'Reimbursement' => []];
            }
        }
    }

    return $config;
}

/**
 * Resolve checklist for a transaction type and coverage category, handling DM aliases.
 *
 * @return array{documents: array, sections: array, note: string|null, source_label: string|null}
 */
function resolveDocumentChecklist(string $transactionType, string $category): array
{
    global $fastPDO;

    $empty = ['documents' => [], 'sections' => [], 'note' => null, 'source_label' => null];

    if ($transactionType === '' || $category === '' || $fastPDO === null) {
        return $empty;
    }

    try {
        return CoverageCategoryService::resolveDocumentChecklist($fastPDO, $transactionType, $category);
    } catch (Throwable $e) {
        error_log('resolveDocumentChecklist failed: ' . $e->getMessage());
        return $empty;
    }
}

function getDocumentChecklistsForJs(): string
{
    global $fastPDO;

    if ($fastPDO === null) {
        return json_encode(['checklists' => [], 'aliases' => []], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    }

    try {
        $checklists = CoverageCategoryService::getChecklistConfig($fastPDO);
        $aliases = CoverageCategoryService::getAliasesForJs($fastPDO);
        return json_encode(['checklists' => $checklists, 'aliases' => $aliases], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    } catch (Throwable $e) {
        error_log('getDocumentChecklistsForJs failed: ' . $e->getMessage());
        return json_encode(['checklists' => [], 'aliases' => []], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    }
}

/**
 * Render checklist HTML for tracker/detail views.
 */
function renderDocumentChecklistHtml(string $transactionType, string $category, bool $compact = false): string
{
    $resolved = resolveDocumentChecklist($transactionType, $category);
    if (empty($resolved['documents']) && empty($resolved['sections'])) {
        return '';
    }

    $listClass = $compact ? 'list-group list-group-flush' : 'list-group list-group-flush border rounded-3 overflow-hidden';
    $html = '';

    if (!empty($resolved['note'])) {
        $html .= '<div class="alert alert-info border-0 py-2 px-3 mb-3 fs-9"><i class="bi bi-info-circle me-1"></i>' . htmlspecialchars($resolved['note']) . '</div>';
    }
    if (!empty($resolved['source_label'])) {
        $html .= '<p class="text-muted fs-9 mb-2"><i class="bi bi-arrow-return-right me-1"></i>' . htmlspecialchars($resolved['source_label']) . '</p>';
    }

    $html .= renderDocumentListGroup($resolved['documents'], $listClass);

    foreach ($resolved['sections'] as $section) {
        $html .= '<h6 class="fw-semibold text-secondary fs-8 mt-3 mb-2">' . htmlspecialchars($section['title'] ?? 'Additional Documents') . '</h6>';
        $html .= renderDocumentListGroup($section['documents'] ?? [], $listClass);
    }

    return $html;
}

function renderDocumentListGroup(array $documents, string $listClass): string
{
    if (empty($documents)) {
        return '';
    }

    $html = '<ul class="' . $listClass . ' mb-0">';
    foreach ($documents as $doc) {
        $required = !empty($doc['required']);
        $badgeClass = $required ? 'bg-danger-subtle text-danger' : 'bg-secondary-subtle text-secondary';
        $badgeText = $required ? 'Required' : 'Optional';
        $condition = $doc['condition'] ?? '';
        $title = htmlspecialchars($doc['title']);
        $conditionHtml = $condition !== '' ? ' <small class="text-muted">(' . htmlspecialchars($condition) . ')</small>' : '';

        $html .= '<li class="list-group-item d-flex justify-content-between align-items-start gap-2 py-2 px-3 fs-8">';
        $html .= '<span class="text-dark">' . $title . $conditionHtml . '</span>';
        $html .= '<span class="badge ' . $badgeClass . ' fs-9 flex-shrink-0">' . $badgeText . '</span>';
        $html .= '</li>';
    }
    $html .= '</ul>';

    return $html;
}
