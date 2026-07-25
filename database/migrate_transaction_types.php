<?php
require_once __DIR__ . '/../config/database.php';

try {
    echo "Starting transaction types migration...\n";
    
    // Create transaction_types table
    $fastPDO->exec("
        CREATE TABLE IF NOT EXISTS `transaction_types` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_name` (`name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    echo "Created transaction_types table (or it already exists).\n";
    
    // Seed default transaction types
    $defaultTypes = ['Cash Advance', 'Reimbursement', 'Payroll'];
    
    $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transaction_types WHERE name = ?");
    $insertStmt = $fastPDO->prepare("INSERT INTO transaction_types (name, is_active) VALUES (?, 1)");
    
    foreach ($defaultTypes as $type) {
        $stmt->execute([$type]);
        $exists = $stmt->fetchColumn();
        
        if (!$exists) {
            $insertStmt->execute([$type]);
            echo "Inserted '$type' transaction type.\n";
        } else {
            echo "'$type' already exists, skipping.\n";
        }
    }
    
    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
