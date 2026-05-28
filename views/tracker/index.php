<?php
/**
 * Transaction Progress Tracker Timeline View for SDO FAST.
 */

$currentPage = 'tracker';
$pageTitle = 'Progress Tracker';
$pageHeader = 'Progress Tracker';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

$searchQuery = trim($_GET['tracking'] ?? '');
$transaction = null;
$logs = [];
$errorMsg = null;

if (!empty($searchQuery) && $fastPDO !== null) {
    try {
        // Fetch transaction details
        $stmt = $fastPDO->prepare("
            SELECT t.*, u.full_name as requestor_name, u.email as requestor_email, 
                   d.dv_number, d.bir_2307_number, d.tax_type, d.attachment_path
            FROM transactions t
            LEFT JOIN users u ON t.requestor_id = u.id
            LEFT JOIN document_details d ON t.id = d.transaction_id
            WHERE t.tracking_number = :tracking
            LIMIT 1
        ");
        $stmt->execute(['tracking' => $searchQuery]);
        $transaction = $stmt->fetch();

        if ($transaction) {
            // Fetch status logs
            $logStmt = $fastPDO->prepare("
                SELECT l.*, u.full_name as changer_name, u.email as changer_email, r.role_name as changer_role
                FROM transaction_status_logs l
                LEFT JOIN users u ON l.changed_by = u.id
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE l.transaction_id = :id
                ORDER BY l.created_at ASC
            ");
            $logStmt->execute(['id' => $transaction['id']]);
            $logs = $logStmt->fetchAll();
        } else {
            $errorMsg = "No transaction record matches tracking number: '" . htmlspecialchars($searchQuery) . "'.";
        }
    } catch (PDOException $e) {
        error_log("Tracker database failure: " . $e->getMessage());
        $errorMsg = "A database query error occurred while searching.";
    }
}
?>

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <!-- Tracker Search Panel -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" action="" class="row g-2">
                    <div class="col-12 col-md-9">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                            <input type="text" name="tracking" class="form-control border-start-0" placeholder="Enter SDO FAST Tracking Number (e.g. FAST-2026-000001)..." value="<?php echo htmlspecialchars($searchQuery); ?>" required autocomplete="off">
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <button type="submit" class="btn btn-primary w-100 justify-content-center">Track Transaction</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($errorMsg): ?>
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                <div><?php echo $errorMsg; ?></div>
            </div>
        <?php endif; ?>

        <?php if ($transaction): 
            // Determine active step index to highlight timeline
            $statusList = ['Pending Support', 'Pending Accountant', 'Pending Final Approval', 'Approved'];
            if ($transaction['current_status'] === 'Rejected') {
                $statusList[3] = 'Rejected';
            } elseif ($transaction['current_status'] === 'Returned') {
                $statusList[3] = 'Returned';
            }
            
            $currentStatus = $transaction['current_status'];
            $activeStepIdx = array_search($currentStatus, $statusList);
            if ($activeStepIdx === false) {
                $activeStepIdx = 0; // Fallback
            }
        ?>
            <!-- Transaction Details Summary Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary-dark">Tracking Details - <?php echo htmlspecialchars($transaction['tracking_number']); ?></h5>
                    <span class="badge badge-status <?php 
                        switch($currentStatus) {
                            case 'Approved': echo 'bg-success'; break;
                            case 'Rejected': echo 'bg-danger'; break;
                            case 'Returned': echo 'bg-dark'; break;
                            default: echo 'bg-warning text-dark'; break;
                        }
                    ?> py-2 px-3"><?php echo htmlspecialchars($currentStatus); ?></span>
                </div>
                <div class="card-body">
                    <div class="row g-3 fs-8">
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Particulars / Event Name</span>
                            <strong class="fs-7 text-dark"><?php echo htmlspecialchars($transaction['event_name']); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Disbursement Type</span>
                            <strong class="text-dark"><?php echo htmlspecialchars($transaction['transaction_type']); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Submitted By</span>
                            <strong class="text-dark"><?php echo htmlspecialchars($transaction['requestor_name']); ?></strong>
                            <small class="text-muted d-block fs-9"><?php echo htmlspecialchars($transaction['requestor_email']); ?></small>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Gross Amount</span>
                            <strong class="fs-7 text-dark">₱<?php echo number_format($transaction['amount'], 2); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Tax Deduction (<?php echo htmlspecialchars($transaction['tax_type'] ?: 'Goods'); ?>)</span>
                            <strong class="text-danger">₱<?php echo number_format($transaction['tax_amount'], 2); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Net Payout Amount</span>
                            <strong class="fs-6 text-primary-dark">₱<?php echo number_format($transaction['net_amount'], 2); ?></strong>
                        </div>
                        
                        <hr class="my-3">
                        
                        <!-- Document Specifics -->
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Disbursement Voucher (DV) No.</span>
                            <strong class="text-dark"><?php echo htmlspecialchars($transaction['dv_number'] ?: 'Not Assigned'); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">BIR 2307 Ref No.</span>
                            <strong class="text-dark"><?php echo htmlspecialchars($transaction['bir_2307_number'] ?: 'Not Assigned'); ?></strong>
                        </div>
                        <div class="col-12 col-sm-6 col-md-4">
                            <span class="text-muted d-block text-uppercase fw-semibold">Supporting Attachment</span>
                            <?php if ($transaction['attachment_path']): ?>
                                <a href="<?php echo env('APP_URL') . '/' . htmlspecialchars($transaction['attachment_path']); ?>" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-3 mt-1 d-inline-flex align-items-center gap-2">
                                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                                    <span>Download Attachment</span>
                                </a>
                            <?php else: ?>
                                <span class="text-muted d-block fs-8 mt-1"><i class="bi bi-file-earmark-excel"></i> No files uploaded</span>
                            <?php endif; ?>
                        </div>

                        <!-- BAC Reference info if synched -->
                        <?php if ($transaction['bac_reference_number']): ?>
                            <div class="col-12 mt-2">
                                <div class="p-3 rounded-3 bg-light d-flex align-items-center gap-3">
                                    <i class="bi bi-hdd-network-fill text-accent fs-3"></i>
                                    <div>
                                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.65rem;">SDO Enterprise Sync</small>
                                        <span class="fs-8">Linked to SDO-BAC Procurement: <strong><?php echo htmlspecialchars($transaction['bac_reference_number']); ?></strong> (Procurement Type: <?php echo htmlspecialchars($transaction['bac_procurement_type'] ?: 'N/A'); ?>)</span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Workflow Timeline Tracking -->
            <div class="card shadow-sm border-0 mb-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0 fw-bold text-primary-dark">Workflow Auditing Timeline</h5>
                </div>
                <div class="card-body">
                    <!-- Timeline wrapper -->
                    <div class="position-relative py-3" style="padding-left: 45px;">
                        
                        <!-- Vertical line background -->
                        <div class="position-absolute" style="left: 17px; top: 0; bottom: 0; width: 4px; background-color: #e2e8f0; z-index: 1;"></div>
                        
                        <!-- Timeline items loop -->
                        <?php 
                        $knownSteps = [
                            'Pending Support' => [
                                'title' => 'Disbursement Request Submitted',
                                'subtitle' => 'Initial Document Receipt',
                                'role' => 'Requestor'
                            ],
                            'Pending Accountant' => [
                                'title' => 'Accounting Support Verified',
                                'subtitle' => 'Staff verification checks complete, forwarded to Accountant',
                                'role' => 'Accounting Staff'
                            ],
                            'Pending Final Approval' => [
                                'title' => 'Accountant Checks Completed',
                                'subtitle' => 'Tax details reviewed, forwarded to Approver',
                                'role' => 'Budget Officer / Accountant'
                            ],
                            'Approved' => [
                                'title' => 'Final Disbursement Approved',
                                'subtitle' => 'Payment approved and released',
                                'role' => 'Financial Approver'
                            ],
                            'Rejected' => [
                                'title' => 'Disbursement Request Rejected',
                                'subtitle' => 'Request denied by audit staff',
                                'role' => 'System Reviewer'
                            ],
                            'Returned' => [
                                'title' => 'Returned to Requestor',
                                'subtitle' => 'Returned for corrections or missing attachments',
                                'role' => 'System Reviewer'
                            ]
                        ];
                        
                        // Map existing database logs to steps to render them
                        $renderedStepsCount = 0;
                        foreach ($logs as $logIdx => $log):
                            $logStatus = $log['new_status'];
                            $stepMeta = $knownSteps[$logStatus] ?? [
                                'title' => 'Workflow Status Changed',
                                'subtitle' => 'Status changed',
                                'role' => 'Staff'
                            ];
                            
                            $dateStr = date('M d, Y h:i A', strtotime($log['created_at']));
                            
                            // Determine status indicator color
                            $nodeBg = 'var(--color-primary)';
                            $nodeIcon = '<i class="bi bi-check text-white"></i>';
                            
                            if ($logStatus === 'Approved') {
                                $nodeBg = '#28a745';
                                $nodeIcon = '<i class="bi bi-check2-all text-white fs-5"></i>';
                            } elseif ($logStatus === 'Rejected') {
                                $nodeBg = '#dc3545';
                                $nodeIcon = '<i class="bi bi-x text-white fs-5"></i>';
                            } elseif ($logStatus === 'Returned') {
                                $nodeBg = '#6c757d';
                                $nodeIcon = '<i class="bi bi-arrow-left text-white fs-5"></i>';
                            }
                            
                            $isLast = ($logIdx === count($logs) - 1);
                        ?>
                            <div class="mb-4 position-relative" style="z-index: 2;">
                                <!-- Circle Marker -->
                                <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center" 
                                     style="left: -40px; top: 0; width: 28px; height: 28px; background-color: <?php echo $nodeBg; ?>; box-shadow: 0 0 0 6px #ffffff; z-index: 3;">
                                    <?php echo $nodeIcon; ?>
                                </div>
                                
                                <!-- Card content bubble -->
                                <div class="card p-3 mb-0 bg-white border border-light shadow-sm" style="margin-left: 5px;">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-1">
                                        <h6 class="mb-0 fw-bold text-primary-dark"><?php echo htmlspecialchars($stepMeta['title']); ?></h6>
                                        <small class="text-muted"><i class="bi bi-clock me-1"></i><?php echo $dateStr; ?></small>
                                    </div>
                                    <small class="text-primary-light fw-medium d-block mb-2">Processed By: <?php echo htmlspecialchars($log['changer_name'] ?: 'System Event'); ?> (<?php echo htmlspecialchars($log['changer_role'] ?: $stepMeta['role']); ?>)</small>
                                    
                                    <p class="text-muted mb-0 fs-8 p-2 rounded-2 bg-light border-start border-3" style="border-left-color: var(--color-primary) !important;">
                                        <strong>Remarks:</strong> <?php echo htmlspecialchars($log['remarks'] ?: 'No workflow audit remarks recorded.'); ?>
                                    </p>
                                </div>
                            </div>
                        <?php 
                        $renderedStepsCount++;
                        endforeach; 
                        ?>
                        
                        <!-- RENDER REMAINING PENDING STEPS (If transaction is not yet final) -->
                        <?php 
                        if (!in_array($currentStatus, ['Approved', 'Rejected', 'Returned'])) {
                            // Find which steps are still pending
                            $allExpectedSteps = ['Pending Support', 'Pending Accountant', 'Pending Final Approval', 'Approved'];
                            
                            // Find index of current state
                            $currIdx = array_search($currentStatus, $allExpectedSteps);
                            
                            if ($currIdx !== false) {
                                for ($i = $currIdx + 1; $i < count($allExpectedSteps); $i++) {
                                    $pendingStatus = $allExpectedSteps[$i];
                                    $stepMeta = $knownSteps[$pendingStatus];
                                    ?>
                                    <div class="mb-4 position-relative" style="z-index: 2; opacity: 0.5;">
                                        <!-- Circle Marker -->
                                        <div class="position-absolute rounded-circle d-flex align-items-center justify-content-center bg-secondary" 
                                             style="left: -40px; top: 0; width: 28px; height: 28px; box-shadow: 0 0 0 6px #ffffff; z-index: 3;">
                                            <i class="bi bi-dash text-white"></i>
                                        </div>
                                        
                                        <!-- Card content bubble -->
                                        <div class="card p-3 mb-0 bg-white border border-light" style="margin-left: 5px;">
                                            <h6 class="mb-1 fw-semibold text-muted"><?php echo htmlspecialchars($stepMeta['title']); ?> (Pending)</h6>
                                            <small class="text-muted d-block"><?php echo htmlspecialchars($stepMeta['subtitle']); ?></small>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Initial Empty State -->
            <?php if (empty($searchQuery)): ?>
                <div class="card shadow-sm border-0 text-center py-5">
                    <div class="card-body">
                        <div class="text-primary-light mb-3">
                            <i class="bi bi-geo-alt-fill" style="font-size: 4rem;"></i>
                        </div>
                        <h4 class="fw-bold text-primary-dark">Disbursement Progress Tracker</h4>
                        <p class="text-muted mx-auto" style="max-width: 460px;">Provide a sequential FAST tracking code (e.g. <code>FAST-YYYY-000001</code>) in the search query above to review the real-time workflow audits and transaction approvals.</p>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
