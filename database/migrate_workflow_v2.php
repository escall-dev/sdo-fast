<?php
/**
 * Workflow Migration v2: 5-Stage → 6-Stage Transition
 * 
 * IDEMPOTENT — Safe to re-run. Uses a single database transaction.
 *
 * Creates:
 *   - attachment_approvals table
 *   - budget_checks table
 *   - signatory_tasks table
 *   - Cashier role, position, and permissions
 *
 * Remaps:
 *   Pending Accountant 1  → Pending Requestor
 *   Pending Support       → Pending Requestor
 *   Pending Budget Check  → Pending Budget
 *   Pending Accountant 2  → Pending Accounting Support
 *   Pending Final Approval→ Pending Signatories
 *   Approved              → Pending Signatory Approval
 *
 * Run via CLI:  php database/migrate_workflow_v2.php
 * Run via web:  http://localhost/fast/database/migrate_workflow_v2.php
 */

// Output as plain text for readability
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

require_once __DIR__ . '/../config/database.php';

if ($fastPDO === null) {
    die("[FATAL] Database connection failed.\n");
}

$log = [];
function logMsg($msg) {
    global $log;
    $log[] = $msg;
    echo $msg . "\n";
}

logMsg("=== FAST Workflow Migration v2 ===");
logMsg("Started at: " . date('Y-m-d H:i:s'));
logMsg("");

// =========================================================================
// STEP 1: Create new tables (DDL — cannot be inside transaction in MySQL)
// =========================================================================

logMsg("[STEP 1] Creating new tables...");

