<?php
/**
 * Migration: Align positions and user roles, and update role-based permissions.
 */
require_once __DIR__ . '/../config/database.php';

try {
    if ($fastPDO === null) {
        throw new Exception("Database connection not established.");
    }

    echo "=== 1. UPDATING POSITION TO ROLE MAPPINGS ===\n";
    $mappings = [
        'Accountant' => 'Accounting Staff',
        'Accounting Support' => 'Accounting Staff',
        'Budget Officer' => 'Budget Officer',
        'ASDS' => 'Approver',
        'SDS' => 'Approver',
        'Personnel' => 'User'
    ];

    $posUpdateStmt = $fastPDO->prepare("UPDATE positions SET mapped_role = :role WHERE position_name = :name");
    foreach ($mappings as $posName => $roleName) {
        $posUpdateStmt->execute([
            'role' => $roleName,
            'name' => $posName
        ]);
        echo "[OK] Position '{$posName}' mapped to role '{$roleName}'\n";
    }

    echo "\n=== 2. ALIGNING EXISTING USER ROLES ===\n";
    $users = $fastPDO->query("SELECT id, username, position_id FROM users WHERE position_id IS NOT NULL")->fetchAll();
    
    foreach ($users as $user) {
        // Fetch mapped role for user's position
        $posStmt = $fastPDO->prepare("SELECT position_name, mapped_role FROM positions WHERE id = :id");
        $posStmt->execute(['id' => $user['position_id']]);
        $pos = $posStmt->fetch();
        
        if ($pos) {
            $mappedRoleName = $pos['mapped_role'];
            // Fetch role ID
            $roleStmt = $fastPDO->prepare("SELECT id FROM roles WHERE role_name = :name");
            $roleStmt->execute(['name' => $mappedRoleName]);
            $roleId = $roleStmt->fetchColumn();
            
            if ($roleId) {
                // Remove any old role assignments to avoid duplicate roles
                $delStmt = $fastPDO->prepare("DELETE FROM user_roles WHERE user_id = :user_id");
                $delStmt->execute(['user_id' => $user['id']]);

                // Update user_roles entry
                $urStmt = $fastPDO->prepare("
                    INSERT INTO user_roles (user_id, role_id) 
                    VALUES (:user_id, :role_id) 
                ");
                $urStmt->execute([
                    'user_id' => $user['id'],
                    'role_id' => $roleId
                ]);
                echo "[OK] User '{$user['username']}' (position '{$pos['position_name']}') assigned to role '{$mappedRoleName}' (ID: {$roleId})\n";
            }
        }
    }

    echo "\n=== 3. CONFIGURING ROLE-SPECIFIC PERMISSIONS ===\n";
    
    function setRolePermission($pdo, $roleName, $key, $enabled) {
        $roleId = $pdo->prepare("SELECT id FROM roles WHERE role_name = :name");
        $roleId->execute(['name' => $roleName]);
        $id = $roleId->fetchColumn();
        
        if ($id) {
            $stmt = $pdo->prepare("
                INSERT INTO role_permissions (role_id, permission_key, is_enabled)
                VALUES (:role_id, :key, :enabled)
                ON DUPLICATE KEY UPDATE is_enabled = VALUES(is_enabled)
            ");
            $stmt->execute([
                'role_id' => $id,
                'key' => $key,
                'enabled' => $enabled
            ]);
            echo "[OK] Permission '{$key}' set to {$enabled} for role '{$roleName}' (ID: {$id})\n";
        } else {
            echo "[WARNING] Role '{$roleName}' not found in database\n";
        }
    }

    // Accounting Staff permissions: view, encode, approve
    setRolePermission($fastPDO, 'Accounting Staff', 'view', 1);
    setRolePermission($fastPDO, 'Accounting Staff', 'encode', 1);
    setRolePermission($fastPDO, 'Accounting Staff', 'approve', 1);
    setRolePermission($fastPDO, 'Accounting Staff', 'view_bactrack', 0);

    // Budget Officer permissions: view, approve
    setRolePermission($fastPDO, 'Budget Officer', 'view', 1);
    setRolePermission($fastPDO, 'Budget Officer', 'approve', 1);
    setRolePermission($fastPDO, 'Budget Officer', 'view_bactrack', 0);

    // Approver permissions: view, approve
    setRolePermission($fastPDO, 'Approver', 'view', 1);
    setRolePermission($fastPDO, 'Approver', 'approve', 1);
    setRolePermission($fastPDO, 'Approver', 'view_bactrack', 0);

    echo "\n=== MIGRATION COMPLETE ===\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
