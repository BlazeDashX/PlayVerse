/* Initial load */
window.onload = function() {
    loadPopularGames();
    loadAllGames();
};

/* Fetch trending games */
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

/* Fetch all games */
function loadAllGames() {
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/library_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let games = JSON.parse(this.responseText);
            renderGames(games, "all-games-container", false);
            filterLibrary(games);
        }
    };
    xhttp.send('action=fetch_all');
}

/* Sort user library */
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

/* Build game cards */
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

        /* Define status badges */
        let badgeHTML = "";
        let sellPrice = parseFloat(g.sell_price);

        if (g.owned_status === 'buy') {
            badgeHTML = `<span class="badge badge-owned">✅ OWNED</span>`;
        } else if (g.owned_status === 'rent') {
            badgeHTML = `<span class="badge badge-rented">⏳ RENTED</span>`;
        } else if (sellPrice === 0) {
            badgeHTML = `<span class="badge badge-free">🎁 FREE</span>`;
        } else if (isPopular) {
            badgeHTML = `<span class="badge badge-hot">🔥 HOT</span>`;
        }

        /* Define buttons */
        let actionBtn = "";
        if (g.owned_status === 'buy' || g.owned_status === 'rent') {
             actionBtn = `<button class="btn-view btn-download" 
                          onclick="downloadGame(${g.id}, '${g.image_filename}')">
                          ⬇ DOWNLOAD
                          </button>`;
        } else {
            actionBtn = `<button class="btn-view" onclick="viewGame(${g.id})">VIEW DETAILS</button>`;
        }

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

/* Redirect to details */
function viewGame(id) {
    window.location.href = "game_details.php?id=" + id;
}

/* Execute download process */
function downloadGame(id, filename) {
    if(!confirm("Start download?")) return;

    /* Update stats */
    let xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/library_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.send('action=record_download&game_id=' + id);

    /* Trigger file browser */
    let link = document.createElement('a');
    link.href = `../../../Admin/MVC/images/uploaded/${filename}`;
    link.download = filename; 
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    alert("Download Started!");
}