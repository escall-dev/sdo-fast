<?php
/**
 * SDO FAST Business Integration Service.
 * Implements transaction draft creation from received SDO-BAC procurement events.
 */

require_once __DIR__ . '/TrackingNumberService.php';
require_once __DIR__ . '/AuditLogService.php';
require_once __DIR__ . '/SyncLogService.php';

class FastIntegrationService {
    /**
     * Processes an approved procurement payload received from SDO-BAC.
     * Generates a transaction draft in FAST.
     * 
     * @param array $payload The raw JSON data parsed from BAC.
     * @param PDO $pdo FAST database connection.
     * @return array Sync transaction result.
     */
    public static function processBacProcurement(array $payload, PDO $pdo) {
        $refNumber = trim($payload['reference_number'] ?? '');
        $refId = (int)($payload['reference_id'] ?? 0);
        $projectNumber = trim($payload['project_number'] ?? '');
        $procurementType = trim($payload['procurement_type'] ?? 'Goods');
        $particulars = trim($payload['particulars'] ?? '');
        $amount = (float)($payload['amount'] ?? 0.00);

        $base64File = $payload['base64_file'] ?? '';
        $originalFilename = $payload['original_filename'] ?? '';
        $prNumber = $payload['pr_number'] ?? '';
        $checklist = $payload['checklist'] ?? [];

        if (empty($refNumber) || $refId <= 0 || empty($particulars) || $amount <= 0) {
            return [
                'success' => false, 
                'status' => 'INVALID_PAYLOAD',
                'message' => 'Missing required payload parameters (reference_number, reference_id, particulars, amount).'
            ];
        }

        // Validate and save the approval file
        if (empty($base64File)) {
            return [
                'success' => false,
                'status' => 'INVALID_PAYLOAD',
                'message' => 'Approval document is required.'
            ];
        }

        $ext = pathinfo($originalFilename, PATHINFO_EXTENSION) ?: 'pdf';
        $filename = $prNumber . '_' . time() . '.' . $ext;
        // Clean filename for safety
        $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $filename);

        $uploadSubDir = 'uploads/received-approvals/';
        $uploadDir = __DIR__ . '/../' . $uploadSubDir;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filePath = $uploadSubDir . $filename;
        $fullPath = $uploadDir . $filename;

        $decodedData = base64_decode($base64File);
        if ($decodedData === false) {
            return [
                'success' => false,
                'status' => 'INVALID_PAYLOAD',
                'message' => 'Failed to decode base64 file data.'
            ];
        }

        if (file_put_contents($fullPath, $decodedData) === false) {
            return [
                'success' => false,
                'status' => 'FILE_SAVE_ERROR',
                'message' => 'Failed to save approval document on server.'
            ];
        }

