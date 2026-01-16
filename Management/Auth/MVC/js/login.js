const userEl = document.getElementById("usernameOrEmail");
const passEl = document.getElementById("password");
const errUser = document.getElementById("errUser");
const errPass = document.getElementById("errPass");

function showError(el, errEl, msg){
    errEl.innerText = msg;
    errEl.style.display = "block";
    el.classList.add("input-error");
}

function clearErrors(){
    errUser.style.display = "none";
    errPass.style.display = "none";
    userEl.classList.remove("input-error");
    passEl.classList.remove("input-error");
}

document.getElementById("loginForm").addEventListener("submit", function(e){
    e.preventDefault();
    clearErrors();

    const data = {
        usernameOrEmail: userEl.value.trim(),
        password: passEl.value
    };

    let xhttp = new XMLHttpRequest();
    // Path leads from Auth/MVC/html/ to Auth/MVC/php/
    xhttp.open("POST", "../php/login_controller.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            let res = JSON.parse(this.responseText);

            if(res.status === "field_error"){
                if(res.errors.usernameOrEmail) showError(userEl, errUser, res.errors.usernameOrEmail);
                if(res.errors.password) showError(passEl, errPass, res.errors.password);
            } 
            else if(res.status === "success"){
                // Correct redirects based on Project Root: Management/
                if(res.role === "admin"){
                    window.location.href = "../../../Admin/MVC/html/admin_dashboard.php";
                } else {
                    window.location.href = "../../../User/MVC/html/user_dashboard.php";
                }
            } else {
                alert(res.message);
            }
        }
    };
    xhttp.send("data=" + encodeURIComponent(JSON.stringify(data)));
});