window.onload = function() {
    trackView();
};

function trackView() {
    const id = document.getElementById('gameId').value;
    
    const xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/stats_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    // We don't need to do anything with the response, just send it.
    xhttp.send('action=track_view&game_id=' + id);
}