        // Create transaction_documents table if not exists (outside transaction to avoid implicit commit in MySQL)
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS transaction_documents (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    transaction_id INT NOT NULL,
                    category VARCHAR(255) NOT NULL,
                    file_path VARCHAR(255) NOT NULL,
                    original_name VARCHAR(255) NOT NULL,
                    file_size INT NOT NULL DEFAULT 0,
                    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
                    INDEX idx_tx_id (transaction_id),
                    INDEX idx_cat (category)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (Exception $tblEx) {
            error_log("Failed to create transaction_documents table: " . $tblEx->getMessage());
        }

        // Self-healing table creation (DDL) must run OUTSIDE transaction block to prevent implicit commit
        try {
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS transaction_documents (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    transaction_id INT NOT NULL,
                    category VARCHAR(255) NOT NULL,
                    file_path VARCHAR(255) NOT NULL,
                    original_name VARCHAR(255) NOT NULL,
                    file_size INT NOT NULL DEFAULT 0,
                    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
                    INDEX idx_tx_id (transaction_id),
                    INDEX idx_cat (category)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            
            // Ensure the procurement_checklist column exists in document_details
            $colCheck = $pdo->query("
                SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'document_details' 
                  AND COLUMN_NAME = 'procurement_checklist'
            ");
            if ((int)$colCheck->fetchColumn() === 0) {
                $pdo->exec("ALTER TABLE `document_details` ADD COLUMN `procurement_checklist` JSON DEFAULT NULL AFTER `attachment_path`");
            }
        } catch (PDOException $ddlEx) {
            error_log("Failed to run defensive DDL: " . $ddlEx->getMessage());
        }

        try {
            $pdo->beginTransaction();

            // 1. Prevent Duplication check
            $existingStmt = $pdo->prepare("
                SELECT id, tracking_number, approval_file_path
                FROM transactions 
                WHERE bac_reference_id = :ref_id OR bac_reference_number = :ref_num
                ORDER BY id DESC
                LIMIT 1
            ");
            $existingStmt->execute([
                'ref_id' => $refId,
                'ref_num' => $refNumber
            ]);
            $existingTx = $existingStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingTx) {
                $existingId = (int)$existingTx['id'];

                // Always refresh approval file path and documents for existing transaction
                $updateApproval = $pdo->prepare("UPDATE transactions SET approval_file_path = ? WHERE id = ?");
                $updateApproval->execute([$filePath, $existingId]);

                // Clear existing checklist documents before re-sync
                $pdo->prepare("DELETE FROM transaction_documents WHERE transaction_id = ?")
                    ->execute([$existingId]);

                // Copy checklist documents from BACtrack into existing transaction
                global $bacPDO;
                if ($bacPDO !== null) {
                    try {
                        $stmtDocs = $bacPDO->prepare("SELECT category, file_path, original_name, file_size FROM project_documents WHERE project_id = ?");
                        $stmtDocs->execute([$refId]);
                        $bacDocs = $stmtDocs->fetchAll();

                        foreach ($bacDocs as $doc) {
                            $cat = strtolower(trim($doc['category'] ?? ''));
                            $categorySlug = '';
                            if ($cat === 'purchase_request' || $cat === 'purchase request' || $cat === 'purchase-request') {
                                $categorySlug = 'purchase_request';
                            } elseif ($cat === 'memorandum') {
                                $categorySlug = 'memorandum';
                            } elseif ($cat === 'activity_proposal' || $cat === 'activity or project proposal' || $cat === 'activity-proposal') {
                                $categorySlug = 'activity_proposal';
                            } elseif ($cat === 'saro') {
                                $categorySlug = 'saro';
                            }

                            if (!empty($categorySlug) && !empty($doc['file_path'])) {
                                $bacFileFullPath = 'C:/xampp/htdocs/SDO-BACtrack/uploads/' . $doc['file_path'];
                                if (file_exists($bacFileFullPath)) {
                                    $fastUploadDir = __DIR__ . '/../uploads/procurement-docs/';
                                    if (!is_dir($fastUploadDir)) {
                                        mkdir($fastUploadDir, 0755, true);
                                    }

                                    $ext = pathinfo($doc['original_name'], PATHINFO_EXTENSION) ?: 'pdf';
                                    $newFilename = "{$existingId}_{$categorySlug}_" . time() . '.' . $ext;
                                    $fastRelativePath = 'uploads/procurement-docs/' . $newFilename;
                                    $fastFileFullPath = $fastUploadDir . $newFilename;

                                    if (copy($bacFileFullPath, $fastFileFullPath)) {
                                        $docInsert = $pdo->prepare("
                                            INSERT INTO transaction_documents (transaction_id, category, file_path, original_name, file_size)
                                            VALUES (?, ?, ?, ?, ?)
                                        ");
                                        $docInsert->execute([
                                            $existingId,
                                            $categorySlug,
                                            $fastRelativePath,
                                            $doc['original_name'],
                                            $doc['file_size']
                                        ]);
                                    }
                                }
                            }
                        }
                    } catch (Exception $copyEx) {
                        error_log("Failed to sync checklist documents for existing transaction: " . $copyEx->getMessage());
                    }
                }

                // Ensure at least purchase_request exists using approval document as fallback
                try {
                    $prCheck = $pdo->prepare("SELECT COUNT(*) FROM transaction_documents WHERE transaction_id = ? AND category = 'purchase_request'");
                    $prCheck->execute([$existingId]);
                    if ((int)$prCheck->fetchColumn() === 0) {
                        $docInsert = $pdo->prepare("
                            INSERT INTO transaction_documents (transaction_id, category, file_path, original_name, file_size)
                            VALUES (:tx_id, 'purchase_request', :path, :orig, :size)
                        ");
                        $docInsert->execute([
                            'tx_id' => $existingId,
                            'path' => $filePath,
                            'orig' => $originalFilename,
                            'size' => file_exists($fullPath) ? filesize($fullPath) : 0
                        ]);
                    }
                } catch (Exception $fallbackEx) {
                    error_log("Failed to insert fallback PR for existing transaction: " . $fallbackEx->getMessage());
                }

                // Update checklist JSON if document_details exists
                $docDetailStmt = $pdo->prepare("SELECT id FROM document_details WHERE transaction_id = ? LIMIT 1");
                $docDetailStmt->execute([$existingId]);
                $docDetailId = (int)$docDetailStmt->fetchColumn();
                if ($docDetailId > 0) {
                    $checklist = $payload['checklist'] ?? [];
                    $checklistLabels = [
                        'purchase_request' => 'Purchase Request',
                        'memorandum' => 'Memorandum',
                        'activity_proposal' => 'Activity or Project Proposal',
                        'saro' => 'SARO'
                    ];
                    $normalizedChecklist = [];
                    foreach ($checklistLabels as $key => $label) {
                        $normalizedChecklist[$key] = !empty($checklist[$key]);
                    }
                    $updateDocStmt = $pdo->prepare("
                        UPDATE document_details
                        SET procurement_checklist = :checklist_json
                        WHERE id = :doc_id
                    ");
                    $updateDocStmt->execute([
                        'checklist_json' => json_encode($normalizedChecklist, JSON_UNESCAPED_UNICODE),
                        'doc_id' => $docDetailId
                    ]);
                }

                $pdo->commit();
                return [
                    'success' => true,
                    'status' => 'UPDATED',
                    'tracking_number' => $existingTx['tracking_number'],
                    'message' => "Existing FAST draft updated with submitted documents for '{$refNumber}'."
                ];
            }

            // 2. Fetch tax configuration percentage
            // SDO-BAC procurement types usually map to Goods or Services
            $taxType = 'Goods';
            if (stripos($procurementType, 'service') !== false || stripos($procurementType, 'consulting') !== false) {
                $taxType = 'Services';
            } elseif (stripos($procurementType, 'food') !== false || stripos($procurementType, 'catering') !== false) {
                $taxType = 'Foods';
            }

            $taxStmt = $pdo->prepare("SELECT tax_percentage FROM tax_configurations WHERE tax_type = :tax_type AND is_active = 1 LIMIT 1");
            $taxStmt->execute(['tax_type' => $taxType]);
            $taxPercentage = $taxStmt->fetchColumn();
            if ($taxPercentage === false) {
                $taxPercentage = 5.00; // fallback default
            }

            // 3. Compute Tax and Net payout
            $taxAmount = $amount * ($taxPercentage / 100);
            $netAmount = $amount - $taxAmount;

            // 4. Generate unique tracking number sequentially
            $trackingNumber = TrackingNumberService::generate($pdo);
            $uuid = bin2hex(random_bytes(16));
            $uuid = substr($uuid, 0, 8) . '-' . substr($uuid, 8, 4) . '-' . substr($uuid, 12, 4) . '-' . substr($uuid, 16, 4) . '-' . substr($uuid, 20, 12);
            
            // Map default requestor to Super Admin (User ID 1) for automated entries
            $requestorId = 1; 
            $status = 'Pending Accountant 1';

            // 5. Insert Transaction Draft
            $transactionRemarks = "Automatically generated draft from SDO-BAC procurement link: {$refNumber}.";
            $insertSql = "
                INSERT INTO transactions (
                    uuid, tracking_number, requestor_id, transaction_type, event_name, 
                    amount, tax_amount, net_amount, current_status, remarks,
                    bac_reference_number, bac_reference_id, bac_project_number, bac_procurement_type,
                    approval_file_path
                ) VALUES (
                    :uuid, :tracking_number, :requestor_id, 'BACtrack', :event_name,
                    :amount, :tax_amount, :net_amount, :current_status, :remarks,
                    :bac_ref_num, :bac_ref_id, :bac_proj_num, :bac_proc_type,
                    :approval_file_path
                )
            ";
            $txStmt = $pdo->prepare($insertSql);
            $txStmt->execute([
                'uuid' => $uuid,
                'tracking_number' => $trackingNumber,
                'requestor_id' => $requestorId,
                'event_name' => $particulars,
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'net_amount' => $netAmount,
                'current_status' => $status,
                'remarks' => $transactionRemarks,
                'bac_ref_num' => $refNumber,
                'bac_ref_id' => $refId,
                'bac_proj_num' => $projectNumber,
                'bac_proc_type' => $procurementType,
                'approval_file_path' => $filePath
            ]);

            $transactionId = $pdo->lastInsertId();

            // 5b. Copy and insert actual checklist documents from SDO-BACtrack
            global $bacPDO;
            if ($bacPDO !== null) {
                try {
                    $stmtDocs = $bacPDO->prepare("SELECT category, file_path, original_name, file_size FROM project_documents WHERE project_id = ?");
                    $stmtDocs->execute([$refId]);
                    $bacDocs = $stmtDocs->fetchAll();

                    foreach ($bacDocs as $doc) {
                        $cat = strtolower(trim($doc['category'] ?? ''));
                        $categorySlug = '';
                        if ($cat === 'purchase_request' || $cat === 'purchase request' || $cat === 'purchase-request') {
                            $categorySlug = 'purchase_request';
                        } elseif ($cat === 'memorandum') {
                            $categorySlug = 'memorandum';
                        } elseif ($cat === 'activity_proposal' || $cat === 'activity or project proposal' || $cat === 'activity-proposal') {
                            $categorySlug = 'activity_proposal';
                        } elseif ($cat === 'saro') {
                            $categorySlug = 'saro';
                        }

                        if (!empty($categorySlug) && !empty($doc['file_path'])) {
                            $bacFileFullPath = 'C:/xampp/htdocs/SDO-BACtrack/uploads/' . $doc['file_path'];
                            if (file_exists($bacFileFullPath)) {
                                $fastUploadDir = __DIR__ . '/../uploads/procurement-docs/';
                                if (!is_dir($fastUploadDir)) {
                                    mkdir($fastUploadDir, 0755, true);
                                }

                                $ext = pathinfo($doc['original_name'], PATHINFO_EXTENSION) ?: 'pdf';
                                $newFilename = "{$transactionId}_{$categorySlug}_" . time() . '.' . $ext;
                                $fastRelativePath = 'uploads/procurement-docs/' . $newFilename;
                                $fastFileFullPath = $fastUploadDir . $newFilename;

                                if (copy($bacFileFullPath, $fastFileFullPath)) {
                                    $docInsert = $pdo->prepare("
                                        INSERT INTO transaction_documents (transaction_id, category, file_path, original_name, file_size)
                                        VALUES (?, ?, ?, ?, ?)
                                    ");
                                    $docInsert->execute([
                                        $transactionId,
                                        $categorySlug,
                                        $fastRelativePath,
                                        $doc['original_name'],
                                        $doc['file_size']
                                    ]);
                                }
                            }
                        }
                    }
                } catch (Exception $copyEx) {
                    error_log("Failed to sync checklist documents from SDO-BACtrack: " . $copyEx->getMessage());
                }
            }

            // Fallback: If no purchase_request was copied, insert the approval document as a fallback
            try {
                $prCheck = $pdo->prepare("SELECT COUNT(*) FROM transaction_documents WHERE transaction_id = ? AND category = 'purchase_request'");
                $prCheck->execute([$transactionId]);
                if ((int)$prCheck->fetchColumn() === 0) {
                    $docInsert = $pdo->prepare("
                        INSERT INTO transaction_documents (transaction_id, category, file_path, original_name, file_size)
                        VALUES (:tx_id, 'purchase_request', :path, :orig, :size)
                    ");
                    $docInsert->execute([
                        'tx_id' => $transactionId,
                        'path' => $filePath,
                        'orig' => $originalFilename,
                        'size' => file_exists($fullPath) ? filesize($fullPath) : 0
                    ]);
                }
            } catch (Exception $fallbackEx) {
                error_log("Failed to insert fallback synced PR document: " . $fallbackEx->getMessage());
            }


            // 6. Insert Document details
            $docStmt = $pdo->prepare("
                INSERT INTO document_details (transaction_id, tax_type) 
                VALUES (:transaction_id, :tax_type)
            ");
            $docStmt->execute([
                'transaction_id' => $transactionId,
                'tax_type' => $taxType
            ]);

            $documentDetailId = $pdo->lastInsertId();

            // 6b. Persist checklist JSON into document_details
            $checklist = $payload['checklist'] ?? [];
            if (!empty($checklist)) {
                $checklistLabels = [
                    'purchase_request' => 'Purchase Request',
                    'memorandum' => 'Memorandum',
                    'activity_proposal' => 'Activity or Project Proposal',
                    'saro' => 'SARO'
                ];

                // Normalize checklist: only keep known keys with boolean values
                $normalizedChecklist = [];
                foreach ($checklistLabels as $key => $label) {
                    $normalizedChecklist[$key] = !empty($checklist[$key]);
                }

                $updateDocStmt = $pdo->prepare("
                    UPDATE document_details 
                    SET procurement_checklist = :checklist_json 
                    WHERE id = :doc_id
                ");
                $updateDocStmt->execute([
                    'checklist_json' => json_encode($normalizedChecklist, JSON_UNESCAPED_UNICODE),
                    'doc_id' => $documentDetailId
                ]);
            }

            // 7. Insert Workflow Log
            $logStmt = $pdo->prepare("
                INSERT INTO transaction_status_logs (transaction_id, previous_status, new_status, changed_by, remarks) 
                VALUES (:transaction_id, NULL, :status, :changed_by, :remarks)
            ");
            $logStmt->execute([
                'transaction_id' => $transactionId,
                'status' => $status,
                'changed_by' => $requestorId,
                'remarks' => "Integration Sync: Link established with SDO-BAC procurement reference: {$refNumber}."
            ]);

            // 8. Log sync in bac_sync_logs
            $syncStmt = $pdo->prepare("
                INSERT INTO bac_sync_logs (bac_reference_id, synced_by, sync_status, remarks) 
                VALUES (:bac_ref_id, :synced_by, 'SUCCESS', :remarks)
            ");
            $syncStmt->execute([
                'bac_ref_id' => $refId,
                'synced_by' => $requestorId,
                'remarks' => "FAST tracking code assigned: {$trackingNumber}."
            ]);

            // 9. Audit Event
            AuditLogService::log(
                $pdo, 
                $requestorId, 
                "Enterprise Sync: Generated FAST draft {$trackingNumber} from BAC reference {$refNumber}"
            );

            $pdo->commit();

            return [
                'success' => true,
                'status' => 'SUCCESS',
                'tracking_number' => $trackingNumber,
                'message' => "SDO-BAC procurement reference '{$refNumber}' synchronized successfully. Created FAST draft: '{$trackingNumber}'."
            ];

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Failed to process BAC integration: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'DB_ERROR',
                'message' => 'A database error occurred during integration ingestion: ' . $e->getMessage()
            ];
        }
    }
}
