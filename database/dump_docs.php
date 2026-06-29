<?php
require __DIR__ . '/../config/env.php';
require __DIR__ . '/../config/database.php';

$pdo = $fastPDO;

$stmt = $pdo->prepare("SELECT id FROM coverage_categories WHERE transaction_type = 'Reimbursement' AND name = 'Travel'");
$stmt->execute();
$categoryId = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT id, title, modes_of_travel FROM coverage_category_documents WHERE category_id = :cat_id");
$stmt->execute([':cat_id' => $categoryId]);
$docs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($docs);
echo "</pre>";
