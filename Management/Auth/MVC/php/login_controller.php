<?php
require('../../../shared/db.php');
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Check request data */
if(!isset($_POST['data'])){
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$data = json_decode($_POST['data'], true);
$usernameOrEmail = trim($data['usernameOrEmail'] ?? '');
$password        = $data['password'] ?? '';

/* Input validation */
$errors = [];
if($usernameOrEmail === '') $errors['usernameOrEmail'] = "Username/Email is required.";
if($password === '') $errors['password'] = "Password is required.";

if(count($errors) > 0){
    echo json_encode(["status" => "field_error", "errors" => $errors]);
    exit();
}

/* Query user record */
$stmt = mysqli_prepare($conn, "SELECT id, username, email, password_hash, role, is_active FROM users WHERE username = ? OR email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $usernameOrEmail, $usernameOrEmail);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

/* Verify credentials */
if(!$user || !password_verify($password, $user['password_hash'])){
    echo json_encode(["status" => "error", "message" => "Invalid credentials!"]);
    exit();
}

/* Check account status */
if((int)$user['is_active'] !== 1){
    echo json_encode(["status" => "error", "message" => "Account disabled!"]);
    exit();
}

/* Establish session */
$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];

echo json_encode([
    "status" => "success",
    "message" => "Login success",
    "role" => $user['role']
]);
?>