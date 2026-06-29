<?php
require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/database.php';

$pdo = $fastPDO;

echo "Migrating Travel Modes V3...\n<br>";

$stmt = $pdo->prepare("SELECT id FROM coverage_categories WHERE transaction_type = 'Reimbursement' AND name = 'Travel'");
$stmt->execute();
$categoryId = $stmt->fetchColumn();

// Specific document -> specific modes
$specificMappings = [
    'Boarding Pass and Official Receipt for Airfare' => ['Plane (airfare)'],
    'Boarding Pass and Official Receipt of Airfare' => ['Plane (airfare)'],
    'Bus Ticket / Grab E-Receipts / Other Transport Receipts' => ['Bus', 'Taxi/ride-hailing', 'Van rental', 'Motorcycle/ride-hailing'],
    'Reimbursement Expense Receipt' => ['Jeep', 'Tricycle'],
    'Certificate of Expenses not Requiring Receipts' => ['Jeep', 'Tricycle'],
    'Photocopy of Driver\'s License' => ['Van rental'],
    'Certificate of Registration' => ['Van rental'],
    'Geotagged Pictures of Passenger/s' => ['Van rental'],
    'List of Passenger/s with signature' => ['Van rental'],
    'Authority to Use Van Rental' => ['Van rental']
];

// override specific documents with their specific modes
foreach ($specificMappings as $title => $modes) {
    $jsonModes = json_encode($modes);
    $stmt = $pdo->prepare("UPDATE coverage_category_documents SET modes_of_travel = :modes WHERE category_id = :cat_id AND title = :title");
    $stmt->execute([
        ':modes' => $jsonModes,
        ':cat_id' => $categoryId,
        ':title' => $title
    ]);
    echo "Mapped '$title' -> " . implode(', ', $modes) . ". Rows: " . $stmt->rowCount() . "\n<br>";
}

echo "\n<br>Migration V3 Complete!\n<br>";
