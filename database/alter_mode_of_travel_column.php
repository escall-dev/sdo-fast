<?php
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $fastPDO->exec("ALTER TABLE `reimbursement_details` MODIFY `mode_of_travel` VARCHAR(255) DEFAULT NULL;");
    echo "Column mode_of_travel in reimbursement_details altered to VARCHAR(255) successfully.\n";
} catch (PDOException $e) {
    echo "Error altering table: " . $e->getMessage() . "\n";
}
?>
