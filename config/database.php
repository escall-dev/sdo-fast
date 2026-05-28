<?php
/**
 * Dual Database Connection Layer for SDO FAST.
 * Establishes connections to fast_db and sdo_bac separately.
 */

require_once __DIR__ . '/env.php';

$fastPDO = null;
$bacPDO = null;

// Connect to FAST Database
try {
    $fastHost = env('FAST_DB_HOST', 'localhost');
    $fastName = env('FAST_DB_NAME', 'fast_db');
    $fastUser = env('FAST_DB_USER', 'root');
    $fastPass = env('FAST_DB_PASS', '');

    $fastDsn = "mysql:host={$fastHost};dbname={$fastName};charset=utf8mb4";
    $fastPDO = new PDO($fastDsn, $fastUser, $fastPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Log connection error without database details/credentials in trace
    error_log("SDO FAST - FAST Database Connection Failure: Code " . $e->getCode());
}

// Connect to SDO-BAC Database
try {
    $bacHost = env('BAC_DB_HOST', 'localhost');
    $bacName = env('BAC_DB_NAME', 'sdo_bac');
    $bacUser = env('BAC_DB_USER', 'root');
    $bacPass = env('BAC_DB_PASS', '');

    $bacDsn = "mysql:host={$bacHost};dbname={$bacName};charset=utf8mb4";
    $bacPDO = new PDO($bacDsn, $bacUser, $bacPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Log connection error without database details/credentials in trace
    error_log("SDO FAST - SDO-BAC Database Connection Failure: Code " . $e->getCode());
}
