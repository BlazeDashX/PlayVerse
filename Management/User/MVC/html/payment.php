<?php
require_once('../../../shared/auth_guard.php');
protect_page('user');
require_once('../db/userModel.php');

/* Extract request parameters */
$gameId = (int)($_GET['id'] ?? 0);
$type = $_GET['type'] ?? 'buy';
$game = getGameDetails($gameId);

if (!$game) die("Invalid Game");

/* Calculate transaction values */
$amount = ($type === 'rent') ? $game['rent_price_per_month'] : $game['sell_price'];
$label = ($type === 'rent') ? "Monthly Rental" : "One-Time Purchase";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout | PlayVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/payment.css">
</head>
<body>

    <div class="checkout-container">
        <div class="terminal-header">
            <h2>SECURE CHECKOUT // <span style="color:#66fcf1">PLAYVERSE</span></h2>
        </div>

        <div class="content-grid">
            <div class="summary-box">
                <img src="../../../Admin/MVC/images/uploaded/<?php echo $game['image_filename']; ?>" class="thumb">
                <div class="summary-details">
                    <h3><?php echo htmlspecialchars($game['name']); ?></h3>
                    <p><?php echo $label; ?></p>
                    <div class="total-price">$<?php echo $amount; ?></div>
                </div>
            </div>

            <form id="paymentForm" class="payment-form">
                <label>Card Holder Name</label>
                <input type="text" id="cardName" placeholder="JOHN DOE" required>

                <label>Card Number</label>
                <input type="text" id="cardNum" placeholder="0000 0000 0000 0000" maxlength="19" required>

                <div class="row">
                    <div class="col">
                        <label>Expiry</label>
                        <input type="text" id="expiry" placeholder="MM/YY" maxlength="5" required>
                    </div>
                    <div class="col">
                        <label>CVV</label>
                        <input type="password" id="cvv" placeholder="123" maxlength="3" required>
                    </div>
                </div>

                <button type="submit" class="btn-pay" id="payBtn">INITIATE PAYMENT</button>
                <p id="msg" class="msg-text"></p>
            </form>
        </div>
        
        <a href="game_details.php?id=<?php echo $gameId; ?>" class="btn-cancel">CANCEL TRANSACTION</a>
    </div>

    <div id="confirmModal" class="modal-overlay">
        <div class="modal-box">
            <h3 class="modal-title">⚠ CONFIRM TRANSACTION</h3>
            <p class="modal-text">
                Transfer <span class="highlight">$<?php echo $amount; ?></span> <br>
                for <b><?php echo htmlspecialchars($game['name']); ?></b>.
            </p>
            <div class="modal-actions">
                <button id="btnYes" class="btn-modal btn-yes">CONFIRM</button>
                <button id="btnNo" class="btn-modal btn-no">CANCEL</button>
            </div>
        </div>
    </div>

    <input type="hidden" id="h_gameId" value="<?php echo $gameId; ?>">
    <input type="hidden" id="h_type" value="<?php echo $type; ?>">
    <input type="hidden" id="h_amount" value="<?php echo $amount; ?>">

    <script src="../js/payment.js"></script>
</body>
</html>