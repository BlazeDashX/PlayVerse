<?php
require_once('../../../shared/auth_guard.php');
protect_page('user');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PlayVerse | Nexus</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/user_dashboard.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">🎮 PlayVerse</div>
        <div class="nav-links">
            <span>PILOT // <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
            <a href="profile.php" class="btn-profile">MY PROFILE</a>
            <a href="../../../Auth/MVC/php/logout.php" class="btn-logout">LOGOUT</a>
        </div>
    </nav>

    <div class="container">
        
        <section id="section-owned" class="section" style="display: none;">
            <h2 class="section-title">🏆 MY ARSENAL</h2>
            <div class="game-grid" id="owned-container"></div>
        </section>

        <section id="section-rented" class="section" style="display: none;">
            <h2 class="section-title">⏳ ACTIVE MISSIONS (RENTALS)</h2>
            <div class="game-grid" id="rented-container"></div>
        </section>

        <section class="section">
            <h2 class="section-title">🔥 HYPER TRENDING</h2>
            <div class="game-grid" id="popular-container">
                <div class="loader">Loading Neural Network...</div>
            </div>
        </section>

        <section class="section">
            <h2 class="section-title">🌏 GLOBAL MARKETPLACE</h2>
            <div class="game-grid" id="all-games-container">
                <div class="loader">Loading Inventory...</div>
            </div>
        </section>

    </div>

    <script src="../js/user_dashboard.js"></script>

</body>
</html>