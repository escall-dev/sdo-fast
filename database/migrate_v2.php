<?php
require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/database.php';

$pdo = $fastPDO;

echo "Migrating Travel Modes V2...\n";

// Get Travel Category ID
$stmt = $pdo->prepare("SELECT id FROM coverage_categories WHERE transaction_type = 'Reimbursement' AND name = 'Travel'");
$stmt->execute();
$categoryId = $stmt->fetchColumn();

if (!$categoryId) {
    die("Travel category not found.\n");
}

$allModes = json_encode([
    'Plane (airfare)', 'Bus', 'Taxi/ride-hailing', 'Van rental',
    'Ferry/boat', 'Motorcycle/ride-hailing', 'Train (MRT, LRT, PNR)',
    'Jeep', 'Tricycle'
]);

// Specific document -> specific modes
$specificMappings = [
    '%Boarding Pass%' => ['Plane (airfare)'],
    '%Bus Ticket%' => ['Bus', 'Taxi/ride-hailing', 'Van rental', 'Motorcycle/ride-hailing'],
    '%Electronic tickets%' => ['Train (MRT, LRT, PNR)', 'Ferry/boat'],
    '%RER%' => ['Jeep', 'Tricycle'],
];

// First: set ALL documents to ALL modes (global)
$stmt = $pdo->prepare("UPDATE coverage_category_documents SET modes_of_travel = :modes WHERE category_id = :cat_id AND (modes_of_travel IS NULL OR modes_of_travel = '' OR modes_of_travel = '[]')");
$stmt->execute([':modes' => $allModes, ':cat_id' => $categoryId]);
echo "Set ALL modes for " . $stmt->rowCount() . " unassigned documents.\n<br>";

// Then: override specific documents with their specific modes
foreach ($specificMappings as $like => $modes) {
    $jsonModes = json_encode($modes);
    $stmt = $pdo->prepare("UPDATE coverage_category_documents SET modes_of_travel = :modes WHERE category_id = :cat_id AND title LIKE :like");
    $stmt->execute([
        ':modes' => $jsonModes,
        ':cat_id' => $categoryId,
        ':like' => $like
    ]);
    echo "Mapped '$like' -> " . implode(', ', $modes) . ". Rows: " . $stmt->rowCount() . "\n<br>";
}

echo "\n<br>Migration Complete!\n<br>";
echo "Common documents now appear for ALL modes.\n<br>";
echo "Specific documents only appear for their assigned modes.\n<br>";
