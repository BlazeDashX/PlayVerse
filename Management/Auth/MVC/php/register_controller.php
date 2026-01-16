<?php
require('../../../shared/db.php');

header('Content-Type: application/json');

$response = ["status" => "error", "message" => "Invalid request"];

// Must receive JSON string in POST[data]
if (!isset($_POST['data'])) {
    echo json_encode($response);
    exit();
}

$data = json_decode($_POST['data'], true);

$username = trim($data['username'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm  = $data['confirmPassword'] ?? '';

// ==========================
// 1) PHP Validation (ALL errors together)
// ==========================
$errors = [];

if ($username === '') {
    $errors['username'] = "Username is required.";
} else if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    $errors['username'] = "Username must be 3-20 chars (letters, numbers, underscore).";
}

if ($email === '') {
    $errors['email'] = "Email is required.";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email format.";
}

if ($password === '') {
    $errors['password'] = "Password is required.";
} else if (strlen($password) < 6) {
    $errors['password'] = "Password must be at least 6 characters.";
}

if ($confirm === '') {
    $errors['confirmPassword'] = "Confirm password is required.";
} else if ($password !== '' && $password !== $confirm) {
    $errors['confirmPassword'] = "Passwords do not match.";
}

// If any validation error → return all field errors
if (count($errors) > 0) {
    echo json_encode([
        "status" => "field_error",
        "errors" => $errors
    ]);
    exit();
}

// ==========================
// 2) Check username exists
// ==========================
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    echo json_encode([
        "status" => "field_error",
        "errors" => ["username" => "Username already exists!"]
    ]);
    exit();
}
mysqli_stmt_close($stmt);

// ==========================
// 3) Check email exists
// ==========================
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    echo json_encode([
        "status" => "field_error",
        "errors" => ["email" => "Email already exists!"]
    ]);
    exit();
}
mysqli_stmt_close($stmt);

// ==========================
// 4) Insert User (default role = user)
// ==========================
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')"
);
mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hash);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "✅ Registration successful! You can login now."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "❌ Registration failed! Try again."
    ]);
}

mysqli_stmt_close($stmt);
