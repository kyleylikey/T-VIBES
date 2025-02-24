<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    .active, .nav:hover {
        font-weight: bold;
    }

    .logoforsmall {
        display: none;
    }

    .logo {
        margin: 8px;
    }
    
    @media screen and (max-width: 768px) {
        
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
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
</head>
</head>
<header>
          <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid">
			<img src="/T-VIBES//public/assets/images/headerlogo.jpg" alt="" class="img-fluid" width="250" height="82"> 
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                    <a class="nav-link mb-0" href="/T-VIBES/public/" style="margin-right: 16px;">Explore</a>
                  </li>
                  <li class="nav-item <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">
                    <a class="nav-link mb-0" href="#about" style="margin-right: 16px;">About Us</a>
                  </li>
                  <li class="nav-item <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">
                    <a class="nav-link mb-0" href="#contact">Contact</a>
                  </li>
                </ul>
				<ul class="navbar-nav d-flex -flex-row gap-3 align-items-center">
				<div>
                    <a href="<?php 
                    if (!isset($_SESSION['userid']) || (isset($_SESSION['usertype']) && $_SESSION['usertype'] != 'trst')) {
                        echo "/T-VIBES/src/views/frontend/login.php";
                    }
                    else {
                        echo "/T-VIBES/src/views/frontend/tours/tourrequest.php";
                    }
                    ?>" class="btn" style="margin-right: 12px;">
                        <i class="<?php echo $current_page == 'tourrequest.php' ? 'bi bi-map-fill' : 'bi bi-map'; ?>" onmouseover="this.className='bi bi-map-fill'" onmouseout="this.className='<?php echo $current_page == 'tourrequest.php' ? 'bi bi-map-fill' : 'bi bi-map'; ?>'"></i>
                    </a>
                    <a href="<?php 
                    if (!isset($_SESSION['userid']) || (isset($_SESSION['usertype']) && $_SESSION['usertype'] != 'trst')) {
                        echo "/T-VIBES/src/views/frontend/login.php";
                    }
                    else {
                        echo "/T-VIBES/src/views/frontend/account.php";
                    }
                    ?>" class="btn">
                        <i class="<?php echo $current_page == 'account.php' ? 'bi bi-person-fill' : 'bi bi-person'; ?>" onmouseover="this.className='bi bi-person-fill'" onmouseout="this.className='<?php echo $current_page == 'account.php' ? 'bi bi-person-fill' : 'bi bi-person'; ?>'"></i>
                    </a>
				</ul>`
              </div>
            </div>
          </nav>
    </header>