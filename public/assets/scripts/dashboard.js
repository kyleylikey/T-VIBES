document.querySelectorAll('.tablecontainerrequests tbody tr').forEach(function(row) {
    row.addEventListener('click', function() {
        document.getElementById('requestModal').style.display = 'flex';
        document.getElementById('requestModal').style.alignItems = 'center';
        document.getElementById('requestModal').style.justifyContent = 'center';
    });
});

document.querySelectorAll('.revitem').forEach(function(row) {
    row.addEventListener('click', function() {
        document.getElementById('requestModal').style.display = 'flex';
        document.getElementById('requestModal').style.alignItems = 'center';
        document.getElementById('requestModal').style.justifyContent = 'center';
    });
});

document.getElementById('closeforsmall').addEventListener('click', function() {
    document.getElementById('requestModal').style.display = 'none';
});

document.getElementById('closeforbig').addEventListener('click', function() {
    document.getElementById('requestModal').style.display = 'none';
});

window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('requestModal')) {
        document.getElementById('requestModal').style.display = 'none';
    }
});

function setActiveTab(button) {
    var buttons = document.querySelectorAll('.tabbutton');
    buttons.forEach(function(btn) {
        btn.classList.remove('active');
    });
    button.classList.add('active');
}