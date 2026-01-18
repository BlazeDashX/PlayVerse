<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Enforce role-based access */
function protect_page($allowed_role) {
    if (!isset($_SESSION['user_id'])) {
        /* Redirect unauthorized guest */
        header("Location: ../../../Auth/MVC/html/login.php");
        exit();
    }

    if ($_SESSION['role'] !== $allowed_role) {
        /* Redirect incorrect role */
        if ($_SESSION['role'] === 'admin') {
            header("Location: ../../../Admin/MVC/html/admin_dashboard.php");
        } else {
            header("Location: ../../../User/MVC/html/user_dashboard.php");
        }
        exit();
    }
}

/* Alias for protection */
function requireRole($role) {
    protect_page($role);
}
?>