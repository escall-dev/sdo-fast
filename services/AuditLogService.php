<?php
/**
 * System Auditing and Activity Logging Service for SDO FAST.
 */

class AuditLogService {
    /**
     * Inserts an entry into the activity_logs table.
     * 
     * @param PDO $pdo The database connection instance.
     * @param int|null $userId User ID associated with the action, or null if system-driven.
     * @param string $activity Description of the action performed.
     * @param mixed $oldValue Previous value of modified field(s) (will be JSON encoded).
     * @param mixed $newValue Updated value of modified field(s) (will be JSON encoded).
     * @return bool True on success, false on failure.
     */
    public static function log(PDO $pdo, ?int $userId, string $activity, $oldValue = null, $newValue = null) {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
            
            // Format values to JSON if they are arrays or objects
            $oldFormatted = is_scalar($oldValue) ? $oldValue : json_encode($oldValue);
            $newFormatted = is_scalar($newValue) ? $newValue : json_encode($newValue);
            
            $stmt = $pdo->prepare("
                INSERT INTO activity_logs (user_id, activity, old_value, new_value, ip_address) 
                VALUES (:user_id, :activity, :old_value, :new_value, :ip_address)
            ");
            return $stmt->execute([
                'user_id' => $userId,
                'activity' => substr($activity, 0, 255),
                'old_value' => $oldFormatted,
                'new_value' => $newFormatted,
                'ip_address' => $ip
            ]);
        } catch (PDOException $e) {
            error_log("Failed to write audit log: " . $e->getMessage());
            return false;
        }
    }
}
