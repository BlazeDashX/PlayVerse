const usernameEl = document.getElementById("username");
const emailEl = document.getElementById("email");
const passwordEl = document.getElementById("password");
const confirmEl = document.getElementById("confirmPassword");

const resultMsg = document.getElementById("resultMsg");

const errUsername = document.getElementById("errUsername");
const errEmail = document.getElementById("errEmail");
const errPassword = document.getElementById("errPassword");
const errConfirm = document.getElementById("errConfirm");

// -----------------------------
// Helpers
// -----------------------------
function clearErrors() {
    errUsername.innerText = ""; errEmail.innerText = "";
    errPassword.innerText = ""; errConfirm.innerText = "";

    errUsername.style.display = "none";
    errEmail.style.display = "none";
    errPassword.style.display = "none";
    errConfirm.style.display = "none";

    usernameEl.classList.remove("input-error");
    emailEl.classList.remove("input-error");
    passwordEl.classList.remove("input-error");
    confirmEl.classList.remove("input-error");

    resultMsg.innerText = "";
}

function showError(el, errEl, msg) {
    errEl.innerText = msg;
    errEl.style.display = "block";
    el.classList.add("input-error");
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidUsername(u) {
    return /^[a-zA-Z0-9_]{3,20}$/.test(u);
}

// -----------------------------
// Submit Registration (NO live check)
// -----------------------------
document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();

    clearErrors();

    const username = usernameEl.value.trim();
    const email = emailEl.value.trim();
    const password = passwordEl.value;
    const confirmPassword = confirmEl.value;

    let hasError = false;

    // Username
    if (username === "") {
        hasError = true;
        showError(usernameEl, errUsername, "Username is required.");
    } else if (!isValidUsername(username)) {
        hasError = true;
        showError(usernameEl, errUsername, "Username must be 3-20 chars (letters, numbers, underscore).");
    }

    // Email
    if (email === "") {
        hasError = true;
        showError(emailEl, errEmail, "Email is required.");
    } else if (!isValidEmail(email)) {
        hasError = true;
        showError(emailEl, errEmail, "Invalid email format. Example: user@gmail.com");
    }

    // Password
    if (password === "") {
        hasError = true;
        showError(passwordEl, errPassword, "Password is required.");
    } else if (password.length < 6) {
        hasError = true;
        showError(passwordEl, errPassword, "Password must be at least 6 characters.");
    }

    // Confirm
    if (confirmPassword === "") {
        hasError = true;
        showError(confirmEl, errConfirm, "Confirm password is required.");
    } else if (password !== "" && password !== confirmPassword) {
        hasError = true;
        showError(confirmEl, errConfirm, "Passwords do not match.");
    }

    if (hasError) {
        return; // show all errors at once
    }

    // Send JSON to PHP
    let data = {
        username: username,
        email: email,
        password: password,
        confirmPassword: confirmPassword
    };

    let jsonData = JSON.stringify(data);

    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../php/register_controller.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {

            let response = JSON.parse(this.responseText);

            // PHP field errors
            if (response.status === "field_error" && response.errors) {
                if (response.errors.username) showError(usernameEl, errUsername, response.errors.username);
                if (response.errors.email) showError(emailEl, errEmail, response.errors.email);
                if (response.errors.password) showError(passwordEl, errPassword, response.errors.password);
                if (response.errors.confirmPassword) showError(confirmEl, errConfirm, response.errors.confirmPassword);
                return;
            }

            // If success: popup + redirect to login
            if (response.status === "success") {
                alert("Registration successful! Redirecting to Login...");
                window.location.href = "login.php"; // same folder: Auth/MVC/html/
                return;
            }

            // Otherwise show message (optional)
            resultMsg.innerText = response.message || "";

        }
    };

    xhttp.send("data=" + encodeURIComponent(jsonData));
});
