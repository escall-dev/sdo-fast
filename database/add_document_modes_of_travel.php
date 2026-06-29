<?php
/**
 * Migration to add modes_of_travel column to coverage_category_documents
 */

require_once __DIR__ . '/../config/env.php';

try {
    $pdo = new PDO(
        'mysql:host=' . env('FAST_DB_HOST', 'localhost') . ';dbname=' . env('FAST_DB_NAME', 'fast_db') . ';charset=utf8mb4',
        env('FAST_DB_USER', 'root'),
        env('FAST_DB_PASS', '')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add modes_of_travel column
    $pdo->exec("ALTER TABLE `coverage_category_documents` ADD COLUMN IF NOT EXISTS `modes_of_travel` VARCHAR(255) DEFAULT NULL AFTER `condition_text`;");
    echo "[OK] modes_of_travel column added to coverage_category_documents.\n";

} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
