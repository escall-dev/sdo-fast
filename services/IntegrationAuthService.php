<?php
/**
 * Authentication and Verification Service for SDO Enterprise Integrations.
 * Validates incoming API request Bearer tokens against stored SHA-256 hashes.
 */

require_once __DIR__ . '/../config/database.php';

class IntegrationAuthService {
    /**
     * Authenticates an incoming integration API request.
     * 
     * @param PDO $pdo The database connection instance.
     * @return array|bool Returns the token row if authorized, otherwise false.
     */
    public static function authenticate(PDO $pdo) {
        $headers = self::getRequestHeaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (empty($authHeader)) {
            return false;
        }

        // Extract Bearer token
        if (preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
            $token = $matches[1];
            // Compute SHA-256 hash of the received token
            $tokenHash = hash('sha256', $token);

            try {
                $stmt = $pdo->prepare("
                    SELECT * FROM integration_tokens 
                    WHERE token_hash = :hash AND status = 'active' 
                    LIMIT 1
                ");
                $stmt->execute(['hash' => $tokenHash]);
                $tokenRow = $stmt->fetch();

                if ($tokenRow) {
                    return $tokenRow;
                }
            } catch (PDOException $e) {
                error_log("Integration authentication database error: " . $e->getMessage());
            }
        }

        return false;
    }

    /**
     * Helper to retrieve all request headers.
     */
    private static function getRequestHeaders() {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
