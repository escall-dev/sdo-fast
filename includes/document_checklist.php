<?php
/**
 * Helpers for DM 214 document checklists per transaction type and coverage category.
 */

function getDocumentChecklistConfig(): array
{
    static $config = null;
    if ($config === null) {
        $config = require __DIR__ . '/../config/document_checklists.php';
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
    $empty = ['documents' => [], 'sections' => [], 'note' => null, 'source_label' => null];

    if ($transactionType === '' || $category === '') {
        return $empty;
    }

    $config = getDocumentChecklistConfig();
    $note = null;
    $sourceLabel = null;
    $lookupType = $transactionType;
    $lookupCategory = $category;

    if ($transactionType === 'Cash Advance' && $category === 'Accommodation') {
        $lookupCategory = 'Meals and Accommodation';
        $note = 'Per DM 214, Accommodation has no separate enclosure and falls under Meals and Accommodation.';
    } elseif ($transactionType === 'Reimbursement' && $category === 'Accommodation') {
        $lookupCategory = 'Meals and Accommodation';
        $lookupType = 'Cash Advance';
        $note = 'Per DM 214, Accommodation has no separate enclosure and falls under Meals and Accommodation.';
        $sourceLabel = 'Same as Cash Advance: Meals and Accommodation';
    } elseif ($transactionType === 'Reimbursement' && $category === 'Meals and Accommodation') {
        $lookupType = 'Cash Advance';
        $lookupCategory = 'Meals and Accommodation';
        $sourceLabel = 'Same as Cash Advance: Meals and Accommodation';
    } elseif ($transactionType === 'Reimbursement' && $category === 'Honorarium') {
        $lookupType = 'Cash Advance';
        $lookupCategory = 'Honorarium';
        $sourceLabel = 'Same as Cash Advance: Honorarium';
    }

    $entry = $config[$lookupType][$lookupCategory] ?? null;
    if ($entry === null) {
        return $empty;
    }

    return [
        'documents' => $entry['documents'] ?? [],
        'sections' => $entry['sections'] ?? [],
        'note' => $note,
        'source_label' => $sourceLabel,
    ];
}

function getDocumentChecklistsForJs(): string
{
    $config = getDocumentChecklistConfig();
    $aliases = [
        'Cash Advance' => [
            'Accommodation' => ['ref' => 'Cash Advance', 'category' => 'Meals and Accommodation', 'note' => 'Per DM 214, Accommodation has no separate enclosure and falls under Meals and Accommodation.'],
        ],
        'Reimbursement' => [
            'Accommodation' => ['ref' => 'Cash Advance', 'category' => 'Meals and Accommodation', 'note' => 'Per DM 214, Accommodation has no separate enclosure and falls under Meals and Accommodation.', 'source_label' => 'Same as Cash Advance: Meals and Accommodation'],
            'Meals and Accommodation' => ['ref' => 'Cash Advance', 'category' => 'Meals and Accommodation', 'source_label' => 'Same as Cash Advance: Meals and Accommodation'],
            'Honorarium' => ['ref' => 'Cash Advance', 'category' => 'Honorarium', 'source_label' => 'Same as Cash Advance: Honorarium'],
        ],
    ];

    return json_encode(['checklists' => $config, 'aliases' => $aliases], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
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
