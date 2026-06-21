<?php
/**
 * Migration: coverage_categories + coverage_category_documents tables.
 * Seeds from hardcoded category lists, field maps, document checklists, and aliases.
 */

require_once __DIR__ . '/../config/env.php';

try {
    $pdo = new PDO(
        'mysql:host=' . env('FAST_DB_HOST', 'localhost') . ';dbname=' . env('FAST_DB_NAME', 'fast_db') . ';charset=utf8mb4',
        env('FAST_DB_USER', 'root'),
        env('FAST_DB_PASS', '')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `coverage_categories` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `transaction_type` VARCHAR(50) NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `display_label` VARCHAR(255) DEFAULT NULL,
            `sort_order` INT NOT NULL DEFAULT 0,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `field_config` JSON NOT NULL,
            `alias_transaction_type` VARCHAR(50) DEFAULT NULL,
            `alias_category_id` INT DEFAULT NULL,
            `alias_note` TEXT DEFAULT NULL,
            `alias_source_label` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY `uq_coverage_type_name` (`transaction_type`, `name`),
            KEY `idx_coverage_active` (`transaction_type`, `is_active`, `sort_order`),
            CONSTRAINT `fk_coverage_alias_category`
                FOREIGN KEY (`alias_category_id`) REFERENCES `coverage_categories` (`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "[OK] coverage_categories table created/verified.\n";

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `coverage_category_documents` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `category_id` INT NOT NULL,
            `section_title` VARCHAR(255) DEFAULT NULL,
            `sort_order` INT NOT NULL DEFAULT 0,
            `title` VARCHAR(255) NOT NULL,
            `is_required` TINYINT(1) NOT NULL DEFAULT 1,
            `condition_text` VARCHAR(255) DEFAULT NULL,
            KEY `idx_category_docs` (`category_id`, `section_title`, `sort_order`),
            CONSTRAINT `fk_category_docs_category`
                FOREIGN KEY (`category_id`) REFERENCES `coverage_categories` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "[OK] coverage_category_documents table created/verified.\n";

    $existing = (int)$pdo->query("SELECT COUNT(*) FROM coverage_categories")->fetchColumn();
    if ($existing > 0) {
        echo "[SKIP] coverage_categories already seeded ($existing rows). Run manually if re-seed needed.\n";
        exit(0);
    }

    $checklists = require __DIR__ . '/../config/document_checklists.php';

    $caFieldConfig = [
        'Travel' => ['dateVenue' => true, 'fundSource' => true, 'taItinerary' => true, 'activityProposal' => false, 'month' => false],
        'School MOOE' => ['dateVenue' => false, 'fundSource' => true, 'taItinerary' => false, 'activityProposal' => false, 'month' => false],
        'SBFP' => ['dateVenue' => false, 'fundSource' => true, 'taItinerary' => false, 'activityProposal' => false, 'month' => false],
        'Training' => ['dateVenue' => true, 'fundSource' => false, 'taItinerary' => false, 'activityProposal' => true, 'month' => false],
        'Meals' => ['dateVenue' => true, 'fundSource' => false, 'taItinerary' => false, 'activityProposal' => false, 'month' => false],
        'Accommodation' => ['dateVenue' => true, 'fundSource' => false, 'taItinerary' => false, 'activityProposal' => false, 'month' => false],
        'Meals and Accommodation' => ['dateVenue' => true, 'fundSource' => false, 'taItinerary' => false, 'activityProposal' => false, 'month' => false],
        'Honorarium' => ['dateVenue' => false, 'fundSource' => false, 'taItinerary' => false, 'activityProposal' => false, 'month' => false],
        'Supplies and Materials' => ['dateVenue' => false, 'fundSource' => false, 'taItinerary' => false, 'activityProposal' => false, 'month' => false],
        'Communication Expenses' => ['dateVenue' => false, 'fundSource' => false, 'taItinerary' => false, 'activityProposal' => false, 'month' => true],
        'SLAC / Moving-Up / Graduation / GAWAD' => ['dateVenue' => true, 'fundSource' => false, 'taItinerary' => false, 'activityProposal' => true, 'month' => false],
    ];

    $reimbFieldConfig = [
        'Travel' => ['dateVenue' => true, 'taItinerary' => true, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Supplies and Materials' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Meals' => ['dateVenue' => true, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Accommodation' => ['dateVenue' => true, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Meals and Accommodation' => ['dateVenue' => true, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Honorarium' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Communication Load' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => true, 'utilityBills' => false],
        'Utility Bills' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => true],
        'Repair, Repaint, Improvement' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Installation of Electricity and Water' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Installation of Internet / Telephone' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Seminars / Trainings' => ['dateVenue' => true, 'taItinerary' => false, 'activityProposal' => true, 'communications' => false, 'utilityBills' => false],
        'GAD Documents / SLAC Session' => ['dateVenue' => true, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Job Order' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Fidelity Bond' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
        'Immersion and Insurance for SHS' => ['dateVenue' => false, 'taItinerary' => false, 'activityProposal' => false, 'communications' => false, 'utilityBills' => false],
    ];

    $displayLabels = [
        'Cash Advance' => [
            'Travel' => 'Travel (land transpo excluded)',
            'SBFP' => 'SBFP (School Based Feeding Program)',
            'SLAC / Moving-Up / Graduation / GAWAD' => 'SLAC / Moving-Up / Graduation / GAWAD and similar events',
        ],
        'Reimbursement' => [
            'Utility Bills' => 'Utility Bills (Electricity, Water, Telephone, Internet)',
            'Seminars / Trainings' => 'Seminars / Trainings (from Enclosure 12)',
        ],
    ];

    $caOrder = array_keys($caFieldConfig);
    $reimbOrder = array_keys($reimbFieldConfig);

    $insertCat = $pdo->prepare("
        INSERT INTO coverage_categories
            (transaction_type, name, display_label, sort_order, is_active, field_config,
             alias_transaction_type, alias_category_id, alias_note, alias_source_label)
        VALUES
            (:transaction_type, :name, :display_label, :sort_order, 1, :field_config,
             NULL, NULL, NULL, NULL)
    ");

    $insertDoc = $pdo->prepare("
        INSERT INTO coverage_category_documents
            (category_id, section_title, sort_order, title, is_required, condition_text)
        VALUES
            (:category_id, :section_title, :sort_order, :title, :is_required, :condition_text)
    ");

    $categoryIds = [];

    $pdo->beginTransaction();

    foreach (['Cash Advance' => $caOrder, 'Reimbursement' => $reimbOrder] as $txType => $names) {
        $fieldMap = $txType === 'Cash Advance' ? $caFieldConfig : $reimbFieldConfig;
        foreach ($names as $sort => $name) {
            $displayLabel = $displayLabels[$txType][$name] ?? null;
            $insertCat->execute([
                'transaction_type' => $txType,
                'name' => $name,
                'display_label' => $displayLabel,
                'sort_order' => $sort + 1,
                'field_config' => json_encode($fieldMap[$name]),
            ]);
            $categoryIds[$txType][$name] = (int)$pdo->lastInsertId();
        }
    }

    // Apply checklist aliases after all categories exist
    $aliasUpdates = [
        ['Cash Advance', 'Accommodation', 'Cash Advance', 'Meals and Accommodation',
            'Per DM 214, Accommodation has no separate enclosure and falls under Meals and Accommodation.', null],
        ['Reimbursement', 'Accommodation', 'Cash Advance', 'Meals and Accommodation',
            'Per DM 214, Accommodation has no separate enclosure and falls under Meals and Accommodation.',
            'Same as Cash Advance: Meals and Accommodation'],
        ['Reimbursement', 'Meals and Accommodation', 'Cash Advance', 'Meals and Accommodation', null,
            'Same as Cash Advance: Meals and Accommodation'],
        ['Reimbursement', 'Honorarium', 'Cash Advance', 'Honorarium', null,
            'Same as Cash Advance: Honorarium'],
    ];

    $updateAlias = $pdo->prepare("
        UPDATE coverage_categories
        SET alias_transaction_type = :alias_tx,
            alias_category_id = :alias_id,
            alias_note = :alias_note,
            alias_source_label = :alias_source_label
        WHERE transaction_type = :tx_type AND name = :name
    ");

    foreach ($aliasUpdates as [$txType, $name, $aliasTx, $aliasName, $note, $sourceLabel]) {
        $aliasId = $categoryIds[$aliasTx][$aliasName] ?? null;
        $updateAlias->execute([
            'alias_tx' => $aliasTx,
            'alias_id' => $aliasId,
            'alias_note' => $note,
            'alias_source_label' => $sourceLabel,
            'tx_type' => $txType,
            'name' => $name,
        ]);
    }

    // Seed documents from document_checklists.php (skip aliased-only categories without own checklist entry)
    foreach ($checklists as $txType => $categories) {
        foreach ($categories as $catName => $entry) {
            $catId = $categoryIds[$txType][$catName] ?? null;
            if ($catId === null) {
                continue;
            }

            $sort = 0;
            foreach ($entry['documents'] ?? [] as $doc) {
                $insertDoc->execute([
                    'category_id' => $catId,
                    'section_title' => null,
                    'sort_order' => $sort++,
                    'title' => $doc['title'],
                    'is_required' => !empty($doc['required']) ? 1 : 0,
                    'condition_text' => $doc['condition'] ?? null,
                ]);
            }

            foreach ($entry['sections'] ?? [] as $section) {
                $sectionTitle = $section['title'] ?? 'Additional Documents';
                $sectionSort = 0;
                foreach ($section['documents'] ?? [] as $doc) {
                    $insertDoc->execute([
                        'category_id' => $catId,
                        'section_title' => $sectionTitle,
                        'sort_order' => $sectionSort++,
                        'title' => $doc['title'],
                        'is_required' => !empty($doc['required']) ? 1 : 0,
                        'condition_text' => $doc['condition'] ?? null,
                    ]);
                }
            }
        }
    }

    $pdo->commit();

    $catCount = (int)$pdo->query("SELECT COUNT(*) FROM coverage_categories")->fetchColumn();
    $docCount = (int)$pdo->query("SELECT COUNT(*) FROM coverage_category_documents")->fetchColumn();
    echo "[OK] Seeded $catCount coverage categories and $docCount document rows.\n";

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
