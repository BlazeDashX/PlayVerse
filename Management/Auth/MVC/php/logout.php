<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Clear session data */
session_unset();
session_destroy();

/* Redirect to login */
header("Location: ../html/login.php");
exit();