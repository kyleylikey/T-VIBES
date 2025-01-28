
document.getElementById('minus-btn').addEventListener('click', function() {
    var paxInput = document.getElementById('pax');
    var currentValue = parseInt(paxInput.value);
    if (currentValue > 1) {
        paxInput.value = currentValue - 1;
    }
});

document.getElementById('plus-btn').addEventListener('click', function() {
    var paxInput = document.getElementById('pax');
    var currentValue = parseInt(paxInput.value);
    if (currentValue < 100) {
        paxInput.value = currentValue + 1;
    }
});
