<?php
/**
 * Bidirectional Integration Page for SDO FAST.
 * Synchronizes projects and transactions with SDO-BACtrack.
 * Access restricted to Super Admin and Accounting Staff.
 */

$currentPage = 'integrations_page';
$pageTitle = 'Integrations';
$pageHeader = 'Bidirectional synchronization with SDO-FAST';

ob_start();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
require_once __DIR__ . '/../../includes/sidebar.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../services/BacIntegrationService.php';
require_once __DIR__ . '/../../services/FastIntegrationService.php';
require_once __DIR__ . '/../../services/SyncLogService.php';

$userRole = $_SESSION['user_role'] ?? '';
$userPosition = $_SESSION['user_position'] ?? '';

// Restrict access to Super Admin and Accounting Staff
if (!in_array($userRole, ['Super Admin', 'Accounting Staff'])) {
    $_SESSION['flash_error'] = 'Access denied: Integrations page is restricted to Super Admin and Accounting Staff.';
    header('Location: ' . env('APP_URL') . '/views/dashboard/index.php');
    exit;
}

// Helper to check if a file exists in FAST and return its details
function getChecklistDocument(PDO $fastPDO, int $transactionId, string $category) {
    try {
        $stmt = $fastPDO->prepare("SELECT * FROM transaction_documents WHERE transaction_id = ? AND category = ? LIMIT 1");
        $stmt->execute([$transactionId, $category]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Exception $e) {
        return null;
    }
}

// Helpers for integration requests (uses native streams fallback to avoid php_curl extension dependency)
function fetchPendingFromBac() {
    global $bacPDO;
    $bacBaseUrl = '';
    
    // First, try the API call
    $bacApiUrl = env('BAC_API_URL', 'http://localhost/SDO-BACtrack/api/integrations/receive-fast.php');
    $bacBaseUrl = str_replace('/receive-fast.php', '', $bacApiUrl);
    $url = $bacBaseUrl . '/send-to-fast.php';
    $token = env('BAC_SYSTEM_TOKEN', 'bac_secure_token_123');

    $options = [
        'http' => [
            'method' => 'GET',
            'header' => "Authorization: Bearer " . $token . "\r\n" .
                        "Content-Type: application/json\r\n",
            'ignore_errors' => true,
            'timeout' => 6
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ];
    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    $httpCode = 500;
    if (isset($http_response_header) && is_array($http_response_header)) {
        if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/i', $http_response_header[0], $matches)) {
            $httpCode = (int)$matches[1];
        }
    }

    if ($httpCode === 200 && $response !== false) {
        $data = json_decode($response, true);
        if (!empty($data['projects'])) {
            foreach ($data['projects'] as &$project) {
                $project['bac_base_url'] = $bacBaseUrl;
            }
            unset($project);
            return $data['projects'];
        }
    }
    
    // Fallback: Query BACtrack database directly if connected
    if ($bacPDO !== null) {
        try {
            $stmt = $bacPDO->query("
                SELECT p.*, u.name as creator_name 
                FROM projects p 
                LEFT JOIN users u ON p.created_by = u.id 
                WHERE p.approval_status = 'APPROVED' 
                  AND p.fast_sync_status = 'PENDING'
                ORDER BY p.id DESC
            ");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $baseUrl = rtrim($bacBaseUrl ?: env('BAC_APP_URL', 'http://localhost/SDO-BACtrack'), '/');

            foreach ($projects as &$project) {
                $projectId = (int)($project['id'] ?? 0);
                $project['bac_base_url'] = $baseUrl;
                $project['documents'] = [];
                $project['approval_document'] = null;

                if ($projectId > 0) {
                    $docStmt = $bacPDO->prepare("SELECT category, file_path, original_name, file_size, uploaded_at FROM project_documents WHERE project_id = ? ORDER BY uploaded_at DESC");
                    $docStmt->execute([$projectId]);
                    $docs = $docStmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($docs as &$doc) {
                        $doc['file_url'] = !empty($doc['file_path']) ? ($baseUrl . '/uploads/' . ltrim($doc['file_path'], '/')) : null;
                    }
                    unset($doc);
                    $project['documents'] = $docs;

                    if (!empty($project['approval_file_path'])) {
                        $project['approval_document'] = [
                            'file_path' => $project['approval_file_path'],
                            'original_name' => basename($project['approval_file_path']),
                            'file_url' => $baseUrl . '/uploads/' . ltrim($project['approval_file_path'], '/')
                        ];
                    }
                }
            }
            unset($project);
            return $projects;
        } catch (Exception $e) {
            error_log("Fallback direct query of BACtrack approved projects failed: " . $e->getMessage());
        }
    }
    return [];
}

// Handle Form Submissions / Action triggers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Handle checklist document upload
    if ($action === 'upload_checklist_file' && $fastPDO !== null) {
        $transactionId = (int)($_POST['transaction_id'] ?? 0);
        $categorySlug = trim($_POST['category_slug'] ?? '');
        $validCategories = ['purchase_request', 'memorandum', 'activity_proposal', 'saro'];
        
        $tabParam = trim($_POST['active_tab'] ?? 'send');
        $redirectUrl = env('APP_URL') . '/views/integrations/integrations.php?tab=' . urlencode($tabParam);
        
        if ($transactionId > 0 && in_array($categorySlug, $validCategories, true)) {
            if (isset($_FILES['checklist_file']) && $_FILES['checklist_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['checklist_file'];
                if ($file['error'] !== UPLOAD_ERR_OK) {
                    $_SESSION['flash_error'] = 'File upload failed with error code: ' . $file['error'];
                } else {
                    $maxSize = 5 * 1024 * 1024; // 5MB
                    if ($file['size'] > $maxSize) {
                        $_SESSION['flash_error'] = 'File size exceeds maximum allowed (5MB).';
                    } else {
                        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                        if (!in_array($extension, $allowedExtensions, true)) {
                            $_SESSION['flash_error'] = 'Only PDF, JPG, and PNG files are allowed.';
                        } else {
                            $uploadDir = __DIR__ . '/../../uploads/procurement-docs/';
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0755, true);
                            }
                            
                            $timestamp = time();
                            $filename = "{$transactionId}_{$categorySlug}_{$timestamp}.{$extension}";
                            $filePath = 'uploads/procurement-docs/' . $filename;
                            $fullPath = $uploadDir . $filename;
                            
                            try {
                                $stmt = $fastPDO->prepare("SELECT id, file_path FROM transaction_documents WHERE transaction_id = ? AND category = ?");
                                $stmt->execute([$transactionId, $categorySlug]);
                                $existing = $stmt->fetch();
                                
                                if (move_uploaded_file($file['tmp_name'], $fullPath)) {
                                    if ($existing) {
                                        $oldFullPath = __DIR__ . '/../../' . $existing['file_path'];
                                        if (file_exists($oldFullPath)) {
                                            @unlink($oldFullPath);
                                        }
                                        $updateStmt = $fastPDO->prepare("UPDATE transaction_documents SET file_path = ?, original_name = ?, file_size = ?, uploaded_at = NOW() WHERE id = ?");
                                        $updateStmt->execute([$filePath, $file['name'], $file['size'], $existing['id']]);
                                    } else {
                                        $insertStmt = $fastPDO->prepare("INSERT INTO transaction_documents (transaction_id, category, file_path, original_name, file_size) VALUES (?, ?, ?, ?, ?)");
                                        $insertStmt->execute([$transactionId, $categorySlug, $filePath, $file['name'], $file['size']]);
                                    }
                                    $_SESSION['flash_success'] = 'Document uploaded successfully.';
                                } else {
                                    $_SESSION['flash_error'] = 'Failed to save uploaded file.';
                                }
                            } catch (Exception $e) {
                                $_SESSION['flash_error'] = 'Database error: ' . $e->getMessage();
                            }
                        }
                    }
                }
            } else {
                $_SESSION['flash_error'] = 'Please select a file.';
            }
        }
        header('Location: ' . $redirectUrl);
        exit;
    }

    // Handle checklist document deletion
    if ($action === 'delete_checklist_file' && $fastPDO !== null) {
        $transactionId = (int)($_POST['transaction_id'] ?? 0);
        $categorySlug = trim($_POST['category_slug'] ?? '');
        $validCategories = ['purchase_request', 'memorandum', 'activity_proposal', 'saro'];
        
        $tabParam = trim($_POST['active_tab'] ?? 'send');
        $redirectUrl = env('APP_URL') . '/views/integrations/integrations.php?tab=' . urlencode($tabParam);
        
        if ($transactionId > 0 && in_array($categorySlug, $validCategories, true)) {
            try {
                $stmt = $fastPDO->prepare("SELECT id, file_path FROM transaction_documents WHERE transaction_id = ? AND category = ?");
                $stmt->execute([$transactionId, $categorySlug]);
                $existing = $stmt->fetch();
                if ($existing) {
                    $oldFullPath = __DIR__ . '/../../' . $existing['file_path'];
                    if (file_exists($oldFullPath)) {
                        @unlink($oldFullPath);
                    }
                    $delStmt = $fastPDO->prepare("DELETE FROM transaction_documents WHERE id = ?");
                    $delStmt->execute([$existing['id']]);
                    $_SESSION['flash_success'] = 'Checklist document removed.';
                }
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Database error: ' . $e->getMessage();
            }
        }
        header('Location: ' . $redirectUrl);
        exit;
    }

    // Handle Send to BACtrack sync POST
    if ($action === 'send_to_bac' && $fastPDO !== null) {
        $transactionId = (int)($_POST['transaction_id'] ?? 0);
        
        if ($transactionId > 0) {
            // Check if Purchase Request is uploaded
            $stmt = $fastPDO->prepare("SELECT COUNT(*) FROM transaction_documents WHERE transaction_id = ? AND category = 'purchase_request'");
            $stmt->execute([$transactionId]);
            $hasPr = ($stmt->fetchColumn() > 0);
            
            if (!$hasPr) {
                $_SESSION['flash_error'] = 'Purchase Request document is required before sending.';
                header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=send');
                exit;
            }
            
            // Re-fetch transaction details
            $stmt = $fastPDO->prepare("
                SELECT t.*, d.dv_number, d.bir_2307_number, d.tax_type
                FROM transactions t
                LEFT JOIN document_details d ON t.id = d.transaction_id
                WHERE t.id = ? AND t.bac_reference_number IS NOT NULL
                LIMIT 1
            ");
            $stmt->execute([$transactionId]);
            $tx = $stmt->fetch();
            
            if ($tx) {
                // Fetch and encode checklist documents
                $docStmt = $fastPDO->prepare("SELECT * FROM transaction_documents WHERE transaction_id = ?");
                $docStmt->execute([$transactionId]);
                $docs = $docStmt->fetchAll();
                
                $checklistFilesPayload = [];
                foreach ($docs as $doc) {
                    $filePath = __DIR__ . '/../../' . $doc['file_path'];
                    if (file_exists($filePath)) {
                        $content = file_get_contents($filePath);
                        if ($content !== false) {
                            $checklistFilesPayload[$doc['category']] = [
                                'base64_file' => base64_encode($content),
                                'original_filename' => $doc['original_name'],
                                'file_size' => strlen($content)
                            ];
                        }
                    }
                }
                
                // Map current status to event type
                $eventType = 'DV_CREATED';
                if ($tx['current_status'] === 'Approved') {
                    $eventType = 'FINANCIAL_COMPLETED';
                } elseif ($tx['current_status'] === 'Rejected') {
                    $eventType = 'PROCUREMENT_CANCELLED';
                } elseif ($tx['current_status'] === 'Returned') {
                    $eventType = 'PROCUREMENT_UPDATED';
                }
                
                $bacApiUrl = env('BAC_API_URL', 'http://localhost/SDO-BACtrack/api/integrations/receive-fast.php');
                $fastToken = env('FAST_SYSTEM_TOKEN', 'fast_secure_token_456');
                
                $payload = [
                    'reference_number' => $tx['bac_reference_number'],
                    'event_type' => $eventType,
                    'system_token' => $fastToken,
                    'payload' => [
                        'fast_reference_number' => $tx['tracking_number'],
                        'fast_financial_status' => $tx['current_status'],
                        'dv_number' => $tx['dv_number'] ?? '',
                        'remarks' => $tx['remarks'] ?? 'Sent from FAST Integrations page',
                        'synced_at' => date('Y-m-d H:i:s'),
                        'checklist_files' => $checklistFilesPayload
                    ]
                ];
                
                // Dispatch payload via stream context
                $options = [
                    'http' => [
                        'method' => 'POST',
                        'header' => "Authorization: Bearer " . $fastToken . "\r\n" .
                                    "Content-Type: application/json\r\n",
                        'content' => json_encode($payload),
                        'ignore_errors' => true,
                        'timeout' => 8
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ];
                $context = stream_context_create($options);
                $response = @file_get_contents($bacApiUrl, false, $context);
                
                $httpCode = 500;
                if (isset($http_response_header) && is_array($http_response_header)) {
                    if (preg_match('/^HTTP\/\d\.\d\s+(\d+)/i', $http_response_header[0], $matches)) {
                        $httpCode = (int)$matches[1];
                    }
                }
                
                $status = 'SUCCESS';
                $responseMsg = $response;
                if ($response === false || ($httpCode !== 200 && $httpCode !== 201)) {
                    $status = 'FAILED';
                    $responseMsg = $response === false ? 'Failed to connect to SDO-BACtrack API.' : "HTTP Error {$httpCode}: {$response}";
                }
                
                // Log sync in integration_logs
                SyncLogService::log(
                    $fastPDO,
                    'SDO-FAST',
                    'SDO-BAC',
                    $eventType,
                    $tx['bac_reference_number'],
                    $status,
                    $status === 'FAILED' ? $responseMsg : json_encode($payload)
                );
                
                if ($status === 'SUCCESS') {
                    $_SESSION['flash_success'] = 'Project and checklist files submitted to SDO-BACtrack successfully!';
                } else {
                    $_SESSION['flash_error'] = 'Failed to synchronize with SDO-BACtrack: ' . $responseMsg;
                }
            } else {
                $_SESSION['flash_error'] = 'Transaction not found or not linked to BACtrack.';
            }
        }
        header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=send');
        exit;
    }

    // Handle Accept Project from BACtrack
    if ($action === 'accept_project' && $fastPDO !== null) {
        $projectId = (int)($_POST['project_id'] ?? 0);
        
        if ($projectId > 0) {
            $project = null;
            if ($bacPDO !== null) {
                try {
                    $stmt = $bacPDO->prepare("SELECT p.*, u.name as creator_name FROM projects p LEFT JOIN users u ON p.created_by = u.id WHERE p.id = ?");
                    $stmt->execute([$projectId]);
                    $project = $stmt->fetch();
                } catch (Exception $e) {
                    $_SESSION['flash_error'] = 'Failed to query BACtrack database: ' . $e->getMessage();
                    header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=receive');
                    exit;
                }
            }
            
            if (!$project) {
                $_SESSION['flash_error'] = 'Project not found in BACtrack database.';
                header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=receive');
                exit;
            }

            $approvalFilePath = $project['approval_file_path'] ?? '';
            if (empty($approvalFilePath)) {
                // Fallback: Check if Purchase Request exists in project_documents
                try {
                    if ($bacPDO !== null) {
                        $stmt = $bacPDO->prepare("SELECT file_path FROM project_documents WHERE project_id = ? AND category = 'purchase_request' LIMIT 1");
                        $stmt->execute([$projectId]);
                        $approvalFilePath = $stmt->fetchColumn() ?: '';
                    }
                } catch (Exception $e) {
                    error_log("Failed to fetch PR document fallback: " . $e->getMessage());
                }
            }

            if (empty($approvalFilePath)) {
                $_SESSION['flash_error'] = 'No approval document or Purchase Request uploaded for this project in SDO-BACtrack.';
                header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=receive');
                exit;
            }

            $fullPath = 'C:/xampp/htdocs/SDO-BACtrack/uploads/' . $approvalFilePath;
            if (!file_exists($fullPath)) {
                $_SESSION['flash_error'] = 'File not found at: ' . $fullPath;
                header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=receive');
                exit;
            }

            $fileContent = file_get_contents($fullPath);
            if ($fileContent === false) {
                $_SESSION['flash_error'] = 'Failed to read file: ' . $approvalFilePath;
                header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=receive');
                exit;
            }

            $base64File = base64_encode($fileContent);
            $originalFilename = basename($approvalFilePath);

            // Fetch checklist docs status
            $checklistPayload = [
                'purchase_request' => false,
                'memorandum' => false,
                'activity_proposal' => false,
                'saro' => false
            ];
            try {
                if ($bacPDO !== null) {
                    $stmtDocs = $bacPDO->prepare("SELECT category FROM project_documents WHERE project_id = ?");
                    $stmtDocs->execute([$projectId]);
                    $checklistDocs = $stmtDocs->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($checklistDocs as $doc) {
                        $cat = strtolower(trim($doc['category'] ?? ''));
                        if ($cat === 'purchase_request' || $cat === 'purchase request' || $cat === 'purchase-request') {
                            $checklistPayload['purchase_request'] = true;
                        } elseif ($cat === 'memorandum') {
                            $checklistPayload['memorandum'] = true;
                        } elseif ($cat === 'activity_proposal' || $cat === 'activity or project proposal' || $cat === 'activity-proposal') {
                            $checklistPayload['activity_proposal'] = true;
                        } elseif ($cat === 'saro') {
                            $checklistPayload['saro'] = true;
                        }
                    }
                }
            } catch (Exception $e) {
                error_log("Failed to fetch checklist details: " . $e->getMessage());
            }

            $refNumber = $project['bactrack_id'] ?: 'PR-' . str_pad($project['id'], 4, '0', STR_PAD_LEFT);

            $syncData = [
                'reference_number' => $refNumber,
                'reference_id' => $projectId,
                'project_number' => $refNumber,
                'procurement_type' => $project['procurement_type'],
                'particulars' => $project['title'],
                'amount' => (float)(($project['approved_budget'] ?? 0) > 0 ? $project['approved_budget'] : 150000.00),
                'base64_file' => $base64File,
                'original_filename' => $originalFilename,
                'pr_number' => $refNumber,
                'checklist' => $checklistPayload
            ];

            // Ingest project into FAST using the service directly
            $res = FastIntegrationService::processBacProcurement($syncData, $fastPDO);
            
            if ($res['success']) {
                // Log SUCCESS in integration_logs
                SyncLogService::log(
                    $fastPDO,
                    'SDO-BAC',
                    'SDO-FAST',
                    'PROCUREMENT_APPROVED',
                    $refNumber,
                    'SUCCESS',
                    "Draft generated successfully: {$res['tracking_number']} | Filename: {$originalFilename} | Sender: SDO-BAC | PR Number: {$refNumber} | Timestamp: " . date('Y-m-d H:i:s')
                );
                
                // Mark as synced in SDO-BACtrack's database
                if ($bacPDO !== null) {
                    try {
                           $bacPDO->prepare("UPDATE projects SET fast_tracking_number = ?, fast_sync_status = 'ACCEPTED', fast_synced_at = NOW() WHERE id = ?")
                               ->execute([$res['tracking_number'], $projectId]);
                    } catch (Exception $e) {
                        error_log("Failed to update sync state in BACtrack database: " . $e->getMessage());
                    }
                }
                
                $_SESSION['flash_success'] = 'Incoming project accepted successfully! Draft created: ' . $res['tracking_number'];
            } else {
                $_SESSION['flash_error'] = 'Failed to ingest project: ' . ($res['message'] ?? 'Unknown error');
            }
        }
        header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=received');
        exit;
    }

    // Handle Reject Project
    if ($action === 'reject_project' && $fastPDO !== null) {
        $projectId = (int)($_POST['project_id'] ?? 0);
        $reason = trim($_POST['rejection_reason'] ?? '');
        
        if ($projectId > 0) {
            if (empty($reason)) {
                $_SESSION['flash_error'] = 'Rejection reason is required.';
                header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=receive');
                exit;
            }

            // Fetch details from BACtrack
            $project = null;
            if ($bacPDO !== null) {
                try {
                    $stmt = $bacPDO->prepare("SELECT title, bactrack_id FROM projects WHERE id = ?");
                    $stmt->execute([$projectId]);
                    $project = $stmt->fetch();
                } catch (Exception $e) {
                    error_log("Failed to fetch project for rejection: " . $e->getMessage());
                }
            }

            $refNumber = $project ? ($project['bactrack_id'] ?: 'PR-' . str_pad($projectId, 4, '0', STR_PAD_LEFT)) : 'PR-' . str_pad($projectId, 4, '0', STR_PAD_LEFT);
            $projectTitle = $project ? ($project['title'] ?? 'Unknown Project') : 'Unknown Project';

            // Log REJECTED in integration_logs
            SyncLogService::log(
                $fastPDO,
                'SDO-BAC',
                'SDO-FAST',
                'PROCUREMENT_APPROVED',
                $refNumber,
                'REJECTED',
                "Rejected: {$projectTitle} | Reason: {$reason} | Timestamp: " . date('Y-m-d H:i:s')
            );

            // Update SDO-BACtrack database to hide it from pending
            if ($bacPDO !== null) {
                try {
                    $bacPDO->prepare("UPDATE projects SET fast_sync_status = 'REJECTED', rejection_remarks = ?, rejected_at = NOW() WHERE id = ?")
                           ->execute([$reason, $projectId]);
                } catch (Exception $e) {
                    error_log("Failed to reject sync in BACtrack database: " . $e->getMessage());
                }
            }

            $_SESSION['flash_success'] = 'Project sync request has been rejected and logged.';
        }
        header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=received');
        exit;
    }

    // Handle Delete history entry
    if ($action === 'delete_received_log' && $fastPDO !== null) {
        $logId = (int)($_POST['log_id'] ?? 0);
        
        if ($logId > 0) {
            try {
                // Fetch the record details before deleting
                $stmt = $fastPDO->prepare("SELECT * FROM integration_logs WHERE id = ?");
                $stmt->execute([$logId]);
                $logRecord = $stmt->fetch();
                
                if ($logRecord) {
                    $refNumber = $logRecord['reference_id'] ?: 'Unknown';
                    
                    // 1. Delete from database
                    $delStmt = $fastPDO->prepare("DELETE FROM integration_logs WHERE id = ?");
                    $delStmt->execute([$logId]);
                    
                    // 2. Log deletion
                    $username = $_SESSION['user_name'] ?? 'User';
                    $userId = $_SESSION['user_id'] ?? 0;
                    $timestamp = date('Y-m-d H:i:s');
                    
                    SyncLogService::log(
                        $fastPDO,
                        'SDO-FAST',
                        'SDO-BAC',
                        'DELETION',
                        $refNumber,
                        'DELETED',
                        "Record deleted by User: {$username} (ID: {$userId}) at {$timestamp} for Project ID: {$refNumber}"
                    );
                    
                    $_SESSION['flash_success'] = 'Received history entry deleted and logged successfully.';
                } else {
                    $_SESSION['flash_error'] = 'History entry not found.';
                }
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Failed to delete history entry: ' . $e->getMessage();
            }
        }
        header('Location: ' . env('APP_URL') . '/views/integrations/integrations.php?tab=received');
        exit;
    }
}

// Active tab routing
$activeTab = $_GET['tab'] ?? 'send';
if (!in_array($activeTab, ['send', 'receive', 'received'], true)) {
    $activeTab = 'send';
}

// Load views data
// 1. Send to BACtrack: Transactions processed in FAST ready to be synced back
$sendTransactions = [];
if ($fastPDO !== null) {
    try {
        $stmt = $fastPDO->query("
            SELECT t.*, u.full_name as requestor_name, d.dv_number, d.bir_2307_number, d.tax_type
            FROM transactions t
            LEFT JOIN users u ON t.requestor_id = u.id
            LEFT JOIN document_details d ON t.id = d.transaction_id
            WHERE t.bac_reference_number IS NOT NULL OR t.transaction_type = 'BACtrack'
            ORDER BY t.id DESC
        ");
        $sendTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to fetch integration transactions: " . $e->getMessage());
    }
}

// 2. Receive from BACtrack: Pending approved projects fetched from SDO-BACtrack
$receiveProjects = fetchPendingFromBac();

// 3. Received History: Accepted or rejected entries logged
$receivedHistory = [];
if ($fastPDO !== null) {
    try {
        $stmt = $fastPDO->query("
            SELECT l.*, t.id as transaction_id, t.event_name
            FROM integration_logs l
            LEFT JOIN transactions t ON l.reference_id = t.bac_reference_number
            WHERE l.source_system = 'SDO-BAC' 
              AND l.destination_system = 'SDO-FAST' 
              AND l.sync_status IN ('SUCCESS', 'REJECTED')
            ORDER BY l.id DESC
        ");
        $receivedHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Failed to fetch received history logs: " . $e->getMessage());
    }
}

// Lookup project title from BACtrack or transaction
function getProjectTitle(PDO $fastPDO, ?PDO $bacPDO, $refNumber, $transactionId = null) {
    if ($transactionId) {
        $stmt = $fastPDO->prepare("SELECT event_name FROM transactions WHERE id = ?");
        $stmt->execute([$transactionId]);
        $title = $stmt->fetchColumn();
        if ($title) return $title;
    }
    
    $stmt = $fastPDO->prepare("SELECT event_name FROM transactions WHERE bac_reference_number = ?");
    $stmt->execute([$refNumber]);
    $title = $stmt->fetchColumn();
    if ($title) return $title;

    if ($bacPDO !== null) {
        try {
            $stmt = $bacPDO->prepare("SELECT title FROM projects WHERE bactrack_id = ? OR id = ?");
            $numericId = 0;
            if (preg_match('/PR-(\d+)/i', $refNumber, $matches)) {
                $numericId = (int)$matches[1];
            }
            $stmt->execute([$refNumber, $numericId]);
            $title = $stmt->fetchColumn();
            if ($title) return $title;
        } catch (Exception $e) {}
    }
    
    return 'Unknown Project';
}
?>

<div class="integrations-page">
    <div class="page-header">
        <div>
            <p class="integrations-subtitle">Bidirectional synchronization with SDO-FAST</p>
        </div>
    </div>

    <div class="tabs integrations-tabs" data-tabs>
        <a class="tab-btn<?php echo $activeTab === 'send' ? ' is-active' : ''; ?>" href="?tab=send">
            Send to BACtrack
            <span class="tab-count"><?php echo count($sendTransactions); ?></span>
        </a>
        <a class="tab-btn<?php echo $activeTab === 'receive' ? ' is-active' : ''; ?>" href="?tab=receive">
            Receive from BACtrack
            <span class="tab-count"><?php echo count($receiveProjects); ?></span>
        </a>
        <a class="tab-btn<?php echo $activeTab === 'received' ? ' is-active' : ''; ?>" href="?tab=received">
            Received History
            <span class="tab-count"><?php echo count($receivedHistory); ?></span>
        </a>
    </div>

<!-- Flash Messages -->
<?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
        <div><?php echo htmlspecialchars($_SESSION['flash_error']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm d-flex align-items-center gap-2 mb-4" role="alert">
        <div><?php echo htmlspecialchars($_SESSION['flash_success']); ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<div class="tab-panels">

<!-- =========================================================================
     TAB 1: SEND TO BACTRACK
     ========================================================================= -->
<?php if ($activeTab === 'send'): ?>
    <section class="tab-panel is-active" data-tab-panel="send">
        <h2 class="integrations-section-title">Send to BACtrack</h2>
        <div class="data-card">
            <?php if (empty($sendTransactions)): ?>
                <div class="empty-state">
                    <h3>No projects pending sync</h3>
                    <p>All approved BAC projects are synchronized with SDO-FAST.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Project ID</th>
                                <th style="text-align: center;">Project Title</th>
                                <th style="text-align: center;">Mode of Procurement</th>
                                <th style="text-align: center;">Project Proponent</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Implementation Date</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sendTransactions as $tx):
                                $txDocs = [];
                                if ($fastPDO !== null) {
                                    $docStmt = $fastPDO->prepare("SELECT * FROM transaction_documents WHERE transaction_id = ?");
                                    $docStmt->execute([$tx['id']]);
                                    $txDocs = $docStmt->fetchAll(PDO::FETCH_ASSOC);
                                }

                                $statusClass = 'status-pending';
                                if (($tx['current_status'] ?? '') === 'Approved') {
                                    $statusClass = 'status-approved';
                                } elseif (($tx['current_status'] ?? '') === 'Rejected') {
                                    $statusClass = 'status-disapproved';
                                } elseif (($tx['current_status'] ?? '') === 'Returned') {
                                    $statusClass = 'status-in-progress';
                                }
                            ?>
                                <tr>
                                    <td style="text-align: center; font-weight: 700; letter-spacing: 0.02em;">
                                        <?php echo htmlspecialchars($tx['bac_reference_number'] ?: $tx['tracking_number']); ?>
                                    </td>
                                    <td>
                                        <span style="color: #000; font-weight: 600;">
                                            <?php echo htmlspecialchars($tx['event_name']); ?>
                                        </span>
                                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                            FAST Ref: <?php echo htmlspecialchars($tx['tracking_number']); ?>
                                            <?php if (!empty($tx['dv_number'])): ?> | DV: <?php echo htmlspecialchars($tx['dv_number']); ?><?php endif; ?>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php echo htmlspecialchars($tx['bac_procurement_type'] ?? 'Goods'); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <span><?php echo htmlspecialchars($tx['requestor_name'] ?? '-'); ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($tx['current_status']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php echo date('M d, Y', strtotime($tx['target_date'] ?: $tx['created_at'])); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; align-items: center; gap: 6px;">
                                            <button type="button" class="btn btn-secondary btn-sm integrations-action-btn" onclick='openProjectModal(<?php echo json_encode($tx); ?>, <?php echo json_encode($txDocs); ?>, false)'>
                                                View
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="action" value="send_to_bac">
                                                <input type="hidden" name="transaction_id" value="<?php echo $tx['id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm integrations-action-btn">
                                                    Send
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<!-- =========================================================================
     TAB 2: RECEIVED FROM BACTRACK
     ========================================================================= -->
<?php if ($activeTab === 'receive'): ?>
    <section class="tab-panel is-active" data-tab-panel="receive">
        <h2 class="integrations-section-title">Receive from BACtrack</h2>
        <div class="data-card">
            <?php if (empty($receiveProjects)): ?>
                <div class="empty-state">
                    <h3>No pending data from FAST</h3>
                    <p>There are no status updates or new projects to retrieve from SDO-FAST right now.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Project ID</th>
                                <th style="text-align: center;">Project Title</th>
                                <th style="text-align: center;">Mode of Procurement</th>
                                <th style="text-align: center;">Project Proponent</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Implementation Date</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receiveProjects as $p):
                                $statusClass = ($p['approval_status'] ?? '') === 'APPROVED' ? 'status-approved' : 'status-pending';
                            ?>
                                <tr>
                                    <td style="text-align: center; font-weight: 700; letter-spacing: 0.02em;">
                                        <?php echo htmlspecialchars($p['bactrack_id'] ?? ('PR-' . str_pad($p['id'], 4, '0', STR_PAD_LEFT))); ?>
                                    </td>
                                    <td>
                                        <span style="color: #000; font-weight: 600;">
                                            <?php echo htmlspecialchars($p['title']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php echo htmlspecialchars($p['procurement_type']); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <span><?php echo htmlspecialchars($p['creator_name'] ?? '-'); ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($p['approval_status']); ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php echo date('M d, Y', strtotime($p['project_start_date'] ?: $p['created_at'])); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; align-items: center; gap: 6px;">
                                            <button type="button" class="btn btn-secondary btn-sm integrations-action-btn" onclick='openProjectModal(<?php echo json_encode($p); ?>, <?php echo json_encode($p['documents'] ?? []); ?>, true)'>
                                                View
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="action" value="accept_project">
                                                <input type="hidden" name="project_id" value="<?php echo $p['id']; ?>">
                                                <button type="submit" class="btn btn-primary btn-sm integrations-action-btn">
                                                    Receive
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm integrations-action-btn" onclick="openRejectModal(<?php echo $p['id']; ?>, <?php echo htmlspecialchars(json_encode($p['title'])); ?>)">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<!-- =========================================================================
     TAB 3: RECEIVED HISTORY
     ========================================================================= -->
<?php if ($activeTab === 'received'): ?>
    <section class="tab-panel is-active" data-tab-panel="received">
        <h2 class="integrations-section-title">Received History</h2>
        <div class="data-card">
            <?php if (empty($receivedHistory)): ?>
                <div class="empty-state">
                    <h3>No received history yet</h3>
                    <p>Successful FAST updates will appear here after you click Receive.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="text-align: center;">Project ID</th>
                                <th style="text-align: center;">Project Title</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Processed At</th>
                                <th style="text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receivedHistory as $row):
                                $projectTitle = getProjectTitle($fastPDO, $bacPDO, $row['reference_id'], $row['transaction_id'] ?? null);
                                $txDocs = [];
                                if (!empty($row['transaction_id']) && $fastPDO !== null) {
                                    $docStmt = $fastPDO->prepare("SELECT * FROM transaction_documents WHERE transaction_id = ?");
                                    $docStmt->execute([$row['transaction_id']]);
                                    $txDocs = $docStmt->fetchAll(PDO::FETCH_ASSOC);
                                }

                                $statusClass = $row['sync_status'] === 'SUCCESS' ? 'status-approved' : 'status-disapproved';
                                $statusLabel = $row['sync_status'] === 'SUCCESS' ? 'ACCEPTED' : 'REJECTED';
                            ?>
                                <tr>
                                    <td style="text-align: center; font-weight: 700; letter-spacing: 0.02em;">
                                        <?php echo htmlspecialchars($row['reference_id'] ?: '-'); ?>
                                    </td>
                                    <td>
                                        <span style="color: #000; font-weight: 600;">
                                            <?php echo htmlspecialchars($projectTitle); ?>
                                        </span>
                                        <?php if (!empty($row['response_message'])): ?>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                                <?php echo htmlspecialchars($row['response_message']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <?php echo date('M d, Y h:i A', strtotime($row['synced_at'])); ?>
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display: inline-flex; align-items: center; gap: 6px;">
                                            <button type="button" class="btn btn-secondary btn-sm integrations-action-btn" onclick='openProjectModal(<?php echo json_encode(array_merge($row, ["title" => $projectTitle])); ?>, <?php echo json_encode($txDocs); ?>, true)'>
                                                View
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="event.preventDefault(); const form = this; API.confirmAction('Confirm Deletion', 'Are you sure you want to delete this record? This cannot be undone.', 'Delete', 'error').then(res => { if(res) form.submit(); });">
                                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                                <input type="hidden" name="action" value="delete_received_log">
                                                <input type="hidden" name="log_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm integrations-action-btn">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

</div>

</div>

<!-- =========================================================================
     MODAL: PROJECT DETAIL & PROCUREMENT CHECKLIST
     ========================================================================= -->
<div class="modal fade" id="projectDetailModal" tabindex="-1" aria-labelledby="projectDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="projectDetailModalLabel">Project Integration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="row g-3 fs-8 mb-3">
                    <div class="col-6">
                        <span class="text-muted d-block text-uppercase fw-semibold">Project ID / Reference</span>
                        <strong class="fs-7 text-primary" id="modalRef">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block text-uppercase fw-semibold">Proponent</span>
                        <strong class="text-dark" id="modalProponent">-</strong>
                    </div>
                    <div class="col-12">
                        <span class="text-muted d-block text-uppercase fw-semibold">Project Title</span>
                        <strong class="fs-7 text-dark" id="modalTitle">-</strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block text-uppercase fw-semibold">Procurement Type</span>
                        <span class="badge bg-light text-dark border" id="modalType">-</span>
                    </div>
                    <div class="col-6">
                        <span class="text-muted d-block text-uppercase fw-semibold">Status</span>
                        <span class="badge bg-secondary" id="modalStatus">-</span>
                    </div>
                    <div class="col-4">
                        <span class="text-muted d-block text-uppercase fw-semibold">Gross Amount</span>
                        <strong id="modalAmount">-</strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted d-block text-uppercase fw-semibold">Tax Deduction</span>
                        <strong class="text-danger" id="modalTax">-</strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted d-block text-uppercase fw-semibold">Net Payout</span>
                        <strong class="text-primary-dark fs-7" id="modalNet">-</strong>
                    </div>
                </div>
                
                <!-- Styled Yellow-Bordered Checklist Box -->
                <div class="p-3 mb-1" style="border: 2px solid #ffc107; background-color: #fffdf0; border-radius: 8px;">
                    <h6 style="color: #855800; font-weight: 700; margin-bottom: 12px; font-size: 0.95rem;">Procurement Checklist (Must Need):</h6>
                    <div id="modalChecklistContent">
                        <!-- Numbered checklist rendered dynamically via JS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL: REJECT INCOMING PROJECT
     ========================================================================= -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-primary-dark" id="rejectModalLabel">Reject Incoming Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <input type="hidden" name="action" value="reject_project">
                    <input type="hidden" name="project_id" id="rejectProjectId" value="">
                    
                    <div class="mb-3 fs-8">
                        <span class="text-muted text-uppercase d-block fw-semibold">Project Title</span>
                        <strong id="rejectProjectTitle" class="fs-7 text-dark">-</strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rejectionReason" class="form-label fs-8 fw-semibold text-muted">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" id="rejectionReason" class="form-control" rows="3" placeholder="Provide a reason for rejecting this project synchronization..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     JAVASCRIPT MODAL POPULATOR
     ========================================================================= -->
<script>
function escapeHTML(str) {
    if (!str) return '';
    return str.replace(/[&<>'"]/g, 
        tag => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' }[tag] || tag)
    );
}

function formatBytes(bytes) {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

function openProjectModal(row, docs, isHistory) {
    document.getElementById('modalRef').innerText = row.bac_reference_number || row.bactrack_id || row.tracking_number || row.reference_id || '-';
    document.getElementById('modalTitle').innerText = row.event_name || row.title || row.particulars || 'Unknown Project';
    document.getElementById('modalType').innerText = row.bac_procurement_type || row.procurement_type || '-';
    document.getElementById('modalProponent').innerText = row.requestor_name || row.creator_name || row.project_owner_name || '-';
    
    let amount = parseFloat(row.amount || row.approved_budget || 0);
    let tax = parseFloat(row.tax_amount || 0);
    let net = parseFloat(row.net_amount || amount - tax || 0);
    document.getElementById('modalAmount').innerText = '₱' + amount.toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.getElementById('modalTax').innerText = '₱' + tax.toLocaleString('en-PH', { minimumFractionDigits: 2 });
    document.getElementById('modalNet').innerText = '₱' + net.toLocaleString('en-PH', { minimumFractionDigits: 2 });
    
    document.getElementById('modalStatus').innerText = row.current_status || row.sync_status || row.approval_status || '-';
    
    const categories = {
        purchase_request: { label: 'Purchase Request', desc: '3 original copies required' },
        memorandum: { label: 'Memorandum', desc: 'photocopy only, if applicable' },
        activity_proposal: { label: 'Activity or Project Proposal', desc: 'photocopy only, if applicable' },
        saro: { label: 'SARO', desc: 'photocopy only, if applicable' }
    };
    
    const checklistDiv = document.getElementById('modalChecklistContent');
    checklistDiv.innerHTML = '';
    
    const ol = document.createElement('ol');
    ol.style.color = '#78350f';
    ol.style.paddingLeft = '20px';
    ol.style.marginBottom = '0';
    
    const transactionId = row.id || row.transaction_id || 0;
    const trackingNo = row.tracking_number || '';
    
    const normalizedDocs = (docs || []).map(d => {
        const categorySlug = (d.category || '').toLowerCase().replace(/\s+/g, '_');
        return {
            ...d,
            category_slug: d.category_slug || categorySlug
        };
    });

    if (row.approval_document && row.approval_document.file_url) {
        const approvalWrapper = document.createElement('div');
        approvalWrapper.style.marginBottom = '12px';
        approvalWrapper.innerHTML = `
            <div style="font-weight: 700; color: #78350f;">Approval Document</div>
            <a href="${row.approval_document.file_url}" target="_blank" class="text-primary fw-semibold" style="text-decoration: none;">
                ${escapeHTML(row.approval_document.original_name || 'approval_document')}
            </a>
        `;
        checklistDiv.appendChild(approvalWrapper);
    }

    for (const [key, meta] of Object.entries(categories)) {
        const doc = normalizedDocs.find(d => d.category_slug === key || d.category === key);
        const li = document.createElement('li');
        li.style.marginBottom = '12px';
        li.style.color = '#78350f';
        
        let fileHTML = '';
        if (doc) {
            const docUrl = doc.file_url || (doc.file_path ? ('<?php echo env('APP_URL'); ?>/' + doc.file_path) : '');
            fileHTML = `
                <div style="margin-top: 4px; font-size: 0.85rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                    <div>
                        <a href="${docUrl}" target="_blank" class="text-primary fw-semibold" style="text-decoration: none;">
                            ${escapeHTML(doc.original_name)}
                        </a>
                        <span class="text-muted" style="font-size: 0.75rem;">(${formatBytes(doc.file_size)})</span>
                    </div>
            `;
            if (!isHistory) {
                fileHTML += `
                    <form method="POST" class="m-0" style="display: inline;">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="delete_checklist_file">
                        <input type="hidden" name="transaction_id" value="${transactionId}">
                        <input type="hidden" name="category_slug" value="${key}">
                        <input type="hidden" name="tracking_number" value="${escapeHTML(trackingNo)}">
                        <input type="hidden" name="active_tab" value="send">
                        <button type="button" class="btn btn-sm btn-outline-danger border-0 py-0 px-2" onclick="API.confirmAction('Confirm Deletion', 'Delete this checklist file?', 'Delete').then(res => { if(res) this.closest('form').submit(); });" title="Delete Document">Delete</button>
                    </form>
                `;
            }
            fileHTML += `</div>`;
        } else {
            fileHTML = `
                <div style="margin-top: 4px; font-size: 0.85rem;">
                    <span class="text-muted">No file submitted</span>
            `;
            if (!isHistory) {
                fileHTML += `
                    <form method="POST" enctype="multipart/form-data" class="mt-2 m-0">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" value="upload_checklist_file">
                        <input type="hidden" name="transaction_id" value="${transactionId}">
                        <input type="hidden" name="category_slug" value="${key}">
                        <input type="hidden" name="tracking_number" value="${escapeHTML(trackingNo)}">
                        <input type="hidden" name="active_tab" value="send">
                        <div class="input-group input-group-sm" style="max-width: 320px;">
                            <input type="file" name="checklist_file" accept=".pdf,.jpg,.jpeg,.png" required class="form-control form-control-sm">
                            <button type="submit" class="btn btn-primary btn-sm">Upload</button>
                        </div>
                        <small class="text-muted fs-9 d-block mt-1">Accepts PDF, JPG, PNG only, max 5MB.</small>
                    </form>
                `;
            }
            fileHTML += `</div>`;
        }
        
        li.innerHTML = `
            <span class="fw-bold">${meta.label}</span>, ${meta.desc}
            ${fileHTML}
        `;
        ol.appendChild(li);
    }
    
    checklistDiv.appendChild(ol);
    
    const modalEl = document.getElementById('projectDetailModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function openRejectModal(projectId, title) {
    document.getElementById('rejectProjectId').value = projectId;
    document.getElementById('rejectProjectTitle').innerText = title;
    document.getElementById('rejectionReason').value = '';
    
    const modalEl = document.getElementById('rejectModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';

ob_end_flush();
?>
