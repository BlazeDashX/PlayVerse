<?php
require_once('../../../shared/auth_guard.php');
protect_page('user');
require_once('../db/userModel.php');

$id = (int)($_GET['id'] ?? 0);
$userId = $_SESSION['user_id'];

/* Fetch game details */
$game = getGameDetails($id);
if (!$game) {
    header("Location: user_dashboard.php");
    exit();
}

/* Check ownership status */
$checkOwn = mysqli_query($conn, "SELECT payment_type FROM payments WHERE user_id=$userId AND game_id=$id AND payment_status='success' LIMIT 1");
$ownedData = mysqli_fetch_assoc($checkOwn);
$isOwned = $ownedData ? true : false;

/* Resolve image path */
$imgFile = $game['image_filename'];
$displayImg = (!empty($imgFile) && file_exists(__DIR__ . "/../../../Admin/MVC/images/uploaded/" . $imgFile)) 
    ? "../../../Admin/MVC/images/uploaded/" . $imgFile 
    : "../../../Admin/MVC/images/default_game.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($game['name']); ?> | PlayVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/game_details.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">🎮 PlayVerse</div>
        <div class="nav-links">
            <a href="user_dashboard.php" class="btn-back">← BACK TO LIBRARY</a>
        </div>
    </nav>

    <div class="container">
        <div class="details-card">
            <div class="image-section">
                <img src="<?php echo htmlspecialchars($displayImg); ?>" alt="Cover" class="game-cover">
            </div>

            <div class="info-section">
                <span class="category-tag"><?php echo htmlspecialchars($game['category']); ?></span>
                <h1 class="game-title"><?php echo htmlspecialchars($game['name']); ?></h1>
                
                <div class="stock-status">
                    <?php if($game['stock_qty'] > 0): ?>
                        <span class="in-stock">✓ INSTANT DELIVERY</span>
                    <?php else: ?>
                        <span class="out-stock">⚠ OUT OF STOCK</span>
                    <?php endif; ?>
                </div>

                <hr class="divider">

                <div class="action-area">
                    <?php if ($isOwned): ?>
                        <div class="option-box" style="border-color: #1cc88a;">
                            <div class="price-label" style="color:#1cc88a;">✅ IN LIBRARY</div>
                            <div class="price-value">READY</div>
                            
                            <a href="../../../Admin/MVC/images/uploaded/<?php echo $game['image_filename']; ?>" 
                               download="<?php echo $game['image_filename']; ?>"
                               class="btn-action btn-buy" 
                               style="background:#1cc88a; color:white;"
                               onclick="trackDownload(<?php echo $game['id']; ?>)">
                               ⬇ DOWNLOAD GAME
                            </a>
                        </div>
                    <?php else: ?>
                        <?php if($game['status'] !== 'rent' && $game['stock_qty'] > 0): ?>
                        <div class="option-box">
                            <div class="price-label">OWN FOREVER</div>
                            <div class="price-value">$<?php echo $game['sell_price']; ?></div>
                            <a href="payment.php?id=<?php echo $game['id']; ?>&type=buy" class="btn-action btn-buy">BUY NOW</a>
                        </div>
                        <?php endif; ?>

                        <?php if($game['status'] !== 'sell' && $game['stock_qty'] > 0): ?>
                        <div class="option-box">
                            <div class="price-label">MONTHLY ACCESS</div>
                            <div class="price-value">$<?php echo $game['rent_price_per_month']; ?><span>/mo</span></div>
                            <a href="payment.php?id=<?php echo $game['id']; ?>&type=rent" class="btn-action btn-rent">RENT NOW</a>
                        </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="gameId" value="<?php echo $game['id']; ?>">
    
    <script>
        /* Record game download */
        function trackDownload(gid) {
            let x = new XMLHttpRequest();
            x.open('POST', '../php/library_controller.php', true);
            x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            x.send('action=record_download&game_id=' + gid);
            alert("Download Started!");
        }
    </script>
</body>
</html>