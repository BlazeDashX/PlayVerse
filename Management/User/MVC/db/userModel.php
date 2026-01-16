<?php
require_once(__DIR__ . '/../../../shared/db.php');

// 1. Get Popular Games + Check Ownership
function getPopularGames($userId, $limit = 4) {
    global $conn;
    // Join with payments to see if THIS user bought it
    $sql = "SELECT g.*, 
            COALESCE(s.download_count, 0) as downloads,
            p.payment_type as owned_status
            FROM games g
            LEFT JOIN game_stats s ON g.id = s.game_id
            LEFT JOIN payments p ON g.id = p.game_id AND p.user_id = ? AND p.payment_status = 'success'
            WHERE g.is_active = 1
            ORDER BY downloads DESC
            LIMIT ?";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $userId, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $games = [];
    while($row = mysqli_fetch_assoc($result)) {
        $games[] = $row;
    }
    return $games;
}

// 2. Get All Games + Check Ownership
function getAllGamesUser($userId) {
    global $conn;
    $sql = "SELECT g.*, p.payment_type as owned_status
            FROM games g
            LEFT JOIN payments p ON g.id = p.game_id AND p.user_id = ? AND p.payment_status = 'success'
            WHERE g.is_active = 1 
            ORDER BY g.id DESC";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $games = [];
    while($row = mysqli_fetch_assoc($result)) {
        $games[] = $row;
    }
    return $games;
}

// 3. Get Single Game Details
function getGameDetails($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM games WHERE id = ? AND is_active = 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// 4. Increment Popularity (View Count)
function incrementPopularity($gameId) {
    global $conn;
    $check = mysqli_query($conn, "SELECT game_id FROM game_stats WHERE game_id=$gameId");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO game_stats (game_id, popularity_count) VALUES ($gameId, 1)");
    } else {
        mysqli_query($conn, "UPDATE game_stats SET popularity_count = popularity_count + 1 WHERE game_id = $gameId");
    }
}
?>