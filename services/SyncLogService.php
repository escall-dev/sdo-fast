<?php
/**
 * Synchronization Logger Service for SDO Enterprise Integrations.
 * Tracks and logs all incoming and outgoing integration payloads.
 */

class SyncLogService {
    /**
     * Inserts an entry into the integration_logs table.
     * 
     * @param PDO $pdo Database connection.
     * @param string $source Source system name (e.g. SDO-BAC, SDO-FAST).
     * @param string $destination Destination system name.
     * @param string $payloadType Event type descriptor (e.g. PROCUREMENT_APPROVED, DV_APPROVED).
     * @param string|null $referenceId The main linking reference (e.g. BAC-2026-0012).
     * @param string $status Sync execution status (SUCCESS, FAILED, DUPLICATE).
     * @param string|null $responseMessage Response payload or error description.
     * @return bool True on success, false on failure.
     */
    public static function log(PDO $pdo, string $source, string $destination, string $payloadType, ?string $referenceId, string $status, ?string $responseMessage = null) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO integration_logs (source_system, destination_system, payload_type, reference_id, sync_status, response_message) 
                VALUES (:source, :destination, :payload_type, :reference_id, :status, :response)
            ");
            return $stmt->execute([
                'source' => substr($source, 0, 100),
                'destination' => substr($destination, 0, 100),
                'payload_type' => substr($payloadType, 0, 100),
                'reference_id' => $referenceId ? substr($referenceId, 0, 100) : null,
                'status' => substr($status, 0, 50),
                'response' => $responseMessage
            ]);
        } catch (PDOException $e) {
            error_log("Failed to write integration log: " . $e->getMessage());
            return false;
        }
    }
}
