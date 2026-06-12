<?php
/**
 * Migration script: Widen category columns for expanded coverage types in SDO FAST.
 * 
 * cash_advance_details.category: VARCHAR(50) → VARCHAR(100)
 * reimbursement_details.category: VARCHAR(50) → VARCHAR(100)
 */

require_once __DIR__ . '/../config/env.php';

try {
    $pdo = new PDO(
        'mysql:host=' . env('FAST_DB_HOST', 'localhost') . ';dbname=' . env('FAST_DB_NAME', 'fast_db') . ';charset=utf8mb4',
        env('FAST_DB_USER', 'root'),
        env('FAST_DB_PASS', '')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Widen cash_advance_details.category
    $pdo->exec("ALTER TABLE `cash_advance_details` MODIFY COLUMN `category` VARCHAR(100) NOT NULL");
    echo "[OK] cash_advance_details.category widened to VARCHAR(100).\n";

    // Widen reimbursement_details.category
    $pdo->exec("ALTER TABLE `reimbursement_details` MODIFY COLUMN `category` VARCHAR(100) NOT NULL");
    echo "[OK] reimbursement_details.category widened to VARCHAR(100).\n";

    // Add inclusive start/end date columns to reimbursement_details for types that need dates
    // Check if columns already exist before adding
    $columns = $pdo->query("SHOW COLUMNS FROM `reimbursement_details`")->fetchAll(PDO::FETCH_COLUMN);
    
    if (!in_array('inclusive_dates', $columns)) {
        $pdo->exec("ALTER TABLE `reimbursement_details` ADD COLUMN `inclusive_dates` VARCHAR(255) DEFAULT NULL AFTER `reimbursement_month`");
        echo "[OK] Added inclusive_dates column to reimbursement_details.\n";
    }
    
    if (!in_array('venue', $columns)) {
        $pdo->exec("ALTER TABLE `reimbursement_details` ADD COLUMN `venue` VARCHAR(255) DEFAULT NULL AFTER `inclusive_dates`");
        echo "[OK] Added venue column to reimbursement_details.\n";
    }

    if (!in_array('approved_ta_path', $columns)) {
        $pdo->exec("ALTER TABLE `reimbursement_details` ADD COLUMN `approved_ta_path` VARCHAR(255) DEFAULT NULL AFTER `venue`");
        echo "[OK] Added approved_ta_path column to reimbursement_details.\n";
    }

    if (!in_array('travel_itinerary_path', $columns)) {
        $pdo->exec("ALTER TABLE `reimbursement_details` ADD COLUMN `travel_itinerary_path` VARCHAR(255) DEFAULT NULL AFTER `approved_ta_path`");
        echo "[OK] Added travel_itinerary_path column to reimbursement_details.\n";
    }

    if (!in_array('activity_proposal_path', $columns)) {
        $pdo->exec("ALTER TABLE `reimbursement_details` ADD COLUMN `activity_proposal_path` VARCHAR(255) DEFAULT NULL AFTER `travel_itinerary_path`");
        echo "[OK] Added activity_proposal_path column to reimbursement_details.\n";
    }

    echo "\n[DONE] All migration steps completed successfully.\n";

} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
