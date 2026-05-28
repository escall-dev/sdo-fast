<?php
/**
 * Global Header Include for SDO FAST.
 * Establishes secure sessions, CSRF tokens, and base styling.
 */

require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/auth.php';

// Generate CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Determine active theme (Admin: #1b4a9a vs Template: #0f4c75)
$bodyClass = '';
if (isset($pageTheme) && $pageTheme === 'template') {
    $bodyClass = 'theme-template';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . " | SDO FAST" : "SDO FAST - Financial Accounting Services & Transactions"; ?></title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Font Awesome 6.5.1 CDN (Sidebar Nav Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Boxicons 2.1.4 CDN (Logout Icon) -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    
    <!-- Custom Theme Stylesheet -->
    <link rel="stylesheet" href="<?php echo env('APP_URL'); ?>/assets/css/style.css">
</head>
<body class="<?php echo $bodyClass; ?>">
    <script>
        if (localStorage.getItem('sidebar-collapsed') === 'true') {
            document.body.classList.add('sidebar-collapsed');
        }
    </script>
    <div class="wrapper">
