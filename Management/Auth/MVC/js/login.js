const userEl = document.getElementById("usernameOrEmail");
const passEl = document.getElementById("password");

const errUser = document.getElementById("errUser");
const errPass = document.getElementById("errPass");

function clearErrors(){
    errUser.innerText = "";
    errPass.innerText = "";

    errUser.style.display = "none";
    errPass.style.display = "none";

    userEl.classList.remove("input-error");
    passEl.classList.remove("input-error");
}

function showError(el, errEl, msg){
    errEl.innerText = msg;
    errEl.style.display = "block";
    el.classList.add("input-error");
}

document.getElementById("loginForm").addEventListener("submit", function(e){
    e.preventDefault();

    clearErrors();

    const usernameOrEmail = userEl.value.trim();
    const password = passEl.value;

    let hasError = false;

    if(usernameOrEmail === ""){
        hasError = true;
        showError(userEl, errUser, "Username/Email is required.");
    }

    if(password === ""){
        hasError = true;
        showError(passEl, errPass, "Password is required.");
    }

    if(hasError) return;

    // JSON payload
    let data = {
        usernameOrEmail: usernameOrEmail,
        password: password
    };

    let jsonData = JSON.stringify(data);

    // AJAX request (your required pattern)
    let xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../php/login_controller.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){

            let res = JSON.parse(this.responseText);

            if(res.status === "field_error" && res.errors){
                if(res.errors.usernameOrEmail){
                    showError(userEl, errUser, res.errors.usernameOrEmail);
                }
                if(res.errors.password){
                    showError(passEl, errPass, res.errors.password);
                }
                return;
            }

            if(res.status === "success"){
                alert("✅ Login successful!");

                // Role-based redirect (admin/user)
                if(res.role === "admin"){
                    window.location.href = "../../../Admin/MVC/php/admin_dashboard.php";
                }else{
                    window.location.href = "../../../User/MVC/php/user_dashboard.php";
                }
                return;
            }

            alert(res.message || "Login failed!");
        }
    };

    xhttp.send("data=" + encodeURIComponent(jsonData));
});
