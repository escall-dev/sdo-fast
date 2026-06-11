<?php
/**
 * Migration script: Create reimbursement_details table for SDO FAST.
 */

require_once __DIR__ . '/../config/env.php';

try {
    $pdo = new PDO(
        'mysql:host=' . env('FAST_DB_HOST', 'localhost') . ';dbname=' . env('FAST_DB_NAME', 'fast_db') . ';charset=utf8mb4',
        env('FAST_DB_USER', 'root'),
        env('FAST_DB_PASS', '')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create reimbursement_details table
    $sql = "
        CREATE TABLE IF NOT EXISTS `reimbursement_details` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `transaction_id` INT NOT NULL UNIQUE,
            `category` VARCHAR(50) NOT NULL, -- 'Travel', 'Communications Allowance', 'Procured Goods'
            `reimbursement_month` VARCHAR(50) DEFAULT NULL, -- for Communications Allowance, e.g. 'June 2026'
            `dtr_path` VARCHAR(255) DEFAULT NULL,
            `certificate_path` VARCHAR(255) DEFAULT NULL,
            `bill_proof_path` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql);
    echo "[OK] reimbursement_details table created/verified successfully.\n";

} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
