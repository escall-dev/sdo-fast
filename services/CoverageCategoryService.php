<?php
/**
 * Coverage category data access for Cash Advance and Reimbursement types.
 */

class CoverageCategoryService
{
    private const CA_FIELD_KEYS = ['dateVenue', 'fundSource', 'taItinerary', 'activityProposal', 'month'];
    private const REIMB_FIELD_KEYS = ['dateVenue', 'taItinerary', 'activityProposal', 'communications', 'utilityBills'];

    public static function getFieldKeysForType(string $transactionType): array
    {
        return $transactionType === 'Cash Advance' ? self::CA_FIELD_KEYS : self::REIMB_FIELD_KEYS;
    }

    public static function defaultFieldConfig(string $transactionType): array
    {
        $config = [];
        foreach (self::getFieldKeysForType($transactionType) as $key) {
            $config[$key] = false;
        }
        return $config;
    }

    public static function normalizeFieldConfig(string $transactionType, array $input): array
    {
        $defaults = self::defaultFieldConfig($transactionType);
        foreach ($defaults as $key => $value) {
            $defaults[$key] = !empty($input[$key]);
        }
        return $defaults;
    }

    public static function decodeFieldConfig(?string $json, string $transactionType): array
    {
        if ($json === null || $json === '') {
            return self::defaultFieldConfig($transactionType);
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return self::defaultFieldConfig($transactionType);
        }
        return self::normalizeFieldConfig($transactionType, $decoded);
    }

    private static function mapCategoryRow(array $row, array $documents = []): array
    {
        $txType = $row['transaction_type'];
        return [
            'id' => (int)$row['id'],
            'transaction_type' => $txType,
            'name' => $row['name'],
            'display_label' => $row['display_label'],
            'sort_order' => (int)$row['sort_order'],
            'is_active' => (int)$row['is_active'],
            'field_config' => self::decodeFieldConfig($row['field_config'] ?? null, $txType),
            'alias_transaction_type' => $row['alias_transaction_type'],
            'alias_category_id' => $row['alias_category_id'] !== null ? (int)$row['alias_category_id'] : null,
            'alias_note' => $row['alias_note'],
            'alias_source_label' => $row['alias_source_label'],
            'documents' => $documents,
        ];
    }

