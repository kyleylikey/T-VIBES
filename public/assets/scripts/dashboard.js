document.querySelectorAll('.tablecontainerrequests tbody tr').forEach(function(row) {
    row.addEventListener('click', function() {
        document.getElementById('requestModal').style.display = 'flex';
        document.getElementById('requestModal').style.alignItems = 'center';
        document.getElementById('requestModal').style.justifyContent = 'center';
    });
});

document.querySelectorAll('.griditem').forEach(function(row) {
    row.addEventListener('click', function() {
        document.getElementById('requestModal').style.display = 'flex';
        document.getElementById('requestModal').style.alignItems = 'center';
        document.getElementById('requestModal').style.justifyContent = 'center';
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const accountItems = document.querySelectorAll('.accountitem');
    const modal = document.getElementById('requestModal');
    const modalName = document.getElementById('modalName');
    const modalUsername = document.getElementById('modalUsername');
    const modalEmail = document.getElementById('modalEmail');
    const modalContact = document.getElementById('modalContact');
    const closeButtons = document.querySelectorAll('.close');

    accountItems.forEach(item => {
        item.addEventListener('click', function () {
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const contact = this.getAttribute('data-contact');
            const username = this.getAttribute('data-username');

            modalName.textContent = name;
            modalUsername.textContent = username;
            modalEmail.textContent = email;
            modalContact.textContent = contact;

            modal.style.display = 'block';
        });
    });

    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            modal.style.display = 'none';
        });
    });

    window.addEventListener('click', function (event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
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