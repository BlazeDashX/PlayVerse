<?php
require_once(__DIR__ . '/../../../shared/db.php');

function checkAndUnlock($userId, $actionType) {
    global $conn;

    // 1. Get User Stats
    // Count Purchases
    $qBuy = mysqli_query($conn, "SELECT COUNT(*) as c FROM payments WHERE user_id=$userId AND payment_type='buy' AND payment_status='success'");
    $buyCount = mysqli_fetch_assoc($qBuy)['c'];

    // Count Rentals
    $qRent = mysqli_query($conn, "SELECT COUNT(*) as c FROM payments WHERE user_id=$userId AND payment_type='rent' AND payment_status='success'");
    $rentCount = mysqli_fetch_assoc($qRent)['c'];

    // Count Downloads
    $qDown = mysqli_query($conn, "SELECT SUM(download_count) as c FROM game_stats"); 
    // Wait! user downloads aren't tracked individually in a specific table in your schema 
    // (Only game_stats tracks total downloads). 
    // LIMITATION: For now, we will assume "ActionType" triggers specific badges directly.
    
    // --- BADGE 1: FIRST PURCHASE ---
    if ($actionType === 'buy' && $buyCount >= 1) {
        awardBadge($userId, 'first_buy');
    }

    // --- BADGE 2: FIRST RENTAL ---
    if ($actionType === 'rent' && $rentCount >= 1) {
        awardBadge($userId, 'renter');
    }
    
    // --- BADGE 3: DOWNLOADER (Simulated for this demo) ---
    if ($actionType === 'download') {
        // Since we don't track user downloads, we just award it on the first download click for demo purposes
        awardBadge($userId, 'downloader');
    }
}

function awardBadge($userId, $code) {
    global $conn;
    
    // Get Achievement ID
    $qA = mysqli_query($conn, "SELECT id FROM achievements WHERE code='$code' LIMIT 1");
    $ach = mysqli_fetch_assoc($qA);
    
    if(!$ach) return; // Achievement doesn't exist in DB
    
    $achId = $ach['id'];

    // Check if user already has it
    $check = mysqli_query($conn, "SELECT * FROM user_achievements WHERE user_id=$userId AND achievement_id=$achId");
    
    if(mysqli_num_rows($check) == 0) {
        // INSERT NEW ACHIEVEMENT
        $stmt = mysqli_prepare($conn, "INSERT INTO user_achievements (user_id, achievement_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ii", $userId, $achId);
        mysqli_stmt_execute($stmt);
    }
}
?>