<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    .active, .nav:hover {
        font-weight: bold;
    }
    
    .hamburger {
        display: none;
        cursor: pointer;
        padding: 10px;
    }

    .logoforsmall {
        display: none;
    }

    .logo {
        margin: 8px;
    }
    
    @media screen and (max-width: 768px) {
        .hamburger {
            display: block;
            font-size: 40px;
            border: none; 
            padding: 10px; 
            border-radius: 5px; 
            cursor: pointer;
        }
        
        nav {
            display: none;
            width: 100%;
            position: absolute;
            top: 72px;
            left: 0;
            background: white;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.5);
            z-index: 1000;
        }
        
        nav.show {
            display: block;
        }
        
        nav a {
            display: block;
            margin: 10px 0 !important;
        }
        
        header > div {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .logoforsmall {
            display: block; 
            margin-left: 10px;
        }

        .logo {
            display: none;
        }
    }
</style>

<header>
    <div>
        <button class="hamburger">
            <i class="bi bi-list"></i>
        </button>
        <span class="logoforsmall">*Logo*</span>
        <nav id="mainNav">
            <span class="logo">*Logo*</span>
            <a href="/T-VIBES/public/" style="margin-right: 16px;" class="nav <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">Explore</a>
            <a href="#about" style="margin-right: 16px;" class="nav <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">About Us</a>
            <a href="#contact" class="nav <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Contact</a>
        </nav>
        <div>
            <a href="<?php 
            if (!isset($_SESSION['userid'])) {
                echo "/T-VIBES/src/views/frontend/login.php";
            }
            else {
                echo "/T-VIBES/src/views/frontend/tours/tourrequest.php";
            }
            ?>" class="btn" style="margin-right: 12px;">
                <i class="<?php echo $current_page == 'tourrequest.php' ? 'bi bi-map-fill' : 'bi bi-map'; ?>" onmouseover="this.className='bi bi-map-fill'" onmouseout="this.className='<?php echo $current_page == 'tourrequest.php' ? 'bi bi-map-fill' : 'bi bi-map'; ?>'"></i>
            </a>
            <a href="<?php 
            if (!isset($_SESSION['userid'])) {
                echo "/T-VIBES/src/views/frontend/login.php";
            }
            else {
                echo "/T-VIBES/src/views/frontend/account.php";
            }
            ?>" class="btn">
                <i class="<?php echo $current_page == 'account.php' ? 'bi bi-person-fill' : 'bi bi-person'; ?>" onmouseover="this.className='bi bi-person-fill'" onmouseout="this.className='<?php echo $current_page == 'account.php' ? 'bi bi-person-fill' : 'bi bi-person'; ?>'"></i>
            </a>
        </div>
    </div>
</header>

<script>
document.querySelector('.hamburger').addEventListener('click', function() {
    document.querySelector('nav').classList.toggle('show');
});

/* Add click outside handler */
document.addEventListener('click', function(event) {
    const nav = document.getElementById('mainNav');
    const menuToggle = document.querySelector('.hamburger');
    
    if (!nav.contains(event.target) && !menuToggle.contains(event.target) && nav.classList.contains('show')) {
        nav.classList.remove('show');
    }
});
</script>