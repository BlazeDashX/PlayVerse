<?php
require_once('../db/userModel.php');

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'track_view') {
    $gameId = (int)($_POST['game_id'] ?? 0);
    
    if ($gameId > 0) {
        incrementPopularity($gameId);
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid ID"]);
    }
    exit();
}
?>