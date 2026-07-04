<?php
/**
 * Migration Script: Travel Modes
 * Creates the modes_of_travel table and populates it with the hardcoded defaults.
 */

require_once __DIR__ . '/../config/database.php';

try {
    echo "Starting travel modes migration...\n";

    // Create table
    $fastPDO->exec("
        CREATE TABLE IF NOT EXISTS modes_of_travel (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) UNIQUE NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "Table 'modes_of_travel' created or already exists.\n";

    // Default modes from legacy codebase
    $defaultModes = [
        'Plane (airfare)',
        'Bus',
        'Taxi/ride-hailing',
        'Van rental',
        'Ferry/boat',
        'Motorcycle/ride-hailing',
        'Train (MRT, LRT, PNR)',
        'Jeep',
        'Tricycle'
    ];

    $stmt = $fastPDO->prepare("INSERT IGNORE INTO modes_of_travel (name, is_active) VALUES (:name, 1)");

    foreach ($defaultModes as $mode) {
        $stmt->execute(['name' => $mode]);
    }
    echo "Inserted default travel modes.\n";

    echo "Migration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
