<?php
require_once __DIR__ . '/../config/database.php';

try {
    $fastPDO->exec("DROP TABLE IF EXISTS `document_checklists`");
    
    // Add stage column to coverage_category_documents if it doesn't exist
    $fastPDO->exec("ALTER TABLE `coverage_category_documents` ADD COLUMN IF NOT EXISTS `stage` VARCHAR(20) NOT NULL DEFAULT 'submission'");
    
    // Insert Liquidation requirements for all Cash Advance categories
    $stmt = $fastPDO->query("SELECT id, name FROM coverage_categories WHERE transaction_type = 'Cash Advance' AND is_active = 1");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $insertStmt = $fastPDO->prepare("INSERT INTO `coverage_category_documents` (category_id, title, is_required, stage) VALUES (?, ?, ?, 'liquidation')");
    $insertCondStmt = $fastPDO->prepare("INSERT INTO `coverage_category_documents` (category_id, title, is_required, stage, condition_text) VALUES (?, ?, ?, 'liquidation', ?)");
    
    foreach ($categories as $cat) {
        $catId = $cat['id'];
        $catName = $cat['name'];
        
        // 1. Original Receipt / Invoice
        $insertStmt->execute([$catId, 'Original Receipt / Invoice', 1]);
        
        // 2. Narrative Report (only if Training Expenses)
        if (stripos($catName, 'Training') !== false) {
            $insertCondStmt->execute([$catId, 'Narrative Report', 1, 'Cash Advance Training Expenses']);
        }
    }
    
    echo "Successfully updated coverage_category_documents table with stage column and seeded liquidation documents.\n";
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
