<?php
require_once '../../controllers/helpers.php';
require_once '../../controllers/accountcontroller.php';
require_once '../../config/dbconnect.php';

if (!isset($_SESSION['userid'])) {
    header('Location: ../frontend/login.php'); 
    exit();
}

if ($_SESSION['usertype'] !== 'mngr') {
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

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../public/assets/styles/dashboard.css">
    <link rel="stylesheet" href="../../../public/assets/styles/monthlyperformance.css">
    <link rel="stylesheet" href="../../../public/assets/styles/tourrequest.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="../../../public/assets/scripts/main.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
    <div class="vertnavbar">
        <div class="logocontainer">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo">
        </div>
        <ul>
            <li><a href="home.php"><i class="bi bi-grid"></i><span class="nav-text">Overview</span></a></li>
            <li><a href="monthlyperformance.php"><i class="bi bi-graph-up-arrow"></i><span class="nav-text">Monthly Performance</span></a></li>
            <li><a href="tourhistory.php"><i class="bi bi-map-fill"></i><span class="nav-text">Tour History</span></a></li>
            <li><a href="touristsites.php"><i class="bi bi-image"></i><span class="nav-text">Tourist Sites</span></a></li>
            <li><a class="active" href="accounts.php"><i class="bi bi-people"></i><span class="nav-text">Accounts</span></a></li>
            <li><a href="employeelogs.php"><i class="bi bi-person-vcard"></i><span class="nav-text">Employee Logs</span></a></li>
        </ul> 
        <div class="accountcontainer">
            <ul>
                <li class="accountname"><i class="bi bi-person-circle"></i><span class="nav-text">Manager Name</span></li>
                <li><a onclick="logoutConfirm()"><i class="bi bi-arrow-left-square-fill"></i><span class="">Sign Out</span></a></li>
            </ul>
        </div>
    </div>
    <div class="dashboardcontainer">
        <div class="content">
            <div class="header">
                <h1>Accounts</h1>
                <span class="date"><h1><?php 
                    date_default_timezone_set('Asia/Manila');
                    echo date('M d, Y | h:i A'); 
                ?></h1></span>
            </div>
            <div class="tabs">
                <button class="emp tabbutton active">Employee</button>
                <button class="trst tabbutton">Tourist</button>
                <button class="mngr tabbutton">Manager</button>
            </div>
            <div class="searchbarcontainer">
                <input class="searchbar" type="text" id="searchInput" placeholder="Search accounts..." onkeyup="filterAccounts()">
            </div>
            <div class="grid">
                <div style="height:260px;" class="addaccount" id="addAccountButton">
                    <span class="bi bi-person-plus-fill accountplus"></span>
                    <h2 class="accountplusdesc">Add Employee Account</h2>
                </div>
                <?php
                    foreach (['mngr', 'emp', 'trst'] as $type) {
                        if (!empty($accounts[$type])) {
                            foreach ($accounts[$type] as $account) {
                                $opacity = ($account['status'] === 'inactive') ? "opacity: 0.5;" : "";
                                echo '<div style="padding-top: 100px; ' . $opacity . '" class="griditem accountitem" data-userstatus="' . htmlspecialchars($account['status']) . '" data-usertype="' . $type . '" data-userid="' . htmlspecialchars($account['userid']) . '" data-name="' . htmlspecialchars($account['name']) . '" data-username="' . $account['username'] . '" data-email="' . htmlspecialchars($account['email']) . '" data-contact="' . htmlspecialchars($account['contactnum']) . '">';                                                               
                                echo '<span class="bi bi-person-circle accounticon"></span>';
                                echo '<h2>' . htmlspecialchars($account['name']) . '</h2>';
                                echo '<p>' . $usertypes[$type] . '</p>';
                                echo '</div>';
                            }
                        }
                    }

                    if (empty($accounts['mngr']) && empty($accounts['emp']) && empty($accounts['trst'])) {
                        echo '<p>No accounts found.</p>';
                    }
                ?>
            </div>
            <div id="requestModal" class="modal">
                <div class="modal-content" style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                    <span id="closeforsmall" class="close">&times;</span>
                    <h1 id="modalName" class="modal-title">Sample Name</h1>
                    <div class="accdetailscontainer">
                        <input type="hidden" id="modalAccountId" value="">
                        <div>
                            <h4 class="label">Username</h4>
                            <p id="modalUsername">sampleusername</p>
                        </div>
                        <div>
                            <h4 class="label">Email</h4>
                            <p id="modalEmail">sampleemail</p>
                        </div>
                        <div>
                            <h4 class="label">Contact Number</h4>
                            <p id="modalContact">samplecontactnumber</p>
                        </div>
                        <h1 hidden class="label" id="accstatus">Disabled</h1>
                    </div>
                    <div class="displayarchive" style="margin-bottom: 20px;">
                        <button class="btn1" onclick="openEditModalFromModal()">Edit</button>
                        <button class="btn2">Delete</button>
                    </div>
                    <span id="closeforbig" class="close">&times;</span>
                </div>
            </div>
             <!-- Add Account Modal -->
             <div id="addAccountModal" class="modal">
                <div class="modal-content" style="margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                <span id="closeforsmall" class="close">&times;</span>
                    <form style="width:100%" id="addAccountForm" action="../../controllers/accountcontroller.php" method="POST">
                    <input type="hidden" name="action" id="action" value="addEmpAccount">
                    <input type="hidden" name="accountid" id="accountid">
                    <h1 class="modal-title">Add or Edit Account</h1>
                        <div class="addaccformcontainer">
                            <div>
                                <h4 class="label">Name</h4>
                                <input type="text" name="name" id="name" required>
                            </div>
                            <div>
                                <h4 class="label">Username</h4>
                                <input type="text" name="username" id="username" required>
                            </div>
                            <div>
                                <h4 class="label">Password</h4>
                                <input type="password" name="password" id="password" required>
                            </div>
                            <div>
                                <h4 class="label">Email</h4>
                                <input type="email" name="email" id="email" required>
                            </div>
                            <div>
                                <h4 class="label">Contact Number</h4>
                                <input type="text" name="contactnum" id="contactnum" required>
                            </div>
                        </div>
                        <div class="displayarchive" style="margin-bottom: 20px;">
                            <button style="width: 100px;" type="submit">Save</button>
                        </div>
                    </form>
                    <span id="closeforbig" class="close">&times;</span>
                </div>
             </div>
        </div>
    </div>
    <script src="../../../public/assets/scripts/account.js"></script>
    <script src="../../../public/assets/scripts/dashboard.js"></script>
</body>
</html>