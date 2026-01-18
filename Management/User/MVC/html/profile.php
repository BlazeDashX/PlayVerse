<?php
require_once('../../../shared/auth_guard.php');
protect_page('user');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Pilot Profile | PlayVerse</title>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/profile.css">
</head>

<body>

    <nav class="navbar">
        <div class="logo">🎮 PlayVerse</div>
        <div class="nav-links">
            <a href="user_dashboard.php" class="btn-back">← BACK TO NEXUS</a>
        </div>
    </nav>

    <div class="container">
        <div class="profile-grid">

            <div class="profile-card">
                <div class="avatar-circle">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <h2 id="disp_username">Loading...</h2>
                <p id="disp_email">...</p>
                <div class="badge-joined" id="disp_joined">Joined: ...</div>

                <hr class="divider">

                <div class="stat-row">
                    <span>🏆 ACHIEVEMENTS</span>
                    <span class="stat-val" id="stat_ach">0</span>
                    <hr class="divider">

                    <div class="achievements-box">
                        <h4 style="color:#66fcf1; font-family:'Rajdhani'; margin:10px 0;">BADGES UNLOCKED</h4>
                        <div id="badge_list" class="badge-grid">
                            <p style="color:#555; font-size:12px;">No badges yet.</p>
                        </div>
                    </div>
                </div>
                <div class="stat-row">
                    <span>💳 TOTAL VALUE</span>
                    <span class="stat-val" id="stat_spent">$0.00</span>
                </div>
            </div>

            <div class="settings-area">

                <div class="setting-box">
                    <h3>🛠 BASIC INFORMATION</h3>
                    <form id="infoForm">
                        <label>Username</label>
                        <input type="text" id="in_username">

                        <label>Email Address</label>
                        <input type="text" id="in_email">

                        <button type="submit" class="btn-save">UPDATE IDENTITY</button>
                    </form>
                </div>

                <div class="setting-box">
                    <h3>🛡 SECURITY (PASSWORD)</h3>
                    <form id="passForm">
                        <label>Current Password</label>
                        <input type="password" id="old_pass" class="pass-input">

                        <label>New Password</label>
                        <input type="password" id="new_pass" class="pass-input">

                        <label>Confirm New Password</label>
                        <input type="password" id="confirm_pass" class="pass-input">

                        <div class="show-pass-wrapper">
                            <input type="checkbox" id="togglePass" onclick="togglePasswords()">
                            <label for="togglePass" style="display:inline; margin:0; cursor:pointer;">Show Characters</label>
                        </div>

                        <button type="submit" class="btn-save btn-warn">CHANGE PASSPHRASE</button>
                    </form>
                </div>

                <div class="setting-box danger-zone">
                    <h3 style="color:#e74c3c">⚠ DANGER ZONE</h3>
                    <p>Deleting your account removes access to all owned games.</p>
                    <button onclick="deleteAccount()" class="btn-delete">DELETE ACCOUNT PERMANENTLY</button>
                </div>

            </div>
        </div>
    </div>

    <div id="statusModal" class="modal-overlay">
        <div class="modal-box">
            <div id="modalIcon" class="modal-icon">!</div>
            <h3 id="modalTitle" class="modal-title">NOTICE</h3>
            <p id="modalMsg" class="modal-text">...</p>
            <button class="btn-modal btn-ok" onclick="closeModal()">ACKNOWLEDGE</button>
        </div>
    </div>

    <script src="../js/profile.js"></script>

</body>
</html>