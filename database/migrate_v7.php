<?php
require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/database.php';

$pdo = $fastPDO;

echo "Migrating Travel Modes V7...\n<br>";

$jsonModes = '["Jeep","Tricycle"]';
$stmt = $pdo->query("UPDATE coverage_category_documents SET modes_of_travel = '$jsonModes' WHERE id = 225");
if ($stmt) {
    echo "Updated ID 225 manually. Rows: " . $stmt->rowCount() . "<br>";
} else {
    print_r($pdo->errorInfo());
}

$stmt = $pdo->query("SELECT modes_of_travel FROM coverage_category_documents WHERE id = 225");
echo "Read back ID 225: " . $stmt->fetchColumn() . "<br>";
