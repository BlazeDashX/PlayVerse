<?php
require_once('../../../shared/auth_guard.php');
protect_page('admin'); 

// --- CONNECT TO MODELS ---
require_once('../db/dashboardModel.php'); // For Stats
require_once('../db/gameModel.php');      // For Game List

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// --- FETCH DATA ---
$stats = getAdminStats(); // Gets the fixed "All Time" stats
$allGames = getAllGames(); // Gets the inventory

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayVerse | Admin</title>
    <link rel="stylesheet" href="../css/admin_dashboard.css">
    <style>
        /* Popup Styles */
        .popup-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;
            visibility: hidden; opacity: 0; transition: 0.3s; z-index: 999;
        }
        .popup-overlay.show { visibility: visible; opacity: 1; }
        .popup-content { background: white; padding: 30px; border-radius: 8px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
        .popup-btn { background: #4e73df; color: white; border: none; padding: 8px 20px; border-radius: 4px; cursor: pointer; margin-top: 15px; }
    </style>
</head>
<body>

<div class="topbar">
    <h2>🎮 PlayVerse <span class="brand-highlight">Admin</span></h2>
    <div>
        <span>Welcome, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span> &nbsp;
        <a href="../../../Auth/MVC/php/logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<div class="container">

    <div class="kpi-grid">
        <div class="kpi-card bg-blue">
            <div class="kpi-label">Total Games</div>
            <div class="kpi-value"><?php echo $stats['total_games']; ?></div>
        </div>
        <div class="kpi-card bg-green">
            <div class="kpi-label">Stock Units</div>
            <div class="kpi-value"><?php echo $stats['total_stock']; ?></div>
        </div>
        <div class="kpi-card bg-red">
            <div class="kpi-label">Out of Stock</div>
            <div class="kpi-value"><?php echo $stats['out_stock_games']; ?></div>
        </div>
        <div class="kpi-card bg-purple">
            <div class="kpi-label">Total Sold</div>
            <div class="kpi-value"><?php echo $stats['month_sold']; ?></div>
        </div>
        <div class="kpi-card bg-orange">
            <div class="kpi-label">Sell Income</div>
            <div class="kpi-value">$<?php echo number_format($stats['month_sell_earning'], 2); ?></div>
        </div>
        <div class="kpi-card bg-teal">
            <div class="kpi-label">Rent Income</div>
            <div class="kpi-value">$<?php echo number_format($stats['month_rent_earning'], 2); ?></div>
        </div>
    </div>

    <div class="card-section">
        <h3>➕ Add New Game</h3>
        <form action="../php/add_game.php" method="POST" enctype="multipart/form-data">
            
            <div class="form-row">
                <input type="text" name="name" placeholder="Game Title" required>
                <input type="text" name="category" placeholder="Category (e.g. Action)" required>
                <input type="number" name="stock_qty" placeholder="Stock Qty" required>
            </div>
            
            <div class="form-row">
                <input type="number" step="0.01" name="sell_price" placeholder="Sell Price ($)" required>
                <input type="number" step="0.01" name="rent_price" placeholder="Rent Price ($/month)" required>
                <select name="status">
                    <option value="sell">Sell Only</option>
                    <option value="rent">Rent Only</option>
                    <option value="both">Both</option>
                </select>
            </div>

            <div class="form-footer">
                <div class="file-group">
                    <label>Game Poster:</label>
                    <input type="file" name="image">
                </div>
                <button type="submit" class="btn btn-primary">Save to Inventory</button>
            </div>
        </form>
    </div>

    <div class="card-section">
        <h3>📦 Inventory Management</h3>
        <table>
            <thead>
                <tr>
                    <th>Game Name</th>
                    <th>Category</th>
                    <th>Sell</th>
                    <th>Rent</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($g = mysqli_fetch_assoc($allGames)) { ?>
                <tr>
                    <td><strong class="text-highlight"><?php echo htmlspecialchars($g['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($g['category']); ?></td>
                    <td>$<?php echo $g['sell_price']; ?></td>
                    <td>$<?php echo $g['rent_price_per_month']; ?></td>
                    <td>
                        <?php if($g['stock_qty'] > 0) { ?>
                            <span class="stock-status stock-ok"><?php echo $g['stock_qty']; ?></span>
                        <?php } else { ?>
                            <span class="stock-status stock-low">Out of Stock</span>
                        <?php } ?>
                    </td>
                    <td><?php echo strtoupper($g['status']); ?></td>
                    <td>
                        <a href="edit_game.php?id=<?php echo $g['id']; ?>" class="action-link edit">Edit</a>
                        <a href="../php/delete_game.php?id=<?php echo $g['id']; ?>" class="action-link delete" onclick="return confirm('Delete this game?')">Delete</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>

<div id="statusPopup" class="popup-overlay">
    <div class="popup-content">
        <h3 id="popupTitle">Notification</h3>
        <p id="popupMsg"></p>
        <button class="popup-btn" onclick="document.getElementById('statusPopup').classList.remove('show')">OK</button>
    </div>
</div>

<script>
    const msg = "<?php echo isset($msg) ? htmlspecialchars($msg) : ''; ?>";
    if (msg !== "") {
        document.getElementById('popupMsg').innerText = msg;
        document.getElementById('statusPopup').classList.add('show');
    }
</script>

</body>
</html>