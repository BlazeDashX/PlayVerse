<?php
require_once('../../../shared/auth_guard.php');
requireRole('admin');

require_once('../db/gameModel.php');

$id = (int)($_GET['id'] ?? 0);

if($id > 0){
    // Optional: Get game info first to delete image file if needed
    // $game = getGameById($id); 
    // unlink(...image...);
    
    if(deleteGame($id)){
        header("Location: ../html/admin_dashboard.php?msg=" . urlencode("Game deleted"));
    } else {
        header("Location: ../html/admin_dashboard.php?msg=" . urlencode("Delete failed"));
    }
} else {
    header("Location: ../html/admin_dashboard.php");
}
exit();
?>