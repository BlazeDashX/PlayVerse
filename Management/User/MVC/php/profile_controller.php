<?php
session_start();
require_once('../db/profileModel.php');

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? 0;
$action = $_POST['action'] ?? '';

/* Retrieve profile data */
if ($action === 'fetch_data') {
    $user = getUserInfo($userId);
    $stats = getUserStats($userId);
    $badges = getUserBadges($userId);

    echo json_encode([
        "status" => "success", 
        "user" => $user, 
        "stats" => $stats,
        "badges" => $badges 
    ]);
    exit();
}

/* Update user identity */
if ($action === 'update_info') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if(empty($username) || empty($email)) {
        echo json_encode(["status" => "error", "message" => "Fields required"]);
        exit();
    }

    if(updateProfile($userId, $username, $email)) {
        $_SESSION['username'] = $username; 
        echo json_encode(["status" => "success", "message" => "Identity updated"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Credentials taken"]);
    }
    exit();
}

/* Modify account password */
if ($action === 'update_password') {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if(empty($old) || empty($new) || empty($confirm)) {
        echo json_encode(["status" => "error", "message" => "Fields required"]);
        exit();
    }

    if($new !== $confirm) {
        echo json_encode(["status" => "error", "message" => "Passwords mismatch"]);
        exit();
    }

    if(strlen($new) < 6) {
        echo json_encode(["status" => "error", "message" => "Minimum 6 characters"]);
        exit();
    }

    if(!verifyCurrentPassword($userId, $old)) {
        echo json_encode(["status" => "error", "message" => "Incorrect password"]);
        exit();
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    if(updatePassword($userId, $hash)) {
        echo json_encode(["status" => "success", "message" => "Password changed"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database error"]);
    }
    exit();
}

/* Process account deactivation */
if ($action === 'delete_account') {
    if(deactivateAccount($userId)) {
        session_destroy();
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Deactivation failed"]);
    }
    exit();
}
?>