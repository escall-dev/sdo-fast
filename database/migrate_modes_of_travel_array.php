<?php
require_once __DIR__ . '/../config/database.php';

try {
    // 1. Migrate coverage_category_documents (Checklist configuration)
    $stmt1 = $fastPDO->query("SELECT id, modes_of_travel FROM coverage_category_documents WHERE modes_of_travel IS NOT NULL AND modes_of_travel != ''");
    $rows1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    
    $updated1 = 0;
    $updateStmt1 = $fastPDO->prepare("UPDATE coverage_category_documents SET modes_of_travel = :new_modes WHERE id = :id");
    
    foreach ($rows1 as $row) {
        $modesStr = $row['modes_of_travel'];
        $decoded = json_decode($modesStr, true);
        
        if (!is_array($decoded)) {
            $newModes = json_encode([$modesStr]);
            $updateStmt1->execute([
                'new_modes' => $newModes,
                'id' => $row['id']
            ]);
            $updated1++;
        }
    }
    echo "Checklist configuration migration completed. Updated $updated1 records.\n";
    
    // 2. Migrate reimbursement_details (Existing transactions)
    $stmt2 = $fastPDO->query("SELECT transaction_id, mode_of_travel FROM reimbursement_details WHERE mode_of_travel IS NOT NULL AND mode_of_travel != ''");
    $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    $updated2 = 0;
    $updateStmt2 = $fastPDO->prepare("UPDATE reimbursement_details SET mode_of_travel = :new_modes WHERE transaction_id = :id");
    
    foreach ($rows2 as $row) {
        $modesStr = $row['mode_of_travel'];
        $decoded = json_decode($modesStr, true);
        
        if (!is_array($decoded)) {
            $newModes = json_encode([$modesStr]);
            $updateStmt2->execute([
                'new_modes' => $newModes,
                'id' => $row['transaction_id']
            ]);
            $updated2++;
        }
    }
    echo "Reimbursement transactions migration completed. Updated $updated2 records.\n";
    
} catch (PDOException $e) {
    echo "Error running migration: " . $e->getMessage() . "\n";
}
?>
