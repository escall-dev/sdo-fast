<?php
/**
 * Enterprise Integration Monitor Dashboard View for SDO FAST.
 * Access restricted to Super Admin and Accounting Staff.
 */

$currentPage = 'integrations';
$pageTitle = 'Integration Monitor';
$pageHeader = 'Integration Monitor';

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';

$userRole = $_SESSION['user_role'] ?? '';

// Double check permission (Admin/Staff only)
if (!in_array($userRole, ['Super Admin', 'Accounting Staff'])) {
    $_SESSION['flash_error'] = 'Access denied: Integration Monitor is restricted to Super Admin and Accounting Staff.';
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

// Fetch stats and tokens
$totalSyncs = 0;
$failedSyncs = 0;
$successfulSyncs = 0;
$duplicateAttempts = 0;
$integrationTokens = [];
$integrationLogs = [];

if ($fastPDO !== null) {
    try {
        // Stats counts
        $totalSyncs = $fastPDO->query("SELECT COUNT(*) FROM integration_logs")->fetchColumn();
        $failedSyncs = $fastPDO->query("SELECT COUNT(*) FROM integration_logs WHERE sync_status = 'FAILED'")->fetchColumn();
        $successfulSyncs = $fastPDO->query("SELECT COUNT(*) FROM integration_logs WHERE sync_status = 'SUCCESS'")->fetchColumn();
        $duplicateAttempts = $fastPDO->query("SELECT COUNT(*) FROM integration_logs WHERE sync_status = 'DUPLICATE'")->fetchColumn();

        // System tokens
        $integrationTokens = $fastPDO->query("SELECT * FROM integration_tokens ORDER BY id ASC")->fetchAll();

        // Recent sync logs (Top 30)
        $integrationLogs = $fastPDO->query("
            SELECT * FROM integration_logs 
            ORDER BY synced_at DESC 
            LIMIT 30
        ")->fetchAll();
    } catch (PDOException $e) {
        error_log("Failed to load integration monitor logs: " . $e->getMessage());
    }
}
?>

<!-- Integration Stats Row -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Sync Requests -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 mb-0 border-start border-primary border-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-8 fw-semibold text-uppercase">Total Sync Logs</span>
                    <h3 class="fw-bold mb-0 text-primary-dark mt-1"><?php echo number_format($totalSyncs); ?></h3>
                </div>
                <div class="bg-light-primary text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(27, 74, 154, 0.08);">
                    <i class="bi bi-arrow-down-up fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Successful Syncs -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 mb-0 border-start border-success border-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-8 fw-semibold text-uppercase">Successful Syncs</span>
                    <h3 class="fw-bold mb-0 text-success mt-1"><?php echo number_format($successfulSyncs); ?></h3>
                </div>
                <div class="bg-light-success text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(40, 167, 69, 0.08);">
                    <i class="bi bi-cloud-check fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Failed Syncs -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 mb-0 border-start border-danger border-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-8 fw-semibold text-uppercase">Failed Syncs</span>
                    <h3 class="fw-bold mb-0 text-danger mt-1" id="failedSyncsCount"><?php echo number_format($failedSyncs); ?></h3>
                </div>
                <div class="bg-light-danger text-danger rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(220, 53, 69, 0.08);">
                    <i class="bi bi-cloud-slash fs-4"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Duplicate Sync blocks -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 mb-0 border-start border-warning border-4">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fs-8 fw-semibold text-uppercase">Duplicate Blocks</span>
                    <h3 class="fw-bold mb-0 text-warning mt-1"><?php echo number_format($duplicateAttempts); ?></h3>
                </div>
                <div class="bg-light-warning text-warning rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: rgba(255, 193, 7, 0.08);">
                    <i class="bi bi-shield-exclamation fs-4"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Active Integration Keys -->
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm border-0 h-100 mb-0">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold text-primary-dark">Authorized Integration Clients</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive border-0">
                    <table class="table align-middle">
                        <thead>
                            <tr class="fs-9 text-uppercase text-muted">
                                <th>System Client</th>
                                <th>Token Hash Preview</th>
                                <th>State</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($integrationTokens as $token): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($token['system_name']); ?></strong>
                                    </td>
                                    <td>
                                        <code class="fs-9" title="<?php echo htmlspecialchars($token['token_hash']); ?>"><?php echo substr($token['token_hash'], 0, 16); ?>...</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-success rounded-pill px-2 fs-9">Active</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-3 bg-light border-top">
                    <small class="text-muted d-block fs-8"><i class="bi bi-info-circle me-1"></i> Bearer tokens are stored securely in the database as SHA-256 hashes to prevent credential leakage.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Integration Sync Logs -->
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm border-0 h-100 mb-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary-dark">Recent Synchronization Activity Logs</h5>
                <button type="button" class="btn btn-sm btn-outline-primary py-1 px-3" onclick="window.location.reload();">Refresh Logs</button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive border-0" style="max-height: 400px; overflow-y: auto;">
                    <table class="table align-middle table-hover">
                        <thead>
                            <tr class="fs-9 text-uppercase text-muted">
                                <th>Timestamp</th>
                                <th>Flow Direction</th>
                                <th>Event Type</th>
                                <th>Reference</th>
                                <th>Sync Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($integrationLogs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No synchronization logs found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($integrationLogs as $log): 
                                    $badge = 'bg-secondary';
                                    if ($log['sync_status'] === 'SUCCESS') $badge = 'bg-success';
                                    elseif ($log['sync_status'] === 'FAILED') $badge = 'bg-danger';
                                    elseif ($log['sync_status'] === 'DUPLICATE') $badge = 'bg-warning text-dark';
                                    elseif ($log['sync_status'] === 'INVALID_PAYLOAD') $badge = 'bg-dark';
                                ?>
                                    <tr id="sync_row_<?php echo $log['id']; ?>">
                                        <td class="text-muted fs-8"><?php echo date('M d, Y h:i A', strtotime($log['synced_at'])); ?></td>
                                        <td>
                                            <span class="fs-8">
                                                <strong><?php echo htmlspecialchars($log['source_system']); ?></strong> 
                                                <i class="bi bi-arrow-right text-primary px-1"></i> 
                                                <strong><?php echo htmlspecialchars($log['destination_system']); ?></strong>
                                            </span>
                                        </td>
                                        <td><small class="badge bg-light text-dark border"><?php echo htmlspecialchars($log['payload_type']); ?></small></td>
                                        <td><strong><?php echo htmlspecialchars($log['reference_id'] ?: '-'); ?></strong></td>
                                        <td><span class="badge badge-status <?php echo $badge; ?>"><?php echo htmlspecialchars($log['sync_status']); ?></span></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button class="btn btn-sm btn-light border py-1 px-2" onclick="showSyncPayload(<?php echo htmlspecialchars(json_encode($log)); ?>)" title="View Payload Details"><i class="bi bi-eye"></i></button>
                                                <?php if ($log['sync_status'] === 'FAILED'): ?>
                                                    <button class="btn btn-sm btn-primary py-1 px-2" id="retry_btn_<?php echo $log['id']; ?>" onclick="triggerManualRetry(<?php echo $log['id']; ?>)" title="Retry Synchronization"><i class="bi bi-arrow-repeat"></i> Retry</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: SYNC LOG DETAILS / PAYLOAD VIEW
     ========================================================================= -->
<div class="modal fade" id="payloadModal" tabindex="-1" aria-labelledby="payloadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-primary-dark" id="payloadModalLabel">Sync Payload / Error Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <span class="fs-8 text-muted text-uppercase d-block">Log Summary</span>
                    <div class="row g-2 p-3 bg-light rounded-3 mt-1 fs-8">
                        <div class="col-4"><strong>Source:</strong> <span id="modalSource">-</span></div>
                        <div class="col-4"><strong>Destination:</strong> <span id="modalDest">-</span></div>
                        <div class="col-4"><strong>Status:</strong> <span id="modalStatus">-</span></div>
                    </div>
                </div>
                <div class="mb-0">
                    <span class="fs-8 text-muted text-uppercase d-block mb-1">Payload / Response Content</span>
                    <pre class="bg-dark text-success p-3 rounded-3 fs-9 mb-0" style="max-height: 250px; overflow-y: auto;" id="modalRawPayload">{}</pre>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT RETRY SYNC
     ========================================================================= -->
<script>
function showSyncPayload(log) {
    document.getElementById('modalSource').innerText = log.source_system;
    document.getElementById('modalDest').innerText = log.destination_system;
    document.getElementById('modalStatus').innerText = log.sync_status;
    
    let displayContent = log.response_message;
    try {
        // Attempt formatting if it is JSON string
        const parsed = JSON.parse(log.response_message);
        displayContent = JSON.stringify(parsed, null, 4);
    } catch(e) {
        // Leave as plain text if it is an error log string
    }
    
    document.getElementById('modalRawPayload').innerText = displayContent || 'No payload recorded.';

    const modalEl = document.getElementById('payloadModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

async function triggerManualRetry(logId) {
    if (!await API.confirmAction("Confirm Retry", "Are you sure you want to retry this failed synchronization request?", "Yes, Retry")) return;

    const btn = document.getElementById('retry_btn_' + logId);
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-grow spinner-grow-sm" role="status"></span> Synching...';

    const payload = new FormData();
    payload.append('log_id', logId);

    const response = await fetch('<?php echo env('APP_URL'); ?>/api/integrations/send-to-bac.php', {
        method: 'POST',
        headers: { 'X-CSRF-Token': '<?php echo $_SESSION['csrf_token']; ?>' },
        body: payload
    });

    const data = await response.json();
    if (data.success) {
        API.showToast(data.message, 'success');
        
        // Reload row or stats dynamically or refresh page
        setTimeout(() => {
            window.location.reload();
        }, 1500);
    } else {
        API.showToast(data.message || 'Retry synchronization failed.', 'danger');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Retry';
    }
}
</script>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
?>
