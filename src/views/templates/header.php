<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: Arial, sans-serif !important;
        line-height: 1.6;
        color: #333;
        background-color: #f5f5f5; /* Light gray background */
    }
   
    .logoforsmall {
        display: none;
    }

    .logo {
        margin: 8px;
    }
    .object-fit-cover {
        object-fit: cover;
    }
    .navbar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    header div{
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding: 10px; 
    }

    header a {
        text-decoration: none; 
        font-size: 20px; 
    }

    header .btn {
        font-size: 40px;
        border: none; 
        padding: 10px; 
        border-radius: 5px; 
        cursor: pointer;
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
				<img src="/T-VIBES/public/assets/images/headerlogo.jpg" alt="" class="img-fluid" width="250" height="82"> 
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarText">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" aria-current="page" href="/T-VIBES/public/index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'explore.php') ? 'active' : ''; ?>" href="/T-VIBES/src/views/frontend/explore.php">Explore</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'aboutus.php') ? 'active' : ''; ?>" href="/T-VIBES/src/views/frontend/aboutus.php">About Us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page == 'contactus.php') ? 'active' : ''; ?>" href="/T-VIBES/src/views/frontend/contactus.php">Contact</a>
                        </li>
					</ul>
					<ul class="navbar-nav d-flex -flex-row gap-3 align-items-center">
					<li class="nav-item">
							<a href="T-VIBES/src/views/frontend/signup.php" class="nav-link">Sign Up</a>
						</li>
						<li class="nav-item">
						<a href="T-VIBES/src/views/frontend/login.php" 
						class="btn btn-danger rounded-pill px-3 py-1" 
						style="font-size: 1.3rem;">
							Login
						</a>
					</li>
					</ul>`

				</div>
				</div>
			</nav>
		</header>