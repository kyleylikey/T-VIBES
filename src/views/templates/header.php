<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    .active, .nav:hover {
        font-weight: bold;
    }
</style>
<header>
    <div>
        <nav>
            <span style="margin-right: 16px;"> *Logo* </span>
            <a href="/T-VIBES/public/" style="margin-right: 16px;" class="nav <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Explore</a>
            <a href="#about" style="margin-right: 16px;" class="nav <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About Us</a>
            <a href="#contact" class="nav <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a>
        </nav>
        <div>
            <a href="/T-VIBES/src/views/frontend/tours/tourrequest.php" class="btn" style="margin-right: 12px;">
                <i class="<?php echo $current_page == 'tourrequest.php' ? 'bi bi-map-fill' : 'bi bi-map'; ?>" onmouseover="this.className='bi bi-map-fill'" onmouseout="this.className='<?php echo $current_page == 'tourrequest.php' ? 'bi bi-map-fill' : 'bi bi-map'; ?>'"></i>
            </a>
            <a href="#account" class="btn">
                <i class="<?php echo $current_page == 'account.php' ? 'bi bi-person-fill' : 'bi bi-person'; ?>" onmouseover="this.className='bi bi-person-fill'" onmouseout="this.className='<?php echo $current_page == 'account.php' ? 'bi bi-person-fill' : 'bi bi-person'; ?>'"></i>
            </a>
        </div>
    </div>
</header>