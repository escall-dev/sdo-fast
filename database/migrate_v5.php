<?php
require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/database.php';

$pdo = $fastPDO;

echo "Migrating Travel Modes V5...\n<br>";

// Specific ID -> specific modes
$specificMappings = [
    225 => ['Jeep', 'Tricycle'], // Reimbursement Expense Receipt
    226 => ['Jeep', 'Tricycle'], // Certificate of Expenses not Requiring Receipts
    227 => ['Van rental'], // Photocopy of Driver's License
    228 => ['Van rental'], // Certificate of Registration
    229 => ['Van rental'], // Geotagged Pictures of Passenger/s
    230 => ['Van rental'], // List of Passenger/s with signature
    231 => ['Van rental']  // Authority to Use Van Rental
];

foreach ($specificMappings as $id => $modes) {
    $jsonModes = json_encode($modes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $stmt = $pdo->prepare("UPDATE coverage_category_documents SET modes_of_travel = :modes WHERE id = :id");
    $result = $stmt->execute([
        ':modes' => $jsonModes,
        ':id' => $id
    ]);
    if (!$result) {
        echo "Error on ID $id: ";
        print_r($stmt->errorInfo());
        echo "<br>";
    }
    echo "Mapped ID $id to $jsonModes. Rows: " . $stmt->rowCount() . "\n<br>";
}

echo "\n<br>Migration V5 Complete!\n<br>";
