<?php
/**
 * One-time migration script: Add positions system to SDO FAST.
 * Run this once to update the database schema.
 */

require_once __DIR__ . '/../config/env.php';

try {
    $pdo = new PDO(
        'mysql:host=' . env('FAST_DB_HOST', 'localhost') . ';dbname=' . env('FAST_DB_NAME', 'fast_db') . ';charset=utf8mb4',
        env('FAST_DB_USER', 'root'),
        env('FAST_DB_PASS', '')
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create positions table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `positions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `position_name` VARCHAR(100) NOT NULL UNIQUE,
        `mapped_role` VARCHAR(50) NOT NULL DEFAULT 'User',
        `is_default` TINYINT(1) NOT NULL DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");
    echo "[OK] positions table created/verified\n";

    // 2. Add position_id column to users if not exists
    $cols = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'position_id'")->fetchAll();
    if (count($cols) === 0) {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `position_id` INT DEFAULT NULL AFTER `username`");
        echo "[OK] position_id column added to users\n";
    } else {
        echo "[SKIP] position_id column already exists\n";
    }

    // 3. Insert new simplified roles
    $pdo->exec("INSERT INTO `roles` (`role_name`) VALUES ('Admin') ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`)");
    $pdo->exec("INSERT INTO `roles` (`role_name`) VALUES ('User') ON DUPLICATE KEY UPDATE `role_name` = VALUES(`role_name`)");
    echo "[OK] Roles updated\n";

    // 4. Seed default positions
    $positions = [
        ['Personnel', 'User', 1],
        ['Accountant', 'Admin', 1],
        ['Accounting Support', 'Admin', 1],
        ['Budget Officer', 'Admin', 1],
        ['ASDS', 'Admin', 1],
        ['SDS', 'Admin', 1]
    ];
    $stmt = $pdo->prepare("INSERT INTO `positions` (`position_name`, `mapped_role`, `is_default`) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE `mapped_role` = VALUES(`mapped_role`), `is_default` = VALUES(`is_default`)");
    foreach ($positions as $pos) {
        $stmt->execute($pos);
    }
    echo "[OK] 6 default positions seeded\n";

    // 5. Get the new role IDs
    $adminRoleId = $pdo->query("SELECT `id` FROM `roles` WHERE `role_name` = 'Admin'")->fetchColumn();
    $userRoleId = $pdo->query("SELECT `id` FROM `roles` WHERE `role_name` = 'User'")->fetchColumn();
    echo "[INFO] Admin role ID: $adminRoleId | User role ID: $userRoleId\n";

    // 6. Get position IDs
    $personnelId = $pdo->query("SELECT `id` FROM `positions` WHERE `position_name` = 'Personnel'")->fetchColumn();
    $accountantId = $pdo->query("SELECT `id` FROM `positions` WHERE `position_name` = 'Accountant'")->fetchColumn();
    $budgetOfficerId = $pdo->query("SELECT `id` FROM `positions` WHERE `position_name` = 'Budget Officer'")->fetchColumn();

    // 7. Update existing seed users with positions (skip Super Admin id=1)
    $pdo->exec("UPDATE `users` SET `position_id` = $accountantId WHERE `id` = 2 AND `position_id` IS NULL");
    $pdo->exec("UPDATE `users` SET `position_id` = $budgetOfficerId WHERE `id` = 3 AND `position_id` IS NULL");
    $pdo->exec("UPDATE `users` SET `position_id` = $personnelId WHERE `id` = 4 AND `position_id` IS NULL");
    $pdo->exec("UPDATE `users` SET `position_id` = $personnelId WHERE `id` = 5 AND `position_id` IS NULL");
    echo "[OK] Seed users updated with positions\n";

    // 8. Remap user_roles for seed users
    $pdo->exec("UPDATE `user_roles` SET `role_id` = $adminRoleId WHERE `user_id` = 2");
    $pdo->exec("UPDATE `user_roles` SET `role_id` = $adminRoleId WHERE `user_id` = 3");
    $pdo->exec("UPDATE `user_roles` SET `role_id` = $userRoleId WHERE `user_id` = 4");
    $pdo->exec("UPDATE `user_roles` SET `role_id` = $userRoleId WHERE `user_id` = 5");
    echo "[OK] User roles remapped to simplified system\n";

    echo "\n=== MIGRATION COMPLETE ===\n";
} catch (PDOException $e) {
    echo 'Migration error: ' . $e->getMessage() . "\n";
}
