<?php
require_once __DIR__ . '/../config/database.php';
$stmt = $fastPDO->query("SELECT count(*) FROM document_checklists");
echo "Total rows: " . $stmt->fetchColumn() . "\n";
