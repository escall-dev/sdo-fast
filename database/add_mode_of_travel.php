<?php
/**
 * Migration: Add mode_of_travel column to reimbursement_details table.
 */

require_once __DIR__ . '/../config/env.php';

try {
    $pdo = new PDO(
        'mysql:host=' . env('FAST_DB_HOST', 'localhost') . ';dbname=' . env('FAST_DB_NAME', 'fast_db') . ';charset=utf8mb4',
        env('FAST_DB_USER', 'root'),
        env('FAST_DB_PASS', '')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("ALTER TABLE `reimbursement_details` ADD COLUMN IF NOT EXISTS `mode_of_travel` VARCHAR(100) DEFAULT NULL AFTER `category`;");
    echo "[OK] mode_of_travel column added to reimbursement_details.\n";

} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
