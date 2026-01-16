<?php
session_start();
require_once('../db/paymentModel.php');

header('Content-Type: application/json');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'pay') {
    // 1. Decode JSON Data
    if (!isset($_POST['data'])) {
        echo json_encode(["status" => "error", "message" => "No data"]);
        exit();
    }
    $data = json_decode($_POST['data'], true);

    // 2. Validate Inputs (Demo Logic)
    $cardNum = str_replace(' ', '', $data['cardNumber']);
    $cvv = $data['cvv'];
    
    if (strlen($cardNum) !== 16 || !ctype_digit($cardNum)) {
        echo json_encode(["status" => "error", "message" => "Invalid Card Number (must be 16 digits)"]);
        exit();
    }
    if (strlen($cvv) !== 3 || !ctype_digit($cvv)) {
        echo json_encode(["status" => "error", "message" => "Invalid CVV"]);
        exit();
    }

    // 3. Prepare Data for DB
    $userId = $_SESSION['user_id'];
    $gameId = (int)$data['gameId'];
    $type = $data['type']; // 'buy' or 'rent'
    $amount = (float)$data['amount'];
    $holder = $data['holder'];
    $last4 = substr($cardNum, -4);
    $expiry = $data['expiry'];

    // 4. Process
    if (processPayment($userId, $gameId, $type, $amount, $holder, $last4, $expiry)) {
        
        // --- NEW: TRIGGER ACHIEVEMENT CHECK ---
        require_once('../db/achievementModel.php');
        checkAndUnlock($userId, $type); // $type is 'buy' or 'rent'
        // -
    echo json_encode(["status" => "success", "message" => "Transaction Approved"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database Error"]);
    }
    exit();


}
?>