/* Initial view trigger */
window.onload = function() {
    trackView();
};

/* Send view analytics */
function trackView() {
    const id = document.getElementById('gameId').value;
    const xhttp = new XMLHttpRequest();

    xhttp.open('POST', '../php/stats_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    
    xhttp.send('action=track_view&game_id=' + id);
}