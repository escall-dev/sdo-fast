
<?php
/**
 * Workflow Migration v3: Reorder workflow stages per new timeline.
 * 
 * IDEMPOTENT — Safe to re-run.
 *
 * Changes from v2 → v3:
 *   - Budget verification moves from Stage 3 to Stage 2 (immediately after submission)
 *   - Requestor submits documents only AFTER budget approval (new Stage 3 loop-back)
 *   - Two Accounting Support stages consolidated into one
 *   - New display labels for all stages
 *
 * Database status values renamed for clarity (v3):
 *   Pending Budget           → Stage 1 (Source of Funds Verification)
 *   Pending Requestor        → Stage 2 (Source of Funds Verified)
 *   Pending Accounting Support → Stage 3 (Mandatory Documentary Requirements Submitted)
 *   Pending Signatories      → Stage 4 (Document Inspection)
 *   Pending Signatory Approval → Stage 5 (Document for Approval and Signature)
 *   Awaiting Payment         → Stage 6a (Release of Payment — pending)
 *   Released                 → Stage 6b (Payment Released — final)
 *
 * This migration only records the v3 activation. All status routing changes
 * are in the application code (APIs, config/auth.php, views).
 *
 * Run via CLI:  php database/migrate_workflow_v3.php
 * Run via web:  http://localhost/fast/database/migrate_workflow_v3.php
 */

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

require_once __DIR__ . '/../config/database.php';

if ($fastPDO === null) {
    die("[FATAL] Database connection failed.\n");
}

$log = [];
function logMsgV3($msg) {
    global $log;
    $log[] = $msg;
    echo $msg . "\n";
}

logMsgV3("=== FAST Workflow Migration v3 ===");
logMsgV3("Started at: " . date('Y-m-d H:i:s'));
logMsgV3("");

// =========================================================================
// STEP 1: Create migration log table if not exists
// =========================================================================
logMsgV3("[STEP 1] Ensuring migration tracking table exists...");

try {
    $fastPDO->exec("
        CREATE TABLE IF NOT EXISTS system_migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration_name VARCHAR(100) NOT NULL UNIQUE,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    logMsgV3("  [OK] system_migrations table verified.");
} catch (Exception $e) {
    logMsgV3("  [WARN] system_migrations: " . $e->getMessage());
}

// =========================================================================
// STEP 2: Record this migration
// =========================================================================
logMsgV3("[STEP 2] Recording workflow v3 migration...");

try {
    $stmt = $fastPDO->prepare("
        INSERT IGNORE INTO system_migrations (migration_name) VALUES ('workflow_v3')
    ");
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        logMsgV3("  [OK] Workflow v3 migration recorded (first run).");
    } else {
        logMsgV3("  [OK] Workflow v3 migration already recorded (re-run).");
    }
} catch (Exception $e) {
    logMsgV3("  [WARN] Failed to record migration: " . $e->getMessage());
}

// =========================================================================
// STEP 3: Rename existing transaction statuses (v3 clear naming)
// =========================================================================
logMsgV3("");
logMsgV3("[STEP 3] Renaming existing transaction statuses for clarity...");

$renameMap = [
    'Pending ACCTG Support' => 'Pending Requestor',
    'Pending ACCT Support' => 'Pending Accounting Support',
    'Pending Cashier Release' => 'Pending Signatory Approval',
    'Pending Signatories' => 'Pending Signatory Approval',
];

foreach ($renameMap as $old => $new) {
    // Update transactions table
    $txStmt = $fastPDO->prepare("UPDATE transactions SET current_status = :new WHERE current_status = :old");
    $txStmt->execute(['new' => $new, 'old' => $old]);
    $txAffected = $txStmt->rowCount();
    
    // Update status logs (previous_status)
    $logOldStmt = $fastPDO->prepare("UPDATE transaction_status_logs SET previous_status = :new WHERE previous_status = :old");
    $logOldStmt->execute(['new' => $new, 'old' => $old]);
    $logOldAffected = $logOldStmt->rowCount();
    
    // Update status logs (new_status)
    $logNewStmt = $fastPDO->prepare("UPDATE transaction_status_logs SET new_status = :new WHERE new_status = :old");
    $logNewStmt->execute(['new' => $new, 'old' => $old]);
    $logNewAffected = $logNewStmt->rowCount();
    
    logMsgV3("  {$old} → {$new}: {$txAffected} tx, {$logOldAffected} prev_logs, {$logNewAffected} new_logs");
}

// =========================================================================
// STEP 4: Verify all required status values exist in data
// =========================================================================
logMsgV3("");
logMsgV3("[STEP 3] Verifying transaction statuses...");

$expectedStatuses = [
    'Pending Requestor',
    'Pending Budget',
    'Pending Accounting Support',
    'Pending Signatory Approval',
    'Released',
    'Rejected',
    'Returned'
];

$statusCounts = [];
foreach ($expectedStatuses as $status) {
    $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status = :s");
    $stmt->execute(['s' => $status]);
    $cnt = $stmt->fetchColumn();
    $statusCounts[$status] = $cnt;
    logMsgV3("  {$status}: {$cnt} transaction(s)");
}

logMsgV3("");
logMsgV3("=== Workflow v3 Migration Complete ===");
logMsgV3("Finished at: " . date('Y-m-d H:i:s'));
logMsgV3("");
logMsgV3("IMPORTANT: This migration only records the v3 activation.");
logMsgV3("All workflow routing changes are in the application code:");
logMsgV3("  - api/transactions/submit-transaction.php");
logMsgV3("  - api/transactions/budget-check.php");
logMsgV3("  - api/transactions/resubmit-documents.php (NEW)");
logMsgV3("  - api/transactions/update-status.php");
logMsgV3("  - api/transactions/approve-attachment.php");
logMsgV3("  - api/transactions/update-tax-classification.php");
logMsgV3("  - config/auth.php");
logMsgV3("  - views/transactions/submit.php");
logMsgV3("  - views/transactions/resubmit-documents.php (NEW)");
logMsgV3("  - views/transactions/index.php");
logMsgV3("  - views/tracker/index.php");
