<?php
/**
 * Outgoing Sync Dispatcher Service from SDO FAST to SDO BAC.
 * Employs Bearer tokens and cURL to transmit financial approvals.
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/SyncLogService.php';

class BacIntegrationService {
    /**
     * Transmits a workflow status change to SDO-BAC.
     * 
     * @param int $transactionId The FAST transaction ID.
     * @param string $newStatus The updated FAST status.
     * @param string $remarks Processing comments.
     * @param string $dvNumber Associated DV number.
     * @param PDO $pdo FAST database connection.
     * @return bool True on successful sync, false on failure.
     */
    public static function syncStatusToBac(int $transactionId, string $newStatus, string $remarks, string $dvNumber, PDO $pdo) {
        try {
            // Fetch transaction details
            $stmt = $pdo->prepare("
                SELECT t.*, d.dv_number 
                FROM transactions t
                LEFT JOIN document_details d ON t.id = d.transaction_id
                WHERE t.id = :id AND t.bac_reference_number IS NOT NULL
                LIMIT 1
            ");
            $stmt->execute(['id' => $transactionId]);
            $tx = $stmt->fetch();

            if (!$tx) {
                // Not linked to a BAC transaction, skip sync
                return true;
            }

            // Map FAST status to BAC integration event type
            $eventType = 'DV_CREATED';
            if ($newStatus === 'Approved') {
                $eventType = 'FINANCIAL_COMPLETED';
            } elseif ($newStatus === 'Rejected') {
                $eventType = 'PROCUREMENT_CANCELLED';
            } elseif ($newStatus === 'Returned') {
                $eventType = 'PROCUREMENT_UPDATED';
            }

            $payload = [
                'reference_number' => $tx['bac_reference_number'],
                'event_type' => $eventType,
                'payload' => [
                    'fast_reference_number' => $tx['tracking_number'],
                    'fast_financial_status' => $newStatus,
                    'dv_number' => !empty($dvNumber) ? $dvNumber : ($tx['dv_number'] ?? ''),
                    'remarks' => $remarks,
                    'synced_at' => date('Y-m-d H:i:s')
                ]
            ];

            return self::dispatchPayload($payload, $tx['bac_reference_number'], $eventType, $pdo);

        } catch (PDOException $e) {
            error_log("Failed to load transaction for BAC sync: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retries a previously failed integration log record.
     * 
     * @param int $logId The integration_logs ID.
     * @param PDO $pdo FAST database connection.
     * @return bool True on success, false on failure.
     */
    public static function retryLogSync(int $logId, PDO $pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM integration_logs WHERE id = :id LIMIT 1");
            $stmt->execute(['id' => $logId]);
            $log = $stmt->fetch();

            if (!$log || $log['sync_status'] !== 'FAILED') {
                return false;
            }

            // Parse stored raw payload or reconstruct it
            $payload = json_decode($log['response_message'], true);
            if (empty($payload) || !isset($payload['reference_number'])) {
                // If response_message was just an error log, we can try to pull transaction metadata
                $txStmt = $pdo->prepare("SELECT id, current_status, remarks FROM transactions WHERE bac_reference_number = :ref LIMIT 1");
                $txStmt->execute(['ref' => $log['reference_id']]);
                $tx = $txStmt->fetch();
                if ($tx) {
                    return self::syncStatusToBac($tx['id'], $tx['current_status'], $tx['remarks'] ?: 'Retry Sync', '', $pdo);
                }
                return false;
            }

            // Dispatch
            $success = self::dispatchPayload($payload, $log['reference_id'], $log['payload_type'], $pdo);
            
            if ($success) {
                // Update log state
                $updateStmt = $pdo->prepare("UPDATE integration_logs SET sync_status = 'SUCCESS', response_message = 'Sync succeeded on manual retry.' WHERE id = :id");
                $updateStmt->execute(['id' => $logId]);
            }
            return $success;

        } catch (Exception $e) {
            error_log("Retry sync failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Executes the HTTP cURL POST to SDO-BAC api.
     */
    private static function dispatchPayload(array $payload, string $refNumber, string $eventType, PDO $pdo) {
        // Target BAC endpoint (defined in .env or defaulting to localhost XAMPP)
        $bacUrl = env('BAC_API_URL', 'http://localhost/bac/api/integrations/receive-fast.php');
        $bacToken = env('BAC_SYSTEM_TOKEN', 'bac_secure_token_123');

        $ch = curl_init($bacUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $bacToken,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_TIMEOUT, 6);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $status = 'SUCCESS';
        $logResponse = $response;

        if ($httpCode !== 200) {
            $status = 'FAILED';
            $logResponse = !empty($curlError) 
                ? "cURL Error: {$curlError}" 
                : "HTTP API Error Code {$httpCode}. Response: {$response}";
        }

        // Write log entry in integration_logs
        SyncLogService::log(
            $pdo,
            'SDO-FAST',
            'SDO-BAC',
            $eventType,
            $refNumber,
            $status,
            $status === 'FAILED' ? $logResponse : json_encode($payload)
        );

        return ($status === 'SUCCESS');
    }
}
