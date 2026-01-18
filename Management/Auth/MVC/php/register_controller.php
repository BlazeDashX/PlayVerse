<?php
require('../../../shared/db.php');

header('Content-Type: application/json');

/* Initialize response */
$response = ["status" => "error", "message" => "Invalid request"];

/* Validate POST data */
if (!isset($_POST['data'])) {
    echo json_encode($response);
    exit();
}

$data = json_decode($_POST['data'], true);

$username = trim($data['username'] ?? '');
$email    = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm  = $data['confirmPassword'] ?? '';

/* Server-side validation */
$errors = [];

if ($username === '') {
    $errors['username'] = "Username is required.";
} else if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
    $errors['username'] = "Username: 3-20 chars.";
}

if ($email === '') {
    $errors['email'] = "Email is required.";
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Invalid email format.";
}

if ($password === '') {
    $errors['password'] = "Password is required.";
} else if (strlen($password) < 6) {
    $errors['password'] = "Min 6 characters.";
}

if ($confirm === '') {
    $errors['confirmPassword'] = "Confirm password required.";
} else if ($password !== '' && $password !== $confirm) {
    $errors['confirmPassword'] = "Passwords mismatch.";
}

if (count($errors) > 0) {
    echo json_encode([
        "status" => "field_error",
        "errors" => $errors
    ]);
    exit();
}

/* Check duplicate username */
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    echo json_encode([
        "status" => "field_error",
        "errors" => ["username" => "Username taken!"]
    ]);
    exit();
}
mysqli_stmt_close($stmt);

/* Check duplicate email */
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    echo json_encode([
        "status" => "field_error",
        "errors" => ["email" => "Email registered!"]
    ]);
    exit();
}
mysqli_stmt_close($stmt);

/* Insert new user */
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'user')"
);
mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hash);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        "status" => "success",
        "message" => "Registration successful!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Registration failed!"
    ]);
}

mysqli_stmt_close($stmt);