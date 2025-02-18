<?php
session_start();
require_once '../../controllers/helpers.php';

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'emp') {
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Access Denied</title>
        <link rel='stylesheet' href='../../../public/assets/styles/main.css'>
        <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <script>
            setTimeout(function() {
                Swal.fire({
                    iconHtml: '<i class=\"fas fa-exclamation-circle\"></i>',
                    customClass: {
                        icon: 'swal2-icon swal2-error-icon',
                    },
                    html: '<p style=\"font-size: 24px; font-weight: bold;\">Access Denied! You do not have permission to access this page.</p>',
                    showConfirmButton: false,
                    timer: 3000
                }).then(() => {
                    window.location.href = '../frontend/login.php';
                });
            }, 100);
        </script>
        <style>
            .swal2-popup {
                border-radius: 12px;
                padding: 20px;
            }
            .swal2-icon.swal2-error-icon {
                border: none;
                font-size: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                width: 60px;
                height: 60px;
                color: #333;
            }
        </style>
    </head>
    <body></body>
    </html>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - Overview</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/dashboard.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="vertnavbar">
        <div class="logocontainer">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
        </div>
        <ul>
            <li><a href="home.php"><i class="bi bi-grid-fill"></i><span class="nav-text">Overview</span></a></li>
            <li><a href="tourrequests.php"><i class="bi bi-map"></i><span class="nav-text">Tour Requests</span></a></li>
            <li><a href="upcomingtourstoday.php"><i class="bi bi-geo"></i><span class="nav-text">Upcoming Tours</span></a></li>
            <li><a class="active"><i class="bi bi-pencil-square"></i><span class="nav-text">Reviews</span></a></li>
            <li><a href="sites.php"><i class="bi bi-image"></i><span class="nav-text">Tourist Sites</span></a></li>
        </ul> 
        <div class="accountcontainer">
            <ul>
                <li class="accountname"><i class="bi bi-person-circle"></i><span class="nav-text">Employee Name</span></li>
                <li><button href="#signout"><i class="bi bi-arrow-left-square-fill"></i><span class="nav-text">Sign Out</span></button></li>
            </ul>
        </div>
    </div>
    <div class="dashboardcontainer">
        <div class="content">
            <div class="header">
                <h1>Reviews</h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="tabs">
                <button class="tabbutton active" onclick="setActiveTab(this)">Pending</button>
                <button class="tabbutton" onclick="setActiveTab(this)">Approved</button>
                <button class="tabbutton" onclick="setActiveTab(this)">Archived</button>
            </div>
            <div class="grid">
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
                <div class="griditem">
                    <h3>Site Name</h3>
                    <br>
                    <h2>Author Name</h2>
                    <h2><?php echo generateStarRating(4.2); ?></h2>
                    <br>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc condimentum dui vestibulum metus porta, in ultricies nibh tincidunt. Pellentesque in diam luctus, tempus nibh sed, efficitur mi. Nullam rutrum lacus nisi, ac fringilla nulla laoreet in.</p>
                </div>
            </div>
            <div id="requestModal" class="modal">
                <div class="modal-content">
                    <span id="closeforsmall" class="close">&times;</span>
                    <div class="revcontainer">
                        <span class="bi bi-chat-quote-fill revbg"></span>
                        <h1>Site Name</h1>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse feugiat orci sollicitudin, porttitor mauris sit amet, vulputate risus. Suspendisse potenti. Integer elementum, ante sed finibus finibus, neque mauris venenatis felis, vitae blandit lectus dui non neque. Nunc maximus orci eu elit sodales gravida. Nullam eu metus in ligula blandit sagittis. Vivamus posuere tincidunt ante. Maecenas dignissim libero a risus suscipit, ut varius lacus euismod. Nulla tempor pretium posuere. Morbi sed enim feugiat, lacinia arcu sit amet, bibendum magna. Aenean eu aliquam turpis, sit amet rutrum leo. Praesent vehicula libero neque, eu lobortis odio lacinia et. Sed facilisis neque sem, nec placerat elit convallis ut. Morbi eget odio eget nisl hendrerit ornare.</p>
                        <div class="authorcontainer">
                            <div>
                                <h2>Author Name</h2>
                                <h2><?php echo generateStarRating(4.2); ?></h2>
                            </div>
                            <p>3 weeks ago</p>
                        </div>
                        <div class="displayarchive">
                            <button>Archive</button>
                            <button>Approve</button>
                        </div>
                    </div>
                    <span id="closeforbig" class="close">&times;</span>
                </div>
            </div>
        </div>
    </div>
    <script src="../../../public/assets/scripts/dashboard.js"></script>
</body>
</html>