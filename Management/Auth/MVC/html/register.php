<!DOCTYPE html>
<html>
<head>
    <title>PlayVerse | Register</title>
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>

<div class="auth-container">

    <div class="logo">
        🎮 <span>PlayVerse</span>
    </div>

    <h2>Create Account</h2>

    <form id="registerForm" novalidate>

        <input type="text" id="username" placeholder="Username">
        <small class="err" id="errUsername"></small>

        <input type="text" id="email" placeholder="Email">
        <small class="err" id="errEmail"></small>

        <input type="password" id="password" placeholder="Password">
        <small class="err" id="errPassword"></small>

        <input type="password" id="confirmPassword" placeholder="Confirm Password">
        <small class="err" id="errConfirm"></small>

        <button type="submit">Register</button>

    </form>

    <p id="resultMsg"></p>

    <div class="bottom-link">
        Already have an account?
        <a href="login.php">Login Here</a>
    </div>

</div>

<script src="../js/register.js"></script>

</body>
</html>