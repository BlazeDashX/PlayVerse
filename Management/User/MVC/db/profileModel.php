<?php
require_once(__DIR__ . '/../../../shared/db.php');

// 1. Get Basic User Info
function getUserInfo($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT username, email, created_at FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    return mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// 2. Get Stats (Achievements & Total Money Spent)
function getUserStats($id) {
    global $conn;
    
    // Count Achievements
    $stmt1 = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM user_achievements WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt1, "i", $id);
    mysqli_stmt_execute($stmt1);
    $ach = mysqli_stmt_get_result($stmt1)->fetch_assoc();

    // Sum Payments (Total Value)
    $stmt2 = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount), 0.00) as total_spent FROM payments WHERE user_id = ? AND payment_status = 'success'");
    mysqli_stmt_bind_param($stmt2, "i", $id);
    mysqli_stmt_execute($stmt2);
    $money = mysqli_stmt_get_result($stmt2)->fetch_assoc();

    return [
        "achievements" => $ach['count'],
        "total_spent" => $money['total_spent']
    ];
}

// 3. Update Basic Info
function updateProfile($id, $username, $email) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE users SET username = ?, email = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $username, $email, $id);
    return mysqli_stmt_execute($stmt);
}

// 4. Check Password
function verifyCurrentPassword($id, $password) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT password_hash FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt)->fetch_assoc();
    return password_verify($password, $res['password_hash']);
}

// 5. Update Password
function updatePassword($id, $newHash) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE users SET password_hash = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $newHash, $id);
    return mysqli_stmt_execute($stmt);
}

// 6. Delete Account (Soft Delete)
function deactivateAccount($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "UPDATE users SET is_active = 0 WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

function getUserBadges($userId) {
    global $conn;
    $sql = "SELECT a.title, a.description, a.code 
            FROM user_achievements ua 
            JOIN achievements a ON ua.achievement_id = a.id 
            WHERE ua.user_id = ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $badges = [];
    while($row = mysqli_fetch_assoc($result)) {
        $badges[] = $row;
    }
    return $badges;
}
?>