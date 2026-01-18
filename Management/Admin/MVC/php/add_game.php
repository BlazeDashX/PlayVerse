<?php
require_once('../../../shared/auth_guard.php');
requireRole('admin');

require_once('../db/gameModel.php');

/* Handle redirection messages */
function redirectMsg($m){
    header("Location: ../html/admin_dashboard.php?msg=" . urlencode($m));
    exit();
}

/* Capture form inputs */
$name       = trim($_POST['name'] ?? '');
$category   = trim($_POST['category'] ?? '');
$sell_price = trim($_POST['sell_price'] ?? '');
$rent_price = trim($_POST['rent_price'] ?? '');
$stock_qty  = trim($_POST['stock_qty'] ?? '');
$status     = $_POST['status'] ?? 'sell';

/* Validate required fields */
if($name === '' || $category === '' || $sell_price === '' || $rent_price === '' || $stock_qty === ''){
    redirectMsg("All fields are required.");
}

/* Process image upload */
$imageName = null;
if(isset($_FILES['image']) && $_FILES['image']['name'] !== ''){
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $imageName = time() . "_" . rand(1000,9999) . "." . $ext;
    $destPath = __DIR__ . "/../images/uploaded/" . $imageName;
    
    /* Ensure directory exists */
    if(!is_dir(__DIR__ . "/../images/uploaded/")){
        mkdir(__DIR__ . "/../images/uploaded/", 0777, true);
    }
    move_uploaded_file($_FILES['image']['tmp_name'], $destPath);
}

/* Prepare record data */
$data = [
    "name" => $name, "category" => $category, 
    "sell_price" => $sell_price, "rent_price" => $rent_price, 
    "stock_qty" => $stock_qty, "status" => $status
];

/* Execute database insertion */
if(addGame($data, $imageName)){
    redirectMsg("Game added successfully!");
} else {
    redirectMsg("Database Error: Failed to add game.");
}
?>