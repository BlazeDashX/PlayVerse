<?php
session_start();
require_once('../db/userModel.php');
require_once('../../../shared/db.php');

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? 0;
$action = $_POST['action'] ?? '';

/* Fetch popular titles */
if ($action === 'fetch_popular') {
    $games = getPopularGames($userId, 4);
    echo json_encode($games);
    exit();
}

/* Fetch user catalog */
if ($action === 'fetch_all') {
    $games = getAllGamesUser($userId);
    echo json_encode($games);
    exit();
}

/* Handle download telemetry */
if ($action === 'record_download') {
    $gameId = (int)($_POST['game_id'] ?? 0);
    
    if ($gameId > 0) {
        /* Initialize stats row */
        $check = mysqli_query($conn, "SELECT game_id FROM game_stats WHERE game_id=$gameId");
        if (mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "INSERT INTO game_stats (game_id, download_count) VALUES ($gameId, 1)");
        } else {
            /* Update download metrics */
            mysqli_query($conn, "UPDATE game_stats SET download_count = download_count + 1, last_download_at = NOW() WHERE game_id = $gameId");
        }

        /* Check achievement criteria */
        require_once('../db/achievementModel.php');
        checkAndUnlock($userId, 'download');

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error"]);
    }
    exit();
}

echo json_encode(["status" => "error", "message" => "Invalid Action"]);
?>