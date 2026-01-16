<?php
session_start();
require_once('../db/profileModel.php');

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? 0;
$action = $_POST['action'] ?? '';

// --- 1. FETCH DATA ---
if ($action === 'fetch_data') {
    $user = getUserInfo($userId);
    $stats = getUserStats($userId);
    
    // NEW: Get Badges
    $badges = getUserBadges($userId);

    echo json_encode([
        "status" => "success", 
        "user" => $user, 
        "stats" => $stats,
        "badges" => $badges 
    ]);
    exit();
}

// --- 2. UPDATE INFO ---
if ($action === 'update_info') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // PHP Validation
    if(empty($username) || empty($email)) {
        echo json_encode(["status" => "error", "message" => "Fields cannot be empty"]);
        exit();
    }

    // Try to update
    // If this fails, it's usually because of a UNIQUE constraint in DB (Duplicate username/email)
    if(updateProfile($userId, $username, $email)) {
        $_SESSION['username'] = $username; 
        echo json_encode(["status" => "success", "message" => "Identity Updated Successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Username or Email already taken!"]);
    }
    exit();
}

// --- 3. UPDATE PASSWORD ---
if ($action === 'update_password') {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    // PHP Validation
    if(empty($old) || empty($new) || empty($confirm)) {
        echo json_encode(["status" => "error", "message" => "All password fields are required"]);
        exit();
    }

    if($new !== $confirm) {
        echo json_encode(["status" => "error", "message" => "New passwords do not match"]);
        exit();
    }

    if(strlen($new) < 6) {
        echo json_encode(["status" => "error", "message" => "Password must be 6+ characters"]);
        exit();
    }

    if(!verifyCurrentPassword($userId, $old)) {
        echo json_encode(["status" => "error", "message" => "Current password is incorrect"]);
        exit();
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    if(updatePassword($userId, $hash)) {
        echo json_encode(["status" => "success", "message" => "Passphrase changed successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "System Database Error"]);
    }
    exit();
}

// --- 4. DELETE ACCOUNT ---
if ($action === 'delete_account') {
    if(deactivateAccount($userId)) {
        session_destroy();
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Could not delete account"]);
    }
    exit();
}
?>