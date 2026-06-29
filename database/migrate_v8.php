<?php
require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/database.php';

$pdo = $fastPDO;

echo "Migrating Travel Modes V8 (Cleaning up standalone documents)...\n<br>";

// Get Travel Category ID
$stmt = $pdo->prepare("SELECT id FROM coverage_categories WHERE transaction_type = 'Reimbursement' AND name = 'Travel'");
$stmt->execute();
$categoryId = $stmt->fetchColumn();

if (!$categoryId) {
    die("Travel category not found.\n");
}

// These are the ONLY documents that should have modes.
$specificMappings = [
    'Boarding Pass and Official Receipt for Airfare' => ['Plane (airfare)'],
    'Boarding Pass and Official Receipt of Airfare' => ['Plane (airfare)'],
    'Bus Ticket / Grab E-Receipts / Other Transport Receipts' => ['Bus', 'Taxi/ride-hailing', 'Van rental', 'Motorcycle/ride-hailing'],
    'Official Receipt for registration fee' => [], // Probably none? Wait. Let's just reset everything not explicitly mentioned below.
    'Reimbursement Expense Receipt' => ['Jeep', 'Tricycle'],
    'Certificate of Expenses not Requiring Receipts' => ['Jeep', 'Tricycle'],
    'Photocopy of Driver\'s License' => ['Van rental'],
    'Certificate of Registration' => ['Van rental'],
    'Geotagged Pictures of Passenger/s' => ['Van rental'],
    'List of Passenger/s with signature' => ['Van rental'],
    'Authority to Use Van Rental' => ['Van rental']
];

$titlesToKeep = array_keys($specificMappings);

// First, reset ALL documents in Travel to []
$emptyModes = json_encode([]);
$stmt = $pdo->prepare("UPDATE coverage_category_documents SET modes_of_travel = :empty WHERE category_id = :cat_id");
$stmt->execute([':empty' => $emptyModes, ':cat_id' => $categoryId]);
echo "Reset ALL Travel documents to []. Rows: " . $stmt->rowCount() . "<br>";

// Then, re-apply the correct specific modes
foreach ($specificMappings as $title => $modes) {
    if (empty($modes)) continue;
    $jsonModes = json_encode($modes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $stmt = $pdo->prepare("UPDATE coverage_category_documents SET modes_of_travel = :modes WHERE category_id = :cat_id AND title = :title");
    $stmt->execute([
        ':modes' => $jsonModes,
        ':cat_id' => $categoryId,
        ':title' => $title
    ]);
    echo "Restored '$title' to $jsonModes. Rows: " . $stmt->rowCount() . "<br>";
}

echo "Cleanup Complete! Standalone documents now have NO modes checked, so they will NEVER appear in the Plane checklist.<br>";
