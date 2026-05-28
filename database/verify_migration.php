<?php
require_once __DIR__ . '/../config/env.php';
$pdo = new PDO('mysql:host=' . env('FAST_DB_HOST', 'localhost') . ';dbname=' . env('FAST_DB_NAME', 'fast_db'), env('FAST_DB_USER', 'root'), env('FAST_DB_PASS', ''));
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

echo "=== POSITIONS ===\n";
foreach ($pdo->query('SELECT * FROM positions ORDER BY id')->fetchAll() as $r) {
    echo $r['id'] . ' | ' . $r['position_name'] . ' | ' . $r['mapped_role'] . ' | default=' . $r['is_default'] . "\n";
}

echo "\n=== ROLES ===\n";
foreach ($pdo->query('SELECT * FROM roles ORDER BY id')->fetchAll() as $r) {
    echo $r['id'] . ' | ' . $r['role_name'] . "\n";
}

echo "\n=== USERS ===\n";
$sql = 'SELECT u.id, u.full_name, u.position_id, p.position_name, r.role_name 
        FROM users u 
        LEFT JOIN positions p ON u.position_id = p.id 
        LEFT JOIN user_roles ur ON u.id = ur.user_id 
        LEFT JOIN roles r ON ur.role_id = r.id 
        ORDER BY u.id';
foreach ($pdo->query($sql)->fetchAll() as $r) {
    $pos = $r['position_name'] ?? 'NULL';
    $role = $r['role_name'] ?? 'NULL';
    echo $r['id'] . ' | ' . $r['full_name'] . ' | pos=' . $pos . ' | role=' . $role . "\n";
}
