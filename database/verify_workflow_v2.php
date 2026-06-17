<?php
/**
 * Verification script for Workflow Migration v2.
 * Checks that all tables, statuses, and data are correctly migrated.
 */

if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/plain; charset=utf-8');
}

require_once __DIR__ . '/../config/database.php';

if ($fastPDO === null) {
    die("[FATAL] Database connection failed.\n");
}

$errors = 0;
$warnings = 0;
$passes = 0;

function check($condition, $passMsg, $failMsg) {
    global $errors, $passes;
    if ($condition) {
        echo "[PASS] $passMsg\n";
        $passes++;
    } else {
        echo "[FAIL] $failMsg\n";
        $errors++;
    }
}

function warn($msg) {
    global $warnings;
    echo "[WARN] $msg\n";
    $warnings++;
}

echo "=== FAST Workflow v2 — Verification ===\n\n";

// 1. Check new tables exist
echo "--- Table Structure ---\n";
$tables = ['attachment_approvals', 'budget_checks', 'signatory_tasks'];
foreach ($tables as $table) {
    try {
        $fastPDO->query("SELECT 1 FROM $table LIMIT 1");
        check(true, "Table '$table' exists.", "");
    } catch (Exception $e) {
        check(false, "", "Table '$table' does NOT exist.");
    }
}

echo "\n--- Old Status Values ---\n";
$oldStatuses = ['Pending Accountant 1', 'Pending Support', 'Pending Budget Check', 'Pending Accountant 2', 'Pending Final Approval', 'Approved'];
foreach ($oldStatuses as $old) {
    $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status = ?");
    $stmt->execute([$old]);
    $count = $stmt->fetchColumn();
    check($count == 0, "No transactions with old status '$old'.", "$count transactions still have old status '$old'.");
}

echo "\n--- New Status Values ---\n";
$newStatuses = ['Pending ACCTG Support', 'Pending Budget', 'Pending ACCT Support', 'Pending Signatories', 'Pending Cashier Release', 'Released', 'Rejected', 'Returned'];
foreach ($newStatuses as $new) {
    $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transactions WHERE current_status = ?");
    $stmt->execute([$new]);
    $count = $stmt->fetchColumn();
    echo "[INFO] Status '$new': $count transactions\n";
}

echo "\n--- Cashier Setup ---\n";
$stmt = $fastPDO->prepare("SELECT id FROM roles WHERE role_name = 'Cashier' LIMIT 1");
$stmt->execute();
check($stmt->fetchColumn() > 0, "Cashier role exists.", "Cashier role MISSING.");

$stmt = $fastPDO->prepare("SELECT id FROM positions WHERE position_name = 'Cashier' LIMIT 1");
$stmt->execute();
check($stmt->fetchColumn() > 0, "Cashier position exists.", "Cashier position MISSING.");

$stmt = $fastPDO->prepare("SELECT COUNT(*) FROM role_permissions rp JOIN roles r ON rp.role_id = r.id WHERE r.role_name = 'Cashier'");
$stmt->execute();
check($stmt->fetchColumn() > 0, "Cashier permissions seeded.", "Cashier permissions MISSING.");

echo "\n--- Attachment Approvals ---\n";
$stmt = $fastPDO->query("SELECT COUNT(*) FROM attachment_approvals");
$totalAA = $stmt->fetchColumn();
echo "[INFO] Total attachment_approvals rows: $totalAA\n";

$stmt = $fastPDO->query("SELECT COUNT(*) FROM attachment_approvals WHERE status = 'pending'");
$pendingAA = $stmt->fetchColumn();
echo "[INFO] Pending approvals: $pendingAA\n";

$stmt = $fastPDO->query("SELECT COUNT(*) FROM attachment_approvals WHERE status = 'approved'");
$approvedAA = $stmt->fetchColumn();
echo "[INFO] Approved: $approvedAA\n";

echo "\n--- Signatory Tasks ---\n";
$stmt = $fastPDO->query("SELECT COUNT(*) FROM signatory_tasks");
$totalST = $stmt->fetchColumn();
echo "[INFO] Total signatory_tasks rows: $totalST\n";

$stmt = $fastPDO->query("SELECT COUNT(*) FROM signatory_tasks WHERE status = 'pending'");
echo "[INFO] Pending tasks: " . $stmt->fetchColumn() . "\n";

$stmt = $fastPDO->query("SELECT COUNT(*) FROM signatory_tasks WHERE status = 'completed'");
echo "[INFO] Completed tasks: " . $stmt->fetchColumn() . "\n";

echo "\n--- Status Logs Consistency ---\n";
foreach ($oldStatuses as $old) {
    $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transaction_status_logs WHERE new_status = ? OR previous_status = ?");
    $stmt->execute([$old, $old]);
    $count = $stmt->fetchColumn();
    check($count == 0, "No status logs reference old status '$old'.", "$count status log entries still reference '$old'.");
}

echo "\n=== RESULTS ===\n";
echo "Passed: $passes | Failed: $errors | Warnings: $warnings\n";
echo ($errors === 0) ? "ALL CHECKS PASSED.\n" : "SOME CHECKS FAILED — review output above.\n";
