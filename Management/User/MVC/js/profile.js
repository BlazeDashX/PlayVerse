window.onload = function() {
    loadProfile();
};

// --- MAIN: Load Profile Data (Info + Stats + Badges) ---
function loadProfile() {
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/profile_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            
            if(res.status === 'success') {
                // 1. Fill Left Card Info
                document.getElementById('disp_username').innerText = res.user.username;
                document.getElementById('disp_email').innerText = res.user.email;
                document.getElementById('disp_joined').innerText = "JOINED: " + res.user.created_at.split(' ')[0];
                
                // 2. Fill Stats
                document.getElementById('stat_ach').innerText = res.stats.achievements;
                document.getElementById('stat_spent').innerText = "$" + res.stats.total_spent;

                // 3. Fill Input Fields
                document.getElementById('in_username').value = res.user.username;
                document.getElementById('in_email').value = res.user.email;

                // 4. Render Badges (New Feature)
                renderBadges(res.badges);
            }
        }
    };
    xhttp.send('action=fetch_data');
}

// --- HELPER: Render Badges ---
function renderBadges(badges) {
    let badgeContainer = document.getElementById('badge_list');
    let badgeHTML = "";

    if (badges && badges.length > 0) {
        badges.forEach(b => {
            // Assign Emojis based on code
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
        badgeContainer.innerHTML = "<p style='color:#777; font-size:12px; margin-top:5px;'>No badges unlocked yet.</p>";
    }
}

// --- FORM 1: Update Basic Info ---
document.getElementById('infoForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let u = document.getElementById('in_username').value.trim();
    let em = document.getElementById('in_email').value.trim();

    // Validation
    if(u === "" || em === "") {
        showPopup("ERROR", "Username and Email cannot be empty", true);
        return;
    }
    
    sendUpdate('action=update_info&username='+encodeURIComponent(u)+'&email='+encodeURIComponent(em));
});

// --- FORM 2: Update Password ---
document.getElementById('passForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let oldP = document.getElementById('old_pass').value;
    let newP = document.getElementById('new_pass').value;
    let confP = document.getElementById('confirm_pass').value;
    
    // Validation
    if(oldP === "" || newP === "" || confP === "") {
        showPopup("ERROR", "All password fields are required", true);
        return;
    }

    if(newP.length < 6) {
        showPopup("WEAK PASSWORD", "New password must be at least 6 characters", true);
        return;
    }

    if(newP !== confP) {
        showPopup("MISMATCH", "New Password and Confirm Password do not match", true);
        return;
    }
    
    sendUpdate('action=update_password&old_password='+encodeURIComponent(oldP)+'&new_password='+encodeURIComponent(newP)+'&confirm_password='+encodeURIComponent(confP));
});

// --- AJAX HELPER: Send Data ---
function sendUpdate(params) {
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/profile_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let res = JSON.parse(this.responseText);
            
            // Show result in Custom Popup
            if(res.status === 'success') {
                showPopup("SUCCESS", res.message, false);
                
                // If Profile Info updated -> Reload card
                if(params.includes('update_info')) { 
                    loadProfile(); 
                }
                // If Password updated -> Clear form
                if(params.includes('update_password')) {
                    document.getElementById('passForm').reset();
                }

            } else {
                showPopup("ERROR", res.message, true);
            }
        }
    };
    xhttp.send(params);
}

// --- ACTION: Delete Account ---
function deleteAccount() {
    if(confirm("⚠ DANGER: Are you sure you want to delete your account? This cannot be undone.")) {
        let xhttp = new XMLHttpRequest();
        xhttp.open('POST', '../php/profile_controller.php', true);
        xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhttp.send('action=delete_account');
        
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                alert("Account Deleted. Logging out...");
                window.location.href = "../../../Auth/MVC/html/login.php";
            }
        };
    }
}

// --- UI: Toggle Password Visibility ---
function togglePasswords() {
    let inputs = document.querySelectorAll('.pass-input');
    let checkBox = document.getElementById('togglePass');
    
    inputs.forEach(input => {
        input.type = checkBox.checked ? "text" : "password";
    });
}

// --- UI: Custom Popup Logic ---
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

function closeModal() {
    document.getElementById('statusModal').classList.remove('show');
}