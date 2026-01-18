/* Image preview logic */
const imgInput = document.getElementById('imageInput');
const imgPreview = document.getElementById('imgPreview');

imgInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imgPreview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

/* Form validation logic */
const form = document.getElementById('editForm');
const nameEl = document.getElementById('g_name');
const catEl = document.getElementById('g_cat');
const sellEl = document.getElementById('g_sell');
const rentEl = document.getElementById('g_rent');
const stockEl = document.getElementById('g_stock');

form.addEventListener('submit', function(e) {
    let isValid = true;

    /* Reset error states */
    document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.form-control').forEach(el => el.style.border = '1px solid #ddd');

    /* Display validation error */
    function showError(input, msgId) {
        document.getElementById(msgId).style.display = 'block';
        input.style.border = '1px solid #e74a3b';
        isValid = false;
    }

    if (nameEl.value.trim() === "") showError(nameEl, 'err_name');
    if (catEl.value.trim() === "") showError(catEl, 'err_cat');
    
    /* Validate numeric inputs */
    if (sellEl.value < 0) {
        alert("Sell price cannot be negative");
        isValid = false;
    }
    if (rentEl.value < 0) {
        alert("Rent price cannot be negative");
        isValid = false;
    }
    if (stockEl.value < 0) {
        alert("Stock cannot be negative");
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault();
    }
});