<?php
// Use this on every protected page (admin/user dashboard etc.)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../Auth/MVC/html/login.php");
    exit();
}

// Optional role check helper (call with required role)
function requireRole($role){
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        // if role mismatch, kick to login
        header("Location: ../../Auth/MVC/html/login.php");
        exit();
    }
}
