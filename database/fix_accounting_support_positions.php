<?php
/**
 * One-time fix: assign Accounting Support position to ACCTG Support staff
 * who were incorrectly mapped to Accountant position.
 */
require_once __DIR__ . '/../config/database.php';

if ($fastPDO === null) {
    echo "Database connection failed.\n";
    exit(1);
}

$stmt = $fastPDO->prepare("
    UPDATE users u
    JOIN user_roles ur ON u.id = ur.user_id
    JOIN roles r ON ur.role_id = r.id
    SET u.position_id = 3, u.position = 'Accounting Support'
    WHERE r.role_name = 'Accounting Staff'
      AND u.position_id = 2
      AND (
        LOWER(u.full_name) LIKE '%support%'
        OR LOWER(u.username) LIKE '%support%'
        OR LOWER(IFNULL(u.position, '')) LIKE '%support%'
      )
");
$stmt->execute();
echo "Fixed users: " . $stmt->rowCount() . "\n";

$users = $fastPDO->query("
    SELECT u.id, u.username, u.full_name, p.position_name
    FROM users u
    LEFT JOIN positions p ON u.position_id = p.id
    JOIN user_roles ur ON u.id = ur.user_id
    JOIN roles r ON ur.role_id = r.id
    WHERE r.role_name = 'Accounting Staff'
")->fetchAll();

foreach ($users as $u) {
    echo "User {$u['id']} ({$u['username']}) position: {$u['position_name']}\n";
}