    public static function getActiveCategories(PDO $pdo, string $transactionType): array
    {
        $stmt = $pdo->prepare("
            SELECT id, transaction_type, name, display_label, sort_order, is_active, field_config,
                   alias_transaction_type, alias_category_id, alias_note, alias_source_label
            FROM coverage_categories
            WHERE transaction_type = :tx_type AND is_active = 1
            ORDER BY sort_order ASC, name ASC
        ");
        $stmt->execute(['tx_type' => $transactionType]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => self::mapCategoryRow($row), $rows);
    }

    public static function getCategoryByName(PDO $pdo, string $transactionType, string $name, bool $activeOnly = false): ?array
    {
        $sql = "
            SELECT id, transaction_type, name, display_label, sort_order, is_active, field_config,
                   alias_transaction_type, alias_category_id, alias_note, alias_source_label
            FROM coverage_categories
            WHERE transaction_type = :tx_type AND name = :name
        ";
        if ($activeOnly) {
            $sql .= " AND is_active = 1";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['tx_type' => $transactionType, 'name' => $name]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        return self::mapCategoryRow($row);
    }

    public static function getCategoryById(PDO $pdo, int $id): ?array
    {
        $stmt = $pdo->prepare("
            SELECT id, transaction_type, name, display_label, sort_order, is_active, field_config,
                   alias_transaction_type, alias_category_id, alias_note, alias_source_label
            FROM coverage_categories
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        $docs = self::getDocumentsForCategory($pdo, $id);
        return self::mapCategoryRow($row, $docs);
    }

    public static function getDocumentsForCategory(PDO $pdo, int $categoryId, ?string $stage = 'submission'): array
    {
        $sql = "
            SELECT id, section_title, sort_order, title, is_required, condition_text, modes_of_travel, stage
            FROM coverage_category_documents
            WHERE category_id = :category_id";
        
        $params = ['category_id' => $categoryId];
        if ($stage !== null) {
            $sql .= " AND stage = :stage";
            $params['stage'] = $stage;
        }
        $sql .= " ORDER BY COALESCE(section_title, ''), sort_order ASC, id ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($row) {
            $modes = null;
            if (!empty($row['modes_of_travel'])) {
                $decoded = json_decode($row['modes_of_travel'], true);
                if (is_array($decoded)) {
                    $modes = $decoded;
                } else {
                    // Fallback for legacy data that was stored as raw strings
                    $modes = [$row['modes_of_travel']];
                }
            }
            return [
                'id' => (int)$row['id'],
                'section_title' => $row['section_title'],
                'sort_order' => (int)$row['sort_order'],
                'title' => $row['title'],
                'is_required' => (int)$row['is_required'],
                'condition_text' => $row['condition_text'],
                'modes_of_travel' => $modes,
                'stage' => $row['stage'],
            ];
        }, $rows);
    }

    public static function getAllForAdmin(PDO $pdo): array
    {
        $stmt = $pdo->query("
            SELECT id, transaction_type, name, display_label, sort_order, is_active, field_config,
                   alias_transaction_type, alias_category_id, alias_note, alias_source_label
            FROM coverage_categories
            ORDER BY transaction_type ASC, sort_order ASC, name ASC
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $row) {
            $docs = self::getDocumentsForCategory($pdo, (int)$row['id'], null);
            $result[] = self::mapCategoryRow($row, $docs);
        }
        return $result;
    }

    public static function getFieldMapsForJs(PDO $pdo): array
    {
        $caMap = [];
        $reimbMap = [];

        foreach (self::getActiveCategories($pdo, 'Cash Advance') as $cat) {
            $caMap[$cat['name']] = $cat['field_config'];
        }
        foreach (self::getActiveCategories($pdo, 'Reimbursement') as $cat) {
            $reimbMap[$cat['name']] = $cat['field_config'];
        }

        return ['caFieldMap' => $caMap, 'reimbFieldMap' => $reimbMap];
    }

    public static function getChecklistConfig(PDO $pdo, string $stage = 'submission'): array
    {
        $config = ['Cash Advance' => [], 'Reimbursement' => []];

        $stmt = $pdo->query("
            SELECT id, transaction_type, name
            FROM coverage_categories
            ORDER BY transaction_type ASC, sort_order ASC, name ASC
        ");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categories as $cat) {
            $docs = self::getDocumentsForCategory($pdo, (int)$cat['id'], $stage);
            $mainDocs = [];
            $sections = [];

            foreach ($docs as $doc) {
                $item = [
                    'title' => $doc['title'],
                    'required' => $doc['is_required'] === 1,
                ];
                if (!empty($doc['condition_text'])) {
                    $item['condition'] = $doc['condition_text'];
                }
                if (!empty($doc['modes_of_travel'])) {
                    $item['modesOfTravel'] = $doc['modes_of_travel'];
                }

                if ($doc['section_title'] === null || $doc['section_title'] === '') {
                    $mainDocs[] = $item;
                } else {
                    $sectionTitle = $doc['section_title'];
                    if (!isset($sections[$sectionTitle])) {
                        $sections[$sectionTitle] = ['title' => $sectionTitle, 'documents' => []];
                    }
                    $sections[$sectionTitle]['documents'][] = $item;
                }
            }

            $entry = ['documents' => $mainDocs];
            if (!empty($sections)) {
                $entry['sections'] = array_values($sections);
            }
            $config[$cat['transaction_type']][$cat['name']] = $entry;
        }

        return $config;
    }

    public static function getAliasesForJs(PDO $pdo): array
    {
        $aliases = ['Cash Advance' => [], 'Reimbursement' => []];

        $stmt = $pdo->query("
            SELECT c.transaction_type, c.name, c.alias_transaction_type, c.alias_category_id,
                   c.alias_note, c.alias_source_label, a.name AS alias_name
            FROM coverage_categories c
            LEFT JOIN coverage_categories a ON c.alias_category_id = a.id
            WHERE c.alias_category_id IS NOT NULL
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $alias = [
                'ref' => $row['alias_transaction_type'],
                'category' => $row['alias_name'],
            ];
            if (!empty($row['alias_note'])) {
                $alias['note'] = $row['alias_note'];
            }
            if (!empty($row['alias_source_label'])) {
                $alias['source_label'] = $row['alias_source_label'];
            }
            $aliases[$row['transaction_type']][$row['name']] = $alias;
        }

        return $aliases;
    }

    public static function isCategoryInUse(PDO $pdo, int $categoryId): bool
    {
        $cat = self::getCategoryById($pdo, $categoryId);
        if ($cat === null) {
            return false;
        }

        if ($cat['transaction_type'] === 'Cash Advance') {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM cash_advance_details WHERE category = :name");
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM reimbursement_details WHERE category = :name");
        }
        $stmt->execute(['name' => $cat['name']]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function deactivateCategory(PDO $pdo, int $id): void
    {
        $stmt = $pdo->prepare("UPDATE coverage_categories SET is_active = 0 WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public static function deleteCategory(PDO $pdo, int $id): void
    {
        if (self::isCategoryInUse($pdo, $id)) {
            throw new InvalidArgumentException('Cannot delete a category that is already used by existing transactions.');
        }

        $pdo->beginTransaction();
        try {
            $stmt1 = $pdo->prepare("DELETE FROM coverage_category_documents WHERE category_id = :id");
            $stmt1->execute(['id' => $id]);

            $stmt2 = $pdo->prepare("DELETE FROM coverage_categories WHERE id = :id");
            $stmt2->execute(['id' => $id]);

            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function saveCategory(PDO $pdo, array $payload): int
    {
        $id = isset($payload['id']) && $payload['id'] !== '' && $payload['id'] !== null
            ? (int)$payload['id'] : null;
        $transactionType = trim((string)($payload['transaction_type'] ?? ''));
        $name = trim((string)($payload['name'] ?? ''));
        $displayLabel = trim((string)($payload['display_label'] ?? ''));
        $displayLabel = $displayLabel !== '' ? $displayLabel : null;
        $sortOrder = (int)($payload['sort_order'] ?? 0);
        $isActive = !empty($payload['is_active']) ? 1 : 0;
        $fieldConfig = self::normalizeFieldConfig(
            $transactionType,
            is_array($payload['field_config'] ?? null) ? $payload['field_config'] : []
        );
        $documents = is_array($payload['documents'] ?? null) ? $payload['documents'] : [];

        $alias = is_array($payload['alias'] ?? null) ? $payload['alias'] : [];
        $aliasCategoryId = !empty($alias['category_id']) ? (int)$alias['category_id'] : null;
        $aliasTxType = !empty($alias['transaction_type']) ? trim((string)$alias['transaction_type']) : null;
        $aliasNote = !empty($alias['note']) ? trim((string)$alias['note']) : null;
        $aliasSourceLabel = !empty($alias['source_label']) ? trim((string)$alias['source_label']) : null;

        if (!in_array($transactionType, ['Cash Advance', 'Reimbursement'], true)) {
            throw new InvalidArgumentException('Invalid transaction type.');
        }
        if ($name === '' || strlen($name) > 100) {
            throw new InvalidArgumentException('Category name is required and must be at most 100 characters.');
        }

        if ($id !== null) {
            $existing = self::getCategoryById($pdo, $id);
            if ($existing === null) {
                throw new InvalidArgumentException('Category not found.');
            }
            if ($existing['name'] !== $name && self::isCategoryInUse($pdo, $id)) {
                throw new InvalidArgumentException('Cannot rename a category that is already used by existing transactions.');
            }
        }

        // Uniqueness check
        $dupStmt = $pdo->prepare("
            SELECT id FROM coverage_categories
            WHERE transaction_type = :tx_type AND name = :name AND id != :id
            LIMIT 1
        ");
        $dupStmt->execute([
            'tx_type' => $transactionType,
            'name' => $name,
            'id' => $id ?? 0,
        ]);
        if ($dupStmt->fetch()) {
            throw new InvalidArgumentException('A category with this name already exists for ' . $transactionType . '.');
        }

        $pdo->beginTransaction();

        try {
            if ($id === null) {
                $stmt = $pdo->prepare("
                    INSERT INTO coverage_categories
                        (transaction_type, name, display_label, sort_order, is_active, field_config,
                         alias_transaction_type, alias_category_id, alias_note, alias_source_label)
                    VALUES
                        (:transaction_type, :name, :display_label, :sort_order, :is_active, :field_config,
                         :alias_transaction_type, :alias_category_id, :alias_note, :alias_source_label)
                ");
                $stmt->execute([
                    'transaction_type' => $transactionType,
                    'name' => $name,
                    'display_label' => $displayLabel,
                    'sort_order' => $sortOrder,
                    'is_active' => $isActive,
                    'field_config' => json_encode($fieldConfig),
                    'alias_transaction_type' => $aliasTxType,
                    'alias_category_id' => $aliasCategoryId,
                    'alias_note' => $aliasNote,
                    'alias_source_label' => $aliasSourceLabel,
                ]);
                $id = (int)$pdo->lastInsertId();
            } else {
                $stmt = $pdo->prepare("
                    UPDATE coverage_categories SET
                        transaction_type = :transaction_type,
                        name = :name,
                        display_label = :display_label,
                        sort_order = :sort_order,
                        is_active = :is_active,
                        field_config = :field_config,
                        alias_transaction_type = :alias_transaction_type,
                        alias_category_id = :alias_category_id,
                        alias_note = :alias_note,
                        alias_source_label = :alias_source_label
                    WHERE id = :id
                ");
                $stmt->execute([
                    'id' => $id,
                    'transaction_type' => $transactionType,
                    'name' => $name,
                    'display_label' => $displayLabel,
                    'sort_order' => $sortOrder,
                    'is_active' => $isActive,
                    'field_config' => json_encode($fieldConfig),
                    'alias_transaction_type' => $aliasTxType,
                    'alias_category_id' => $aliasCategoryId,
                    'alias_note' => $aliasNote,
                    'alias_source_label' => $aliasSourceLabel,
                ]);
            }

            $delDocs = $pdo->prepare("DELETE FROM coverage_category_documents WHERE category_id = :category_id");
            $delDocs->execute(['category_id' => $id]);

            $insertDoc = $pdo->prepare("
                INSERT INTO coverage_category_documents
                    (category_id, section_title, sort_order, title, is_required, condition_text, modes_of_travel, stage)
                VALUES
                    (:category_id, :section_title, :sort_order, :title, :is_required, :condition_text, :modes_of_travel, :stage)
            ");

            foreach ($documents as $sort => $doc) {
                $title = trim((string)($doc['title'] ?? ''));
                if ($title === '') {
                    continue;
                }
                $sectionTitle = trim((string)($doc['section_title'] ?? ''));
                
                $modesOfTravel = null;
                if (!empty($doc['modes_of_travel']) && is_array($doc['modes_of_travel'])) {
                    $modesOfTravel = json_encode($doc['modes_of_travel']);
                }

                $insertDoc->execute([
                    'category_id' => $id,
                    'section_title' => $sectionTitle !== '' ? $sectionTitle : null,
                    'sort_order' => (int)($doc['sort_order'] ?? $sort),
                    'title' => $title,
                    'is_required' => !empty($doc['is_required']) ? 1 : 0,
                    'condition_text' => trim((string)($doc['condition_text'] ?? '')) ?: null,
                    'modes_of_travel' => $modesOfTravel,
                    'stage' => $doc['stage'] ?? 'submission',
                ]);
            }

            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public static function resolveDocumentChecklist(PDO $pdo, string $transactionType, string $category, string $stage = 'submission'): array
    {
        $empty = ['documents' => [], 'sections' => [], 'note' => null, 'source_label' => null];

        if ($transactionType === '' || $category === '') {
            return $empty;
        }

        $cat = self::getCategoryByName($pdo, $transactionType, $category);
        if ($cat === null) {
            return $empty;
        }

        $note = $cat['alias_note'];
        $sourceLabel = $cat['alias_source_label'];
        $lookupId = $cat['id'];

        if ($cat['alias_category_id'] !== null) {
            $lookupId = $cat['alias_category_id'];
        }

        $lookupCat = self::getCategoryById($pdo, $lookupId);
        if ($lookupCat === null) {
            return $empty;
        }

        $docs = $lookupCat['documents'];
        $mainDocs = [];
        $sections = [];

        foreach ($docs as $doc) {
            $item = [
                'title' => $doc['title'],
                'required' => $doc['is_required'] === 1,
            ];
            if (!empty($doc['condition_text'])) {
                $item['condition'] = $doc['condition_text'];
            }
            if (!empty($doc['modes_of_travel'])) {
                $item['modesOfTravel'] = $doc['modes_of_travel'];
            }

            if ($doc['section_title'] === null || $doc['section_title'] === '') {
                $mainDocs[] = $item;
            } else {
                $sectionTitle = $doc['section_title'];
                if (!isset($sections[$sectionTitle])) {
                    $sections[$sectionTitle] = ['title' => $sectionTitle, 'documents' => []];
                }
                $sections[$sectionTitle]['documents'][] = $item;
            }
        }

        return [
            'documents' => $mainDocs,
            'sections' => array_values($sections),
            'note' => $note,
            'source_label' => $sourceLabel,
        ];
    }
}
