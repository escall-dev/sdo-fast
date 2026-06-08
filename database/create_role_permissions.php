<?php
/**
 * Migration: Create role_permissions table and seed default permissions.
 */
require_once __DIR__ . '/../config/database.php';

try {
    // 1. Create table
    $fastPDO->exec("
        CREATE TABLE IF NOT EXISTS role_permissions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            role_id INT NOT NULL,
            permission_key VARCHAR(100) NOT NULL,
            is_enabled TINYINT(1) NOT NULL DEFAULT 0,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY role_permission_unique (role_id, permission_key),
            FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "[OK] Table role_permissions created/verified\n";

    // 2. Fetch all roles to match seed role names with IDs
    $roles = $fastPDO->query("SELECT id, role_name FROM roles")->fetchAll();
    $roleIds = [];
    foreach ($roles as $r) {
        $roleIds[$r['role_name']] = (int)$r['id'];
    }

    // Define initial defaults for each role
    $permissionKeys = ['view', 'encode', 'edit', 'approve', 'delete', 'manage_users', 'configure_system', 'view_bactrack'];

    $defaultAssignments = [
        'Super Admin' => [
            'view' => 1,
            'encode' => 1,
            'edit' => 1,
            'approve' => 1,
            'delete' => 1,
            'manage_users' => 1,
            'configure_system' => 1,
            'view_bactrack' => 1
        ],
        'Admin' => [
            'view' => 1,
            'encode' => 1,
            'edit' => 1,
            'approve' => 1,
            'delete' => 0,
            'manage_users' => 0,
            'configure_system' => 0,
            'view_bactrack' => 0
        ],
        'Accounting Staff' => [
            'view' => 1,
            'encode' => 1,
            'edit' => 0,
            'approve' => 1,
            'delete' => 0,
            'manage_users' => 0,
            'configure_system' => 0,
            'view_bactrack' => 0
        ],
        'Budget Officer' => [
            'view' => 1,
            'encode' => 0,
            'edit' => 0,
            'approve' => 0,
            'delete' => 0,
            'manage_users' => 0,
            'configure_system' => 0,
            'view_bactrack' => 0
        ],
        'Approver' => [
            'view' => 1,
            'encode' => 0,
            'edit' => 0,
            'approve' => 0,
            'delete' => 0,
            'manage_users' => 0,
            'configure_system' => 0,
            'view_bactrack' => 0
        ],
        'Requestor' => [
            'view' => 1,
            'encode' => 0,
            'edit' => 0,
            'approve' => 0,
            'delete' => 0,
            'manage_users' => 0,
            'configure_system' => 0,
            'view_bactrack' => 0
        ],
        'User' => [
            'view' => 1,
            'encode' => 0,
            'edit' => 0,
            'approve' => 0,
            'delete' => 0,
            'manage_users' => 0,
            'configure_system' => 0,
            'view_bactrack' => 0
        ]
    ];

    $stmt = $fastPDO->prepare("
        INSERT INTO role_permissions (role_id, permission_key, is_enabled)
        VALUES (:role_id, :permission_key, :is_enabled)
        ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled)
    ");

    foreach ($defaultAssignments as $roleName => $perms) {
        if (isset($roleIds[$roleName])) {
            $roleId = $roleIds[$roleName];
            foreach ($perms as $key => $enabled) {
                $stmt->execute([
                    'role_id' => $roleId,
                    'permission_key' => $key,
                    'is_enabled' => $enabled
                ]);
            }
            echo "[OK] Default permissions seeded for role: $roleName (ID: $roleId)\n";
        }
    }

    echo "\n=== MIGRATION COMPLETE ===\n";
} catch (PDOException $e) {
    echo 'Migration error: ' . $e->getMessage() . "\n";
}
