const usernameEl = document.getElementById("username");
const emailEl = document.getElementById("email");
const passwordEl = document.getElementById("password");
const confirmEl = document.getElementById("confirmPassword");
const resultMsg = document.getElementById("resultMsg");

const errUsername = document.getElementById("errUsername");
const errEmail = document.getElementById("errEmail");
const errPassword = document.getElementById("errPassword");
const errConfirm = document.getElementById("errConfirm");

/* Reset error states */
function clearErrors() {
    errUsername.innerText = "";
    errEmail.innerText = "";
    errPassword.innerText = "";
    errConfirm.innerText = "";

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

/* Show validation error */
function showError(el, errEl, msg) {
    errEl.innerText = msg;
    errEl.style.display = "block";
    el.classList.add("input-error");
}

/* Email regex check */
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/* Username regex check */
function isValidUsername(u) {
    return /^[a-zA-Z0-9_]{3,20}$/.test(u);
}

/* Handle registration submission */
document.getElementById("registerForm").addEventListener("submit", function (e) {
    e.preventDefault();
    clearErrors();

    const username = usernameEl.value.trim();
    const email = emailEl.value.trim();
    const password = passwordEl.value;
    const confirmPassword = confirmEl.value;

    let hasError = false;

    if (username === "") {
        hasError = true;
        showError(usernameEl, errUsername, "Username is required.");
    } else if (!isValidUsername(username)) {
        hasError = true;
        showError(usernameEl, errUsername, "Username: 3-20 chars.");
    }

    if (email === "") {
        hasError = true;
        showError(emailEl, errEmail, "Email is required.");
    } else if (!isValidEmail(email)) {
        hasError = true;
        showError(emailEl, errEmail, "Invalid email format.");
    }

    if (password === "") {
        hasError = true;
        showError(passwordEl, errPassword, "Password is required.");
    } else if (password.length < 6) {
        hasError = true;
        showError(passwordEl, errPassword, "Min 6 characters.");
    }

    if (confirmPassword === "") {
        hasError = true;
        showError(confirmEl, errConfirm, "Confirm password required.");
    } else if (password !== "" && password !== confirmPassword) {
        hasError = true;
        showError(confirmEl, errConfirm, "Passwords mismatch.");
    }

    if (hasError) return;

    let data = {
        username: username,
        email: email,
        password: password,
        confirmPassword: confirmPassword
    };

    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../php/register_controller.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let response = JSON.parse(this.responseText);

            if (response.status === "field_error" && response.errors) {
                if (response.errors.username) showError(usernameEl, errUsername, response.errors.username);
                if (response.errors.email) showError(emailEl, errEmail, response.errors.email);
                if (response.errors.password) showError(passwordEl, errPassword, response.errors.password);
                if (response.errors.confirmPassword) showError(confirmEl, errConfirm, response.errors.confirmPassword);
                return;
            }

            if (response.status === "success") {
                alert("Success! Redirecting...");
                window.location.href = "login.php";
                return;
            }

            resultMsg.innerText = response.message || "";
        }
    };

    xhttp.send("data=" + encodeURIComponent(JSON.stringify(data)));
});