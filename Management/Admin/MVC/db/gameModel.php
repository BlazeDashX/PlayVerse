<?php
require_once(__DIR__ . '/../../../shared/db.php');

/* Fetch all records */
function getAllGames() {
    global $conn;
    $sql = "SELECT * FROM games ORDER BY id DESC";
    return mysqli_query($conn, $sql);
}

/* Fetch by ID */
function getGameById($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "SELECT * FROM games WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

/* Create new record */
function addGame($data, $imageName) {
    global $conn;
    $sql = "INSERT INTO games (name, category, sell_price, rent_price_per_month, stock_qty, status, image_filename) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssddiss", 
        $data['name'], 
        $data['category'], 
        $data['sell_price'], 
        $data['rent_price'], 
        $data['stock_qty'], 
        $data['status'],
        $imageName
    );
    return mysqli_stmt_execute($stmt);
}

/* Update existing record */
function updateGame($id, $data, $imageName = null) {
    global $conn;
    
    if($imageName) {
        $sql = "UPDATE games SET name=?, category=?, sell_price=?, rent_price_per_month=?, stock_qty=?, status=?, is_active=?, image_filename=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssddisisi", 
            $data['name'], 
            $data['category'], 
            $data['sell_price'], 
            $data['rent_price'], 
            $data['stock_qty'], 
            $data['status'], 
            $data['is_active'], 
            $imageName, 
            $id
        );
    } else {
        $sql = "UPDATE games SET name=?, category=?, sell_price=?, rent_price_per_month=?, stock_qty=?, status=?, is_active=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssddisis", 
            $data['name'], 
            $data['category'], 
            $data['sell_price'], 
            $data['rent_price'], 
            $data['stock_qty'], 
            $data['status'], 
            $data['is_active'], 
            $id
        );
    }
    return mysqli_stmt_execute($stmt);
}

/* Remove record */
function deleteGame($id) {
    global $conn;
    $stmt = mysqli_prepare($conn, "DELETE FROM games WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}
?>