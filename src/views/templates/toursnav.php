<style>
.toursnav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    margin: 0 36px;
}

.toursnav > nav {
    display: flex;
    gap: 12px;
}

.toursnav > nav > a {
    margin: 0;
    border-radius: 40px;
    padding: 10px 20px;
    text-decoration: none;
    border: 2px solid #102E47;
    color: #102E47;
    font-weight: 900;
    transition: background-color 0.3s, color 0.3s;
}

.toursnav > nav > .active,
.toursnav > nav > a:hover {
    background-color: #102E47;
    color: white;
}

.toursnav h2 {
    color: #102E47;
    font-weight: bold !important;
    margin: 0;
    font-family: 'Raleway', sans-serif !important;
}

.toursnav .menu-toggle {
    display: none;
    cursor: pointer;
    font-size: 24px;
}

@media (max-width: 768px) {
    .toursnav > nav {
        display: none;
        flex-direction: column;
        position: absolute;
        margin-top: 48px;
        background-color: white;
        width: 100%;
        padding: 12px;
        border-top: 2px solid #102E47;
        z-index: 10;
    }

    .toursnav > nav.open {
        display: flex;
    }

    .toursnav .menu-toggle {
        display: block;
    }
}
</style>

<div class="toursnav">
    <span><h2>Tours</h2></span>

    <div class="menu-toggle" onclick="toggleMenu()">☰</div>
    
    <nav id="navLinks">
        <a href="tourrequest.php" class="<?php echo $current_page == 'tourrequest.php' ? 'active' : ''; ?>">Plan Request</a>
        <a href="tourpending.php" class="<?php echo $current_page == 'tourpending.php' ? 'active' : ''; ?>">Pending</a>
        <a href="tourapproved.php" class="<?php echo $current_page == 'tourapproved.php' ? 'active' : ''; ?>">Approved</a>
        <a href="tourhistory.php" class="<?php echo $current_page == 'tourhistory.php' ? 'active' : ''; ?>">History</a>
    </nav>
</div>

<script>
function toggleMenu() {
    const nav = document.getElementById('navLinks');
    nav.classList.toggle('open');
}

document.addEventListener('click', function(event) {
    const nav = document.getElementById('navLinks');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (!nav.contains(event.target) && !menuToggle.contains(event.target) && nav.classList.contains('open')) {
        nav.classList.remove('open');
    }
});
</script>
