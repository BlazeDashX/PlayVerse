<!DOCTYPE html>
<html>
<head>
    <title>PlayVerse | Login</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>

<div class="auth-container">

    <div class="logo">
        🎮 <span>PlayVerse</span>
    </div>

    <h2>Sign In</h2>

    <form id="loginForm" novalidate>

        <input type="text" id="usernameOrEmail" placeholder="Username or Email">
        <small class="err" id="errUser"></small>

        <input type="password" id="password" placeholder="Password">
        <small class="err" id="errPass"></small>

        <button type="submit">Login</button>

    </form>

    <div class="bottom-link">
        Don’t have an account?
        <a href="register.php">Register Here</a>
    </div>

</div>

<script src="../js/login.js"></script>

</body>
</html>