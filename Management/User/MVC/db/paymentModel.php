<?php
require_once(__DIR__ . '/../../../shared/db.php');

function processPayment($userId, $gameId, $type, $amount, $holder, $last4, $expiry) {
    global $conn;

    // 1. Insert Payment Record
    $sql = "INSERT INTO payments (user_id, game_id, payment_type, amount, card_holder_name, card_last4, card_expiry, payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'success')";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iisdsss", $userId, $gameId, $type, $amount, $holder, $last4, $expiry);
    
    if (mysqli_stmt_execute($stmt)) {
        // 2. Update Game Stats (Increment Purchase or Rental count)
        $col = ($type === 'buy') ? "purchase_count" : "rental_count";
        
        // Ensure stat row exists first
        $check = mysqli_query($conn, "SELECT game_id FROM game_stats WHERE game_id=$gameId");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO game_stats (game_id) VALUES ($gameId)");
        }
        
        // Update count
        mysqli_query($conn, "UPDATE game_stats SET $col = $col + 1 WHERE game_id = $gameId");
        
        // 3. Decrease Stock (Only if buying)
        if ($type === 'buy') {
            mysqli_query($conn, "UPDATE games SET stock_qty = stock_qty - 1 WHERE id = $gameId AND stock_qty > 0");
        }

        return true;
    }
    return false;
}
?>