<?php
require_once('../../../shared/auth_guard.php');
requireRole('admin');
require_once('../db/gameModel.php');

$id = (int)($_GET['id'] ?? 0);
$game = getGameById($id);

// PHP Validation: If game doesn't exist, kick them out
if (!$game) {
    header("Location: admin_dashboard.php?msg=" . urlencode("Game not found"));
    exit();
}

// Image Path Setup
$imgFile = $game['image_filename'];
$hasImage = !empty($imgFile) && file_exists(__DIR__ . "/../images/uploaded/" . $imgFile);
$displayImg = $hasImage ? "../images/uploaded/" . $imgFile : "../images/default_placeholder.png";
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Game | PlayVerse</title>
    <link rel="stylesheet" href="../css/edit_game.css">
</head>
<body>

    <div class="topbar">
        <h2>✏️ Edit Game</h2>
        <a class="btn-back" href="admin_dashboard.php">Cancel & Back</a>
    </div>

    <div class="container">
        
        <form id="editForm" action="../php/update_game.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $game['id']; ?>">
            
            <div class="edit-card">
                
                <div class="image-section">
                    <div class="preview-box">
                        <img src="<?php echo htmlspecialchars($displayImg); ?>" id="imgPreview" class="preview-img">
                    </div>
                    
                    <label for="imageInput" class="btn-upload">📸 Change Cover Image</label>
                    <input type="file" name="image" id="imageInput" accept="image/*">
                    <small style="color:#888; margin-top:10px;">Max Size: 5MB (JPG, PNG)</small>
                </div>

                <div class="form-section">
                    <h3 style="margin-top:0; color:#4e73df;">Game Details</h3>
                    
                    <div class="form-group">
                        <label>Game Title</label>
                        <input type="text" class="form-control" name="name" id="g_name" 
                               value="<?php echo htmlspecialchars($game['name']); ?>">
                        <small class="error-msg" id="err_name">Name is required</small>
                    </div>

                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" class="form-control" name="category" id="g_cat"
                               value="<?php echo htmlspecialchars($game['category']); ?>">
                        <small class="error-msg" id="err_cat">Category is required</small>
                    </div>

                    <div class="row">
                        <div class="col form-group">
                            <label>Sell Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="sell_price" id="g_sell"
                                   value="<?php echo $game['sell_price']; ?>">
                        </div>
                        <div class="col form-group">
                            <label>Rent Price ($/mo)</label>
                            <input type="number" step="0.01" class="form-control" name="rent_price" id="g_rent"
                                   value="<?php echo $game['rent_price_per_month']; ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col form-group">
                            <label>Stock Qty</label>
                            <input type="number" class="form-control" name="stock_qty" id="g_stock"
                                   value="<?php echo $game['stock_qty']; ?>">
                        </div>
                        <div class="col form-group">
                            <label>Availability</label>
                            <select name="status" class="form-control">
                                <option value="sell" <?php if($game['status']=='sell') echo 'selected'; ?>>Sell Only</option>
                                <option value="rent" <?php if($game['status']=='rent') echo 'selected'; ?>>Rent Only</option>
                                <option value="both" <?php if($game['status']=='both') echo 'selected'; ?>>Both</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <select name="is_active" class="form-control">
                            <option value="1" <?php if($game['is_active']==1) echo 'selected'; ?>>Active (Visible)</option>
                            <option value="0" <?php if($game['is_active']==0) echo 'selected'; ?>>Hidden (Inactive)</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-save">💾 Update Game</button>
                </div>

            </div>
        </form>

    </div>

    <script src="../js/edit_game.js"></script>

</body>
</html>