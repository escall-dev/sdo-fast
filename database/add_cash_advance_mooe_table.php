<?php
/**
 * Migration script: Create cash_advance_details table for SDO FAST.
 */

require_once __DIR__ . '/../config/env.php';

try {
    $pdo = new PDO(
        'mysql:host=' . env('FAST_DB_HOST', 'localhost') . ';dbname=' . env('FAST_DB_NAME', 'fast_db') . ';charset=utf8mb4',
        env('FAST_DB_USER', 'root'),
        env('FAST_DB_PASS', '')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create cash_advance_details table
    $sql = "
        CREATE TABLE IF NOT EXISTS `cash_advance_details` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `transaction_id` INT NOT NULL UNIQUE,
            `category` VARCHAR(50) NOT NULL, -- 'MOOE' or 'Activity'
            `inclusive_dates` VARCHAR(255) DEFAULT NULL,
            `fund_source` VARCHAR(255) DEFAULT NULL,
            `venue` VARCHAR(255) DEFAULT NULL,
            `approved_ta_path` VARCHAR(255) DEFAULT NULL,
            `travel_itinerary_path` VARCHAR(255) DEFAULT NULL,
            `activity_proposal_path` VARCHAR(255) DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($sql);
    echo "[OK] cash_advance_details table created/verified successfully.\n";

} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
