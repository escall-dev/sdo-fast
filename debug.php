<?php
require 'c:/xampp/htdocs/fast/config/database.php';
$stmt = $fastPDO->query('SELECT NOW() as mysql_now');
$row = $stmt->fetch();
$mysql_now = $row['mysql_now'];
$php_now = date('Y-m-d H:i:s');

$stmt = $fastPDO->query('SELECT * FROM password_reset_tokens ORDER BY id DESC LIMIT 5');
$tokens = $stmt->fetchAll(PDO::FETCH_ASSOC);

file_put_contents('debug_db.txt', "MySQL NOW: $mysql_now\nPHP NOW: $php_now\nTokens:\n" . print_r($tokens, true));
echo "Done";