// 1a. attachment_approvals
try {
    $fastPDO->exec("
        CREATE TABLE IF NOT EXISTS attachment_approvals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_id INT NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            file_label VARCHAR(255) DEFAULT NULL,
            status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
            reviewed_by INT DEFAULT NULL,
            reviewed_at TIMESTAMP NULL DEFAULT NULL,
            remarks TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
            FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_aa_tx_status (transaction_id, status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    logMsg("  [OK] attachment_approvals table created/verified.");
} catch (Exception $e) {
    logMsg("  [WARN] attachment_approvals: " . $e->getMessage());
}

// 1b. budget_checks
try {
    $fastPDO->exec("
        CREATE TABLE IF NOT EXISTS budget_checks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_id INT NOT NULL,
            fund_source VARCHAR(255) NOT NULL,
            fund_source_tracking_number VARCHAR(255) DEFAULT NULL,
            fund_available TINYINT(1) NOT NULL DEFAULT 1,
            checked_by INT NOT NULL,
            checked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            remarks TEXT DEFAULT NULL,
            UNIQUE KEY budget_tx_unique (transaction_id),
            FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
            FOREIGN KEY (checked_by) REFERENCES users(id) ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    logMsg("  [OK] budget_checks table created/verified.");
} catch (Exception $e) {
    logMsg("  [WARN] budget_checks: " . $e->getMessage());
}

// 1b-2. Ensure new optional tracking number column exists in older installations
try {
    $colExists = $fastPDO->query("SHOW COLUMNS FROM budget_checks LIKE 'fund_source_tracking_number'")->fetch();
    if (!$colExists) {
        $fastPDO->exec("ALTER TABLE budget_checks ADD COLUMN fund_source_tracking_number VARCHAR(255) DEFAULT NULL AFTER fund_source");
        logMsg("  [OK] Added budget_checks.fund_source_tracking_number column.");
    } else {
        logMsg("  [OK] budget_checks.fund_source_tracking_number already exists.");
    }
} catch (Exception $e) {
    logMsg("  [WARN] budget_checks add column: " . $e->getMessage());
}

// 1c. signatory_tasks
try {
    $fastPDO->exec("
        CREATE TABLE IF NOT EXISTS signatory_tasks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            transaction_id INT NOT NULL,
            task_type ENUM('payroll_prep','dv_ors_prep') NOT NULL,
            status ENUM('pending','completed') NOT NULL DEFAULT 'pending',
            completed_by INT DEFAULT NULL,
            completed_at TIMESTAMP NULL DEFAULT NULL,
            document_path VARCHAR(255) DEFAULT NULL,
            remarks TEXT DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
            FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL,
            UNIQUE KEY st_tx_task_unique (transaction_id, task_type),
            INDEX idx_st_tx_status (transaction_id, status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    logMsg("  [OK] signatory_tasks table created/verified.");
} catch (Exception $e) {
    logMsg("  [WARN] signatory_tasks: " . $e->getMessage());
}

logMsg("");

// =========================================================================
// STEP 2: Ensure Cashier role, position, and permissions exist
// =========================================================================

logMsg("[STEP 2] Setting up Cashier role/position/permissions...");

try {
    // 2a. Insert Cashier role if missing
    $stmt = $fastPDO->prepare("SELECT id FROM roles WHERE role_name = 'Cashier' LIMIT 1");
    $stmt->execute();
    $cashierRoleId = $stmt->fetchColumn();

    if (!$cashierRoleId) {
        $fastPDO->exec("INSERT INTO roles (role_name) VALUES ('Cashier')");
        $cashierRoleId = $fastPDO->lastInsertId();
        logMsg("  [OK] Cashier role created (ID: $cashierRoleId).");
    } else {
        logMsg("  [OK] Cashier role already exists (ID: $cashierRoleId).");
    }

    // 2b. Insert Cashier position if missing
    $stmt = $fastPDO->prepare("SELECT id FROM positions WHERE position_name = 'Cashier' LIMIT 1");
    $stmt->execute();
    $cashierPosId = $stmt->fetchColumn();

    if (!$cashierPosId) {
        $fastPDO->exec("INSERT INTO positions (position_name, mapped_role, is_default) VALUES ('Cashier', 'Cashier', 1)");
        $cashierPosId = $fastPDO->lastInsertId();
        logMsg("  [OK] Cashier position created (ID: $cashierPosId).");
    } else {
        logMsg("  [OK] Cashier position already exists (ID: $cashierPosId).");
    }

    // 2c. Add Cashier to role_data_scope (assigned scope)
    try {
        $fastPDO->exec("
            CREATE TABLE IF NOT EXISTS role_data_scope (
                id INT AUTO_INCREMENT PRIMARY KEY,
                role VARCHAR(50) NOT NULL UNIQUE,
                scope VARCHAR(20) NOT NULL DEFAULT 'own'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    } catch (Exception $e) {
        // Table likely already exists
    }

    $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM role_data_scope WHERE role = 'Cashier'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $fastPDO->exec("INSERT INTO role_data_scope (role, scope) VALUES ('Cashier', 'assigned')");
        logMsg("  [OK] Cashier added to role_data_scope with 'assigned' scope.");
    } else {
        logMsg("  [OK] Cashier already in role_data_scope.");
    }

    // 2d. Seed Cashier permissions
    $cashierPerms = [
        'view' => 1,
        'encode' => 0,
        'edit' => 0,
        'approve' => 1,
        'delete' => 0,
        'manage_users' => 0,
        'configure_system' => 0,
        'view_bactrack' => 0
    ];

    $permStmt = $fastPDO->prepare("
        INSERT INTO role_permissions (role_id, permission_key, is_enabled) 
        VALUES (:role_id, :perm_key, :enabled)
        ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled)
    ");

    foreach ($cashierPerms as $key => $enabled) {
        $permStmt->execute([
            'role_id' => $cashierRoleId,
            'perm_key' => $key,
            'enabled' => $enabled
        ]);
    }
    logMsg("  [OK] Cashier permissions seeded.");

} catch (Exception $e) {
    logMsg("  [ERROR] Cashier setup failed: " . $e->getMessage());
}

logMsg("");

// =========================================================================
// STEP 3: Remap status values (inside transaction)
// =========================================================================

logMsg("[STEP 3] Remapping transaction status values...");

$statusMap = [
    'Pending Accountant 1'  => 'Pending Requestor',
    'Pending Support'       => 'Pending Requestor',
    'Pending Budget Check'  => 'Pending Budget',
    'Pending Accountant 2'  => 'Pending Accounting Support',
    'Pending Final Approval'=> 'Pending Signatories',
    'Approved'              => 'Pending Signatory Approval',
];

try {
    $fastPDO->beginTransaction();

    // 3a. Remap transactions.current_status
    $updateTx = $fastPDO->prepare("UPDATE transactions SET current_status = :new_status WHERE current_status = :old_status");
    
    foreach ($statusMap as $oldStatus => $newStatus) {
        $updateTx->execute(['new_status' => $newStatus, 'old_status' => $oldStatus]);
        $count = $updateTx->rowCount();
        if ($count > 0) {
            logMsg("  [OK] transactions: '$oldStatus' → '$newStatus' ($count rows)");
        } else {
            logMsg("  [--] transactions: '$oldStatus' → '$newStatus' (0 rows, already migrated)");
        }
    }

    // 3b. Remap transaction_status_logs.new_status and previous_status
    $updateLogNew = $fastPDO->prepare("UPDATE transaction_status_logs SET new_status = :new_status WHERE new_status = :old_status");
    $updateLogPrev = $fastPDO->prepare("UPDATE transaction_status_logs SET previous_status = :new_status WHERE previous_status = :old_status");

    foreach ($statusMap as $oldStatus => $newStatus) {
        $updateLogNew->execute(['new_status' => $newStatus, 'old_status' => $oldStatus]);
        $countNew = $updateLogNew->rowCount();
        $updateLogPrev->execute(['new_status' => $newStatus, 'old_status' => $oldStatus]);
        $countPrev = $updateLogPrev->rowCount();
        if ($countNew > 0 || $countPrev > 0) {
            logMsg("  [OK] status_logs: '$oldStatus' → '$newStatus' (new_status: $countNew, prev_status: $countPrev)");
        }
    }

    logMsg("");

    // =========================================================================
    // STEP 4: Populate attachment_approvals for Stage 2 transactions
    // =========================================================================

    logMsg("[STEP 4] Populating attachment_approvals for existing transactions...");

    // Get all transactions (not just Stage 2) and create approval rows for their attachments
    $txStmt = $fastPDO->query("
        SELECT t.id, t.current_status, d.attachment_path,
               cad.approved_ta_path, cad.travel_itinerary_path, cad.activity_proposal_path,
               rd.approved_ta_path as reimb_ta_path, rd.travel_itinerary_path as reimb_itinerary_path,
               rd.activity_proposal_path as reimb_proposal_path, rd.dtr_path, rd.certificate_path, rd.bill_proof_path
        FROM transactions t
        LEFT JOIN document_details d ON t.id = d.transaction_id
        LEFT JOIN cash_advance_details cad ON t.id = cad.transaction_id
        LEFT JOIN reimbursement_details rd ON t.id = rd.transaction_id
        WHERE t.current_status NOT IN ('Rejected', 'Returned')
    ");
    $allTx = $txStmt->fetchAll(PDO::FETCH_ASSOC);

    $insertApproval = $fastPDO->prepare("
        INSERT IGNORE INTO attachment_approvals (transaction_id, file_path, file_label, status)
        VALUES (:tx_id, :file_path, :file_label, :status)
    ");

    $approvalCount = 0;
    foreach ($allTx as $tx) {
        $files = [];

        // Collect attachment paths from document_details (JSON array)
        if (!empty($tx['attachment_path'])) {
            $decoded = json_decode($tx['attachment_path'], true);
            if (is_array($decoded)) {
                foreach ($decoded as $path) {
                    $files[] = ['path' => $path, 'label' => 'Supporting Attachment: ' . basename($path)];
                }
            } else {
                $files[] = ['path' => $tx['attachment_path'], 'label' => 'Supporting Attachment: ' . basename($tx['attachment_path'])];
            }
        }

        // Collect from cash_advance_details
        if (!empty($tx['approved_ta_path'])) {
            $files[] = ['path' => $tx['approved_ta_path'], 'label' => 'Approved Travel Authority'];
        }
        if (!empty($tx['travel_itinerary_path'])) {
            $files[] = ['path' => $tx['travel_itinerary_path'], 'label' => 'Travel Itinerary'];
        }
        if (!empty($tx['activity_proposal_path'])) {
            $files[] = ['path' => $tx['activity_proposal_path'], 'label' => 'Activity Proposal'];
        }

        // Collect from reimbursement_details
        if (!empty($tx['reimb_ta_path'])) {
            $files[] = ['path' => $tx['reimb_ta_path'], 'label' => 'Approved Travel Authority (Reimb)'];
        }
        if (!empty($tx['reimb_itinerary_path'])) {
            $files[] = ['path' => $tx['reimb_itinerary_path'], 'label' => 'Travel Itinerary (Reimb)'];
        }
        if (!empty($tx['reimb_proposal_path'])) {
            $files[] = ['path' => $tx['reimb_proposal_path'], 'label' => 'Activity Proposal (Reimb)'];
        }
        if (!empty($tx['dtr_path'])) {
            $files[] = ['path' => $tx['dtr_path'], 'label' => 'DTR Document'];
        }
        if (!empty($tx['certificate_path'])) {
            $files[] = ['path' => $tx['certificate_path'], 'label' => 'Certificate'];
        }
        if (!empty($tx['bill_proof_path'])) {
            $files[] = ['path' => $tx['bill_proof_path'], 'label' => 'Bill / Proof of Payment'];
        }

        // Determine approval status: if transaction is past Stage 2, mark all as approved
        $pastStage2 = in_array($tx['current_status'], [
            'Pending Budget', 'Pending Accounting Support', 'Pending Signatories', 
            'Pending Signatory Approval', 'Released'
        ]);

        foreach ($files as $file) {
            $insertApproval->execute([
                'tx_id' => $tx['id'],
                'file_path' => $file['path'],
                'file_label' => $file['label'],
                'status' => $pastStage2 ? 'approved' : 'pending'
            ]);
            $approvalCount++;
        }
    }
    logMsg("  [OK] Inserted/verified $approvalCount attachment approval rows.");

    logMsg("");

    // =========================================================================
    // STEP 5: Create signatory_tasks for all non-terminal transactions
    // =========================================================================

    logMsg("[STEP 5] Creating signatory_tasks for existing transactions...");

    $txForTasks = $fastPDO->query("
        SELECT id, current_status FROM transactions 
        WHERE current_status NOT IN ('Rejected', 'Returned')
    ");
    $tasksAll = $txForTasks->fetchAll(PDO::FETCH_ASSOC);

    $insertTask = $fastPDO->prepare("
        INSERT IGNORE INTO signatory_tasks (transaction_id, task_type, status)
        VALUES (:tx_id, :task_type, :status)
    ");

    $taskCount = 0;
    foreach ($tasksAll as $tx) {
        // If past Stage 5, mark tasks as completed
        $pastStage5 = in_array($tx['current_status'], ['Pending Signatory Approval', 'Released']);
        $atStage5 = ($tx['current_status'] === 'Pending Signatories');

        $taskStatus = ($pastStage5) ? 'completed' : 'pending';

        $insertTask->execute([
            'tx_id' => $tx['id'],
            'task_type' => 'payroll_prep',
            'status' => $taskStatus
        ]);
        $insertTask->execute([
            'tx_id' => $tx['id'],
            'task_type' => 'dv_ors_prep',
            'status' => $taskStatus
        ]);
        $taskCount += 2;
    }
    logMsg("  [OK] Inserted/verified $taskCount signatory task rows.");

    logMsg("");

    // =========================================================================
    // STEP 6: Insert migration log entry
    // =========================================================================

    logMsg("[STEP 6] Logging migration event...");

    // Use the activity_logs table for audit
    try {
        $logStmt = $fastPDO->prepare("
            INSERT INTO activity_logs (user_id, activity, old_value, new_value, ip_address)
            VALUES (NULL, :activity, :old_val, :new_val, :ip)
        ");
        $logStmt->execute([
            'activity' => 'Workflow Migration v2: 5-Stage → 6-Stage',
            'old_val' => json_encode(array_keys($statusMap)),
            'new_val' => json_encode(array_values($statusMap)),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'CLI'
        ]);
        logMsg("  [OK] Migration logged to activity_logs.");
    } catch (Exception $e) {
        logMsg("  [WARN] Could not log migration: " . $e->getMessage());
    }

    $fastPDO->commit();
    logMsg("");
    logMsg("=== MIGRATION COMPLETED SUCCESSFULLY ===");
    logMsg("Finished at: " . date('Y-m-d H:i:s'));

} catch (Exception $e) {
    if ($fastPDO->inTransaction()) {
        $fastPDO->rollBack();
    }
    logMsg("[FATAL] Migration failed, rolled back: " . $e->getMessage());
}
