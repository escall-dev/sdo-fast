<?php
/**
 * Environment configuration loader for SDO FAST.
 * Parses the .env file in the root directory.
 */

if (!function_exists('env')) {
    function env($key, $default = null) {
        // Check PHP environment variables first
        $value = getenv($key);
        if ($value !== false) {
            return parseEnvValue($value);
        }

        // Check $_ENV
        if (isset($_ENV[$key])) {
            return parseEnvValue($_ENV[$key]);
        }

        // Check $_SERVER
        if (isset($_SERVER[$key])) {
            return parseEnvValue($_SERVER[$key]);
        }

        return $default;
    }
}

if (!function_exists('parseEnvValue')) {
    function parseEnvValue($value) {
        $value = trim($value);
        if (strtolower($value) === 'true') return true;
        if (strtolower($value) === 'false') return false;
        if (strtolower($value) === 'null') return null;
        
        // Remove surrounding quotes
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            return $matches[1];
        }
        if (preg_match('/^\'(.*)\'$/', $value, $matches)) {
            return $matches[1];
        }
        
        return $value;
    }
}

// Load .env file
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        // Skip comments
        if (strpos($line, '#') === 0 || empty($line)) {
            continue;
        }

        // Parse key-value pair
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Handle inline comments
            if (strpos($value, '#') !== false) {
                // simple split unless it is quoted
                if (!preg_match('/^["\'].*["\']/', $value)) {
                    list($value) = explode('#', $value, 2);
                    $value = trim($value);
                }
            }

            // Put into getenv, $_ENV, $_SERVER
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}
