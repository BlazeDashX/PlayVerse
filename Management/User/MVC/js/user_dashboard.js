window.onload = function() {
    loadPopularGames();
    loadAllGames();
};

// 1. Trending
function loadPopularGames() {
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/library_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let games = JSON.parse(this.responseText);
            renderGames(games, "popular-container", true);
        }
    };
    xhttp.send('action=fetch_popular');
}

// 2. Load All Games + Sort
function loadAllGames() {
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/library_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let games = JSON.parse(this.responseText);
            
            // Render the Marketplace
            renderGames(games, "all-games-container", false);

            // Filter for Owned & Rented
            filterLibrary(games);
        }
    };
    xhttp.send('action=fetch_all');
}

// 3. Sort Library
function filterLibrary(games) {
    let ownedGames = [];
    let rentedGames = [];

    for(let i=0; i<games.length; i++) {
        if(games[i].owned_status === 'buy') {
            ownedGames.push(games[i]);
        } else if(games[i].owned_status === 'rent') {
            rentedGames.push(games[i]);
        }
    }

    if(ownedGames.length > 0) {
        document.getElementById('section-owned').style.display = 'block';
        renderGames(ownedGames, 'owned-container', false);
    }

    if(rentedGames.length > 0) {
        document.getElementById('section-rented').style.display = 'block';
        renderGames(rentedGames, 'rented-container', false);
    }
}

// 4. Render Cards
function renderGames(games, containerId, isPopular) {
    let container = document.getElementById(containerId);
    let html = "";

    if (games.length === 0) {
        container.innerHTML = "<p style='color:#666; font-style:italic;'>No data found.</p>";
        return;
    }

    for (let i = 0; i < games.length; i++) {
        let g = games[i];
        
        let imgPath = g.image_filename 
            ? `../../../Admin/MVC/images/uploaded/${g.image_filename}` 
            : `../../../Admin/MVC/images/default_game.png`; 

        // --- BADGE LOGIC UPDATED ---
        let badgeHTML = "";
        let sellPrice = parseFloat(g.sell_price); // Convert string to number

        if (g.owned_status === 'buy') {
            badgeHTML = `<span class="badge badge-owned">✅ OWNED</span>`;
        } else if (g.owned_status === 'rent') {
            badgeHTML = `<span class="badge badge-rented">⏳ RENTED</span>`;
        } else if (sellPrice === 0) {
            // NEW: Check for Free Game
            badgeHTML = `<span class="badge badge-free">🎁 FREE</span>`;
        } else if (isPopular) {
            badgeHTML = `<span class="badge badge-hot">🔥 HOT</span>`;
        }

        // --- BUTTON LOGIC ---
        let actionBtn = "";
        if (g.owned_status === 'buy' || g.owned_status === 'rent') {
             actionBtn = `<button class="btn-view btn-download" 
                          onclick="downloadGame(${g.id}, '${g.image_filename}')">
                          ⬇ DOWNLOAD
                          </button>`;
        } else {
            actionBtn = `<button class="btn-view" onclick="viewGame(${g.id})">VIEW DETAILS</button>`;
        }

        // Optional: formatting price tag to look nicer for free games
        let displaySellPrice = (sellPrice === 0) ? "FREE" : "$" + g.sell_price;

        html += `
        <div class="game-card">
            ${badgeHTML}
            <div class="card-img" style="background-image: url('${imgPath}');"></div>
            <div class="card-body">
                <h3>${g.name}</h3>
                <span class="category">${g.category}</span>
                <div class="prices">
                    ${g.status !== 'rent' ? `<div class="price-tag">Buy: ${displaySellPrice}</div>` : ''}
                    ${g.status !== 'sell' ? `<div class="price-tag rent">Rent: $${g.rent_price_per_month}</div>` : ''}
                </div>
                ${actionBtn}
            </div>
        </div>
        `;
    }
    container.innerHTML = html;
}

function viewGame(id) {
    window.location.href = "game_details.php?id=" + id;
}

// 5. Download Logic
function downloadGame(id, filename) {
    if(!confirm("Start downloading " + filename + "?")) return;

    // A. Record stats
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/library_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('action=record_download&game_id=' + id);

    // B. Trigger Download
    let link = document.createElement('a');
    link.href = `../../../Admin/MVC/images/uploaded/${filename}`;
    link.download = filename; 
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    alert("Download Started! (Check your downloads folder)");
}