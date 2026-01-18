/* Initial data load */
window.onload = function() {
    loadProfile();
};

/* Fetch profile data */
function loadProfile() {
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/profile_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            
            if(res.status === 'success') {
                /* Update profile card */
                document.getElementById('disp_username').innerText = res.user.username;
                document.getElementById('disp_email').innerText = res.user.email;
                document.getElementById('disp_joined').innerText = "JOINED: " + res.user.created_at.split(' ')[0];
                
                /* Update stats */
                document.getElementById('stat_ach').innerText = res.stats.achievements;
                document.getElementById('stat_spent').innerText = "$" + res.stats.total_spent;

                /* Update inputs */
                document.getElementById('in_username').value = res.user.username;
                document.getElementById('in_email').value = res.user.email;

                renderBadges(res.badges);
            }
        }
    };
    xhttp.send('action=fetch_data');
}

/* Render achievement badges */
function renderBadges(badges) {
    let badgeContainer = document.getElementById('badge_list');
    let badgeHTML = "";

    if (badges && badges.length > 0) {
        badges.forEach(b => {
            let icon = "🏆";
            if(b.code === 'first_buy') icon = "⚔️";
            if(b.code === 'renter') icon = "🏎️";
            if(b.code === 'downloader') icon = "💾";

            badgeHTML += `
            <div class="ach-item" title="${b.description}">
                <div class="ach-icon">${icon}</div>
                <div class="ach-name">${b.title}</div>
            </div>`;
        });
        badgeContainer.innerHTML = badgeHTML;
    } else {
        badgeContainer.innerHTML = "<p style='color:#777; font-size:12px; margin-top:5px;'>No badges unlocked.</p>";
    }
}

/* Handle info update */
document.getElementById('infoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let u = document.getElementById('in_username').value.trim();
    let em = document.getElementById('in_email').value.trim();

    if(u === "" || em === "") {
        showPopup("ERROR", "Fields required", true);
        return;
    }
    
    sendUpdate('action=update_info&username='+encodeURIComponent(u)+'&email='+encodeURIComponent(em));
});

/* Handle password update */
document.getElementById('passForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let oldP = document.getElementById('old_pass').value;
    let newP = document.getElementById('new_pass').value;
    let confP = document.getElementById('confirm_pass').value;
    
    if(oldP === "" || newP === "" || confP === "") {
        showPopup("ERROR", "Fields required", true);
        return;
    }

    if(newP.length < 6) {
        showPopup("WEAK", "Minimum 6 characters", true);
        return;
    }

    if(newP !== confP) {
        showPopup("MISMATCH", "Passwords must match", true);
        return;
    }
    
    sendUpdate('action=update_password&old_password='+encodeURIComponent(oldP)+'&new_password='+encodeURIComponent(newP)+'&confirm_password='+encodeURIComponent(confP));
});

/* Generic update request */
function sendUpdate(params) {
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/profile_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            
            if(res.status === 'success') {
                showPopup("SUCCESS", res.message, false);
                if(params.includes('update_info')) loadProfile(); 
                if(params.includes('update_password')) document.getElementById('passForm').reset();
            } else {
                showPopup("ERROR", res.message, true);
            }
        }
    };
    xhttp.send(params);
}

/* Handle account deletion */
function deleteAccount() {
    if(confirm("Confirm permanent deletion?")) {
        let xhttp = new XMLHttpRequest();
        xhttp.open('POST', '../php/profile_controller.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                window.location.href = "../../../Auth/MVC/html/login.php";
            }
        };
        xhttp.send('action=delete_account');
    }
}

/* Toggle password display */
function togglePasswords() {
    let inputs = document.querySelectorAll('.pass-input');
    let checkBox = document.getElementById('togglePass');
    
    inputs.forEach(input => {
        input.type = checkBox.checked ? "text" : "password";
    });
}

/* Modal UI logic */
function showPopup(title, msg, isError) {
    let modal = document.getElementById('statusModal');
    let titleEl = document.getElementById('modalTitle');
    let msgEl = document.getElementById('modalMsg');
    let iconEl = document.getElementById('modalIcon');
    let btn = document.querySelector('.btn-ok');

    titleEl.innerText = title;
    msgEl.innerText = msg;

    if(isError) {
        iconEl.innerText = "!";
        iconEl.style.color = "#e74c3c";
        iconEl.style.borderColor = "#e74c3c";
        titleEl.style.color = "#e74c3c";
        btn.style.background = "#e74c3c";
    } else {
        iconEl.innerText = "✓";
        iconEl.style.color = "#66fcf1";
        iconEl.style.borderColor = "#66fcf1";
        titleEl.style.color = "#66fcf1";
        btn.style.background = "#66fcf1";
    }

    modal.classList.add('show');
}

/* Close status modal */
function closeModal() {
    document.getElementById('statusModal').classList.remove('show');
}