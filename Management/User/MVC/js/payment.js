const form = document.getElementById('paymentForm');
const modal = document.getElementById('confirmModal');
const btnYes = document.getElementById('btnYes');
const btnNo = document.getElementById('btnNo');
const mainBtn = document.getElementById('payBtn');
const msg = document.getElementById('msg');

/* Show confirmation modal */
form.addEventListener('submit', function(e) {
    e.preventDefault();
    modal.classList.add('show');
});

/* Close confirmation modal */
btnNo.addEventListener('click', function() {
    modal.classList.remove('show');
});

/* Handle payment execution */
btnYes.addEventListener('click', function() {
    modal.classList.remove('show');
    mainBtn.disabled = true;
    mainBtn.innerText = "PROCESSING...";

    const data = {
        gameId: document.getElementById('h_gameId').value,
        type: document.getElementById('h_type').value,
        amount: document.getElementById('h_amount').value,
        holder: document.getElementById('cardName').value,
        cardNumber: document.getElementById('cardNum').value,
        expiry: document.getElementById('expiry').value,
        cvv: document.getElementById('cvv').value
    };

    const xhttp = new XMLHttpRequest();
    xhttp.open('POST', '../php/payment_controller.php', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const res = JSON.parse(this.responseText);
            if (res.status === 'success') {
                msg.style.color = '#66fcf1';
                msg.innerText = "✓ PAYMENT SUCCESSFUL";
                setTimeout(() => window.location.href = "user_dashboard.php", 1500);
            } else {
                msg.style.color = '#e74c3c';
                msg.innerText = "⚠ " + res.message;
                mainBtn.disabled = false;
                mainBtn.innerText = "RETRY PAYMENT";
            }
        }
    };
    xhttp.send('action=pay&data=' + JSON.stringify(data));
});

/* Format card input */
document.getElementById('cardNum').addEventListener('input', function (e) {
    e.target.value = e.target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
});