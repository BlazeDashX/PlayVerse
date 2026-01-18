<?php
require_once('../../../shared/auth_guard.php');
requireRole('admin');

require_once('../db/gameModel.php');

/* Handle feedback redirection */
function backMsg($m){
    header("Location: ../html/admin_dashboard.php?msg=" . urlencode($m));
    exit();
}

/* Validate primary key */
$id = (int)($_POST['id'] ?? 0);
if($id <= 0) backMsg("Invalid ID");

/* Sanitize form inputs */
$name       = trim($_POST['name'] ?? '');
$category   = trim($_POST['category'] ?? '');
$sell_price = trim($_POST['sell_price'] ?? '');
$rent_price = trim($_POST['rent_price'] ?? '');
$stock_qty  = trim($_POST['stock_qty'] ?? '');
$status     = $_POST['status'] ?? 'sell';
$is_active  = (int)($_POST['is_active'] ?? 1);

/* Process file upload */
$imageName = null;
if(isset($_FILES['image']) && $_FILES['image']['name'] !== ''){
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $imageName = time() . "_" . rand(1000,9999) . "." . $ext;
    $destPath = __DIR__ . "/../images/uploaded/" . $imageName;
    move_uploaded_file($_FILES['image']['tmp_name'], $destPath);
}

/* Consolidate update data */
$data = [
    "name" => $name, 
    "category" => $category,
    "sell_price" => $sell_price, 
    "rent_price" => $rent_price,
    "stock_qty" => $stock_qty, 
    "status" => $status,
    "is_active" => $is_active
];

/* Execute database update */
if(updateGame($id, $data, $imageName)){
    backMsg("Game updated successfully!");
} else {
    backMsg("Update failed.");
}
?>