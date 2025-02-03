document.querySelectorAll('.tablecontainerrequests tbody tr').forEach(function(row) {
    row.addEventListener('click', function() {
        document.getElementById('requestModal').style.display = 'flex';
        document.getElementById('requestModal').style.alignItems = 'center';
        document.getElementById('requestModal').style.justifyContent = 'center';
    });
});

document.querySelector('.modal .close').addEventListener('click', function() {
    document.getElementById('requestModal').style.display = 'none';
});

window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('requestModal')) {
        document.getElementById('requestModal').style.display = 'none';
    }
});