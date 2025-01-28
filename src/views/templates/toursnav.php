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
    gap: 8px;
}

.toursnav > nav > a {
    margin: 0 8px;
    border-radius: 40px;
    padding: 10px;
    text-decoration: none;
    border: 2px solid black;
}

.toursnav > nav > .active, .toursnav > nav > a:hover {
    background-color: black;
    color: white;
}

/* Collapsible menu */
.toursnav .menu-toggle {
    display: none;
    cursor: pointer;
    font-size: 24px;
}

.toursnav .menu-toggle.open {
    display: block;
}

.toursnav nav {
    display: flex;
    gap: 8px;
}

/* For small screens */
@media (max-width: 768px) {
    .toursnav > nav {
        display: none;
        flex-direction: column;
        position: absolute;
        margin-top: 88px;
        background-color: white;
        width: 100%;
        padding: 12px;
        border-top: 2px solid black;
        z-index: 1;
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
    <span style="margin-left:8px"><h1>Tours: <?php echo $current_page == 'tourrequest.php' ? 'Plan Request' : ''; ?></h1></span>
    
    <!-- Menu Toggle Icon -->
    <div class="menu-toggle" onclick="toggleMenu()"><i class="bi bi-caret-down-square" onmouseover="this.className='bi bi-caret-down-square-fill'" onmouseout="this.className='bi bi-caret-down-square'"></i></div>
    
    <nav id="navLinks">
        <a href="tourrequest.php" class="nav <?php echo $current_page == 'tourrequest.php' ? 'active' : ''; ?>">Plan Request</a>
        <a href="tourpending.php" class="nav <?php echo $current_page == 'tourpending.php' ? 'active' : ''; ?>">Pending</a>
        <a href="tourapproved.php" class="nav <?php echo $current_page == 'tourapproved.php' ? 'active' : ''; ?>">Approved</a>
        <a href="tourhistory.php" class="nav <?php echo $current_page == 'tourhistory.php' ? 'active' : ''; ?>">History</a>
    </nav>
</div>

<script>
function toggleMenu() {
    const nav = document.getElementById('navLinks');
    nav.classList.toggle('open');
}

/* Add click outside handler */
document.addEventListener('click', function(event) {
    const nav = document.getElementById('navLinks');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (!nav.contains(event.target) && !menuToggle.contains(event.target) && nav.classList.contains('open')) {
        nav.classList.remove('open');
    }
});
</script>
