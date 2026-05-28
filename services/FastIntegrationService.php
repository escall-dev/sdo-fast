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

        if (empty($refNumber) || $refId <= 0 || empty($particulars) || $amount <= 0) {
            return [
                'success' => false, 
                'status' => 'INVALID_PAYLOAD',
                'message' => 'Missing required payload parameters (reference_number, reference_id, particulars, amount).'
            ];
        }

        try {
            $pdo->beginTransaction();

            // 1. Prevent Duplication check
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) FROM transactions 
                WHERE bac_reference_id = :ref_id OR bac_reference_number = :ref_num
            ");
            $checkStmt->execute([
                'ref_id' => $refId,
                'ref_num' => $refNumber
            ]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $pdo->rollBack();
                return [
                    'success' => false,
                    'status' => 'DUPLICATE',
                    'message' => "Transaction linked to SDO-BAC reference '{$refNumber}' has already been synchronized."
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
            $status = 'Pending Support';

            // 5. Insert Transaction Draft
            $insertSql = "
                INSERT INTO transactions (
                    uuid, tracking_number, requestor_id, transaction_type, event_name, 
                    amount, tax_amount, net_amount, current_status, remarks,
                    bac_reference_number, bac_reference_id, bac_project_number, bac_procurement_type
                ) VALUES (
                    :uuid, :tracking_number, :requestor_id, 'Reimbursement', :event_name,
                    :amount, :tax_amount, :net_amount, :current_status, :remarks,
                    :bac_ref_num, :bac_ref_id, :bac_proj_num, :bac_proc_type
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
                'remarks' => "Automatically generated draft from SDO-BAC procurement link: {$refNumber}.",
                'bac_ref_num' => $refNumber,
                'bac_ref_id' => $refId,
                'bac_proj_num' => $projectNumber,
                'bac_proc_type' => $procurementType
            ]);

            $transactionId = $pdo->lastInsertId();

            // 6. Insert Document details
            $docStmt = $pdo->prepare("
                INSERT INTO document_details (transaction_id, tax_type) 
                VALUES (:transaction_id, :tax_type)
            ");
            $docStmt->execute([
                'transaction_id' => $transactionId,
                'tax_type' => $taxType
            ]);

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
            $pdo->rollBack();
            error_log("Failed to process BAC integration: " . $e->getMessage());
            return [
                'success' => false,
                'status' => 'DB_ERROR',
                'message' => 'A database error occurred during integration ingestion.'
            ];
        }
    }
}
