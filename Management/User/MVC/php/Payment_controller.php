<?php
session_start();
require_once('../db/paymentModel.php');

header('Content-Type: application/json');

/* Check authentication */
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$action = $_POST['action'] ?? '';

if ($action === 'pay') {
    /* Decode request payload */
    if (!isset($_POST['data'])) {
        echo json_encode(["status" => "error", "message" => "No data"]);
        exit();
    }
    $data = json_decode($_POST['data'], true);

    /* Validate payment credentials */
    $cardNum = str_replace(' ', '', $data['cardNumber']);
    $cvv = $data['cvv'];
    
    if (strlen($cardNum) !== 16 || !ctype_digit($cardNum)) {
        echo json_encode(["status" => "error", "message" => "Invalid Card Number"]);
        exit();
    }
    if (strlen($cvv) !== 3 || !ctype_digit($cvv)) {
        echo json_encode(["status" => "error", "message" => "Invalid CVV"]);
        exit();
    }

    /* Map transaction data */
    $userId = $_SESSION['user_id'];
    $gameId = (int)$data['gameId'];
    $type = $data['type']; 
    $amount = (float)$data['amount'];
    $holder = $data['holder'];
    $last4 = substr($cardNum, -4);
    $expiry = $data['expiry'];

    /* Execute transaction logic */
    if (processPayment($userId, $gameId, $type, $amount, $holder, $last4, $expiry)) {
        
        /* Evaluate achievement unlocks */
        require_once('../db/achievementModel.php');
        checkAndUnlock($userId, $type); 

        echo json_encode(["status" => "success", "message" => "Transaction Approved"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database Error"]);
    }
    exit();
}
?>