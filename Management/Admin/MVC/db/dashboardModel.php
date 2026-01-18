<?php
require_once(__DIR__ . '/../../../shared/db.php');

/* Get administrative statistics */
function getAdminStats(){
    global $conn;

    /* Initialize statistics array */
    $stats = [
        "total_games" => 0,
        "total_stock" => 0,
        "in_stock_games" => 0,
        "out_stock_games" => 0,
        "month_sold" => 0,
        "month_rented" => 0,
        "month_sell_earning" => 0.0,
        "month_rent_earning" => 0.0
    ];

    /* Fetch inventory metrics */
    $q1 = "SELECT
            COUNT(*) AS total_games,
            COALESCE(SUM(stock_qty),0) AS total_stock,
            SUM(CASE WHEN stock_qty > 0 THEN 1 ELSE 0 END) AS in_stock_games,
            SUM(CASE WHEN stock_qty = 0 THEN 1 ELSE 0 END) AS out_stock_games
        FROM games";
    
    $r1 = mysqli_query($conn, $q1);
    if($r1){
        $row = mysqli_fetch_assoc($r1);
        $stats["total_games"] = (int)$row["total_games"];
        $stats["total_stock"] = (int)$row["total_stock"];
        $stats["in_stock_games"] = (int)$row["in_stock_games"];
        $stats["out_stock_games"] = (int)$row["out_stock_games"];
    }

    /* Fetch transaction metrics */
    $q2 = "SELECT
            SUM(CASE WHEN payment_type='buy' THEN 1 ELSE 0 END) AS month_sold,
            SUM(CASE WHEN payment_type='rent' THEN 1 ELSE 0 END) AS month_rented,
            COALESCE(SUM(CASE WHEN payment_type='buy' THEN amount ELSE 0 END),0) AS month_sell_earning,
            COALESCE(SUM(CASE WHEN payment_type='rent' THEN amount ELSE 0 END),0) AS month_rent_earning
        FROM payments"; 
        
    $r2 = mysqli_query($conn, $q2);
    if($r2){
        $row = mysqli_fetch_assoc($r2);
        $stats["month_sold"] = (int)$row["month_sold"];
        $stats["month_rented"] = (int)$row["month_rented"];
        $stats["month_sell_earning"] = (float)$row["month_sell_earning"];
        $stats["month_rent_earning"] = (float)$row["month_rent_earning"];
    }

    return $stats;
}
?>