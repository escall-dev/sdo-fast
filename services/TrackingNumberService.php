<?php
/**
 * Unique Tracking Number Generation Service for SDO FAST.
 * Code Format: FAST-YYYY-000001
 */

class TrackingNumberService {
    /**
     * Generates a unique, sequential tracking number for the current year.
     * Uses a lock-safe SELECT FOR UPDATE query to prevent concurrency collisions.
     * 
     * @param PDO $pdo The database connection instance.
     * @return string The generated tracking number.
     */
    public static function generate(PDO $pdo) {
        $year = date('Y');
        $prefix = "FAST-{$year}-";

        // Query the latest matching tracking number for the current year with a write lock
        $stmt = $pdo->prepare("
            SELECT tracking_number 
            FROM transactions 
            WHERE tracking_number LIKE :prefix 
            ORDER BY id DESC 
            LIMIT 1 
            FOR UPDATE
        ");
        $stmt->execute(['prefix' => $prefix . '%']);
        $latest = $stmt->fetchColumn();

        $nextSerial = 1;
        if ($latest) {
            // Extract the last 6 digits and increment
            $parts = explode('-', $latest);
            if (count($parts) === 3) {
                $lastSerial = (int)$parts[2];
                $nextSerial = $lastSerial + 1;
            }
        }

        // Pad with leading zeros up to 6 digits
        $serialStr = str_pad($nextSerial, 6, '0', STR_PAD_LEFT);
        return $prefix . $serialStr;
    }
}
