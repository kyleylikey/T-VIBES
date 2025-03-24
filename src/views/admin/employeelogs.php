<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';
require_once '../../controllers/logscontroller.php';

$database = new Database();
$conn = $database->getConnection();

$userid = $_SESSION['userid'];
$query = "SELECT name FROM Users WHERE userid = :userid LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userid', $userid);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$adminName = $admin ? htmlspecialchars($admin['name']) : "Admin";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Employee Logs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        
        .sidebar {
            font-family: 'Raleway', sans-serif;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px;
            background-color: #FFFFFF;
            z-index: 1000;
            transition: all 0.3s ease-in-out;
        }

        .sidebar img {
            max-width: 100%; 
            height: auto;
            display: block; 
            margin-top: auto;
            margin-bottom: 25%;
            transition: all 0.3s ease-in-out; 
        }

        .menu-section {
            margin-top: auto;
            margin-bottom: auto;
        }

        .nav-link {
            color: #434343 !important;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold; 
            transition: background 0.3s ease, color 0.3s ease;
        }

        .nav-link.active {
            background-color: #EC6350 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .nav-link:hover {
            background-color: #EC6350 !important; 
            color: #FFFFFF !important;
        }

        .nav-link i {
            color: inherit; 
        }
        
        .admin-name.active {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .sign-out.active {
            background-color: #E7EBEE !important;
            color: #102E47 !important;
            font-weight: bold;
        }

        .content-container {
            background-color: #E7EBEE;
            padding: 20px;
            border-radius: 10px;
        }

        .main-content {
            margin-left: 260px;
            padding: 20px;
            transition: all 0.3s ease-in-out;
            width: calc(100% - 260px); 
        }

        .header {
            font-family: 'Raleway', sans-serif;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap; 
            gap: 10px; 
        }

        .date {
            text-align: right; 
            min-width: 150px; 
            flex-shrink: 0; 
        }

        .content-container h2, 
        .content-container .date h2 {
            color: #102E47;
            font-weight: bold;
        }

        .search-container {
            display: flex;
            align-items: center;
            font-family: 'Nunito', sans-serif !important;
        }

        .search-wrapper {
            position: relative;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .search-wrapper i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #102E47;
            font-size: 1.2rem;
        }

        .search-wrapper input {
            width: 100%;
            padding: 10px 10px 10px 40px;
            border: 2px solid #102E47;
            border-radius: 5px;
            outline: none;
            font-size: 1rem;
        }

        .search-wrapper input::placeholder {
            color: #102E47;
            opacity: 0.7;
        }

        .info-box {
            position: relative;
            min-height: 150px;
            min-width: 200px; 
            background-color: rgba(114, 154, 184, 0.2); 
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: flex-start;
            text-align: left;
            font-family: 'Nunito', sans-serif;
        }

        .info-box span {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info-box i {
            font-size: 40px;
            opacity: 0.3;
            align-self: flex-end;
        }

        .table-responsive {
            overflow-x: auto; 
            width: 100%;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px; 
            margin: auto;
            text-align: center;
            overflow-x: auto;
            min-width: 500px;
            font-family: 'Nunito', sans-serif;
        }

        thead th {
            color: #434343; 
            font-weight: bold;
            padding-bottom: 10px;
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        tbody tr {
            background: #FFFFFF; 
            border-radius: 15px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); 
        }

        tbody tr td {
            padding: 10px;
            border: none;
            color: #434343; 
        }

        tbody tr td:first-child {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        tbody tr td:last-child {
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        .no-logs {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin: 40px auto;
            font-family: 'Nunito', sans-serif !important;
        }

        .swal2-icon {
            background: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        .swal2-icon-custom {
            font-size: 10px; 
            color: #EC6350; 
        }

        .swal2-title-custom {
            font-size: 24px !important;
            font-weight: bold;
            color: #434343 !important;
        }

        .swal-custom-popup {
            padding: 20px;
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important;
        }

        .swal-custom-btn {
            padding: 10px 20px !important;
            font-size: 16px !important;
            font-weight: bold !important;
            border: 2px solid #102E47 !important;
            border-radius: 25px !important;
            background-color: #FFFFFF !important;
            color: #434343 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
        }

        .swal-custom-btn:hover {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
        }

        .pagination .page-item .page-link {
            color: #434343;
            border-color: #102E47;
            background-color: #FFFFFF;
        }

        .pagination .page-item.active .page-link {
            background-color: #102E47;
            color: #FFFFFF;
            border-color: #102E47;
        }

        .pagination .page-item .page-link:hover {
            background-color: #102E47;
            color: #FFFFFF;
        }

        @media (max-width: 1280px) {
            .search-wrapper {
                position: relative;
                width: 100%;
            }

            .info-box {
                width: 100%;
                padding: 20px;
            }

            .table-responsive {
                overflow-x: auto;
                display: block;
                width: 100%;
            }

            table {
                min-width: 900px;
                font-size: 17px; 
            }

            thead th,
            tbody td {
                padding: 14px;
                font-size: 16px;
            }

            .search-wrapper input {
                font-size: 18px;
            }

            .no-logs {
                font-size: 18px;
                padding: 14px;
            }
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 250px; 
                padding: 15px;
            }

            .sidebar img {
                margin-bottom: 0;
            }

            .nav-link {
                font-size: 14px; 
                margin-bottom: -5%;
            }

            .menu-section {
                padding: 5px 0;
                margin-top: auto;
                margin-bottom: auto;
            }

            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px); 
            }

            .info-box {
                width: 100%;
                padding: 15px;
            }

            .table-responsive {
                overflow-x: auto;
                display: block;
                width: 100%;
            }

            table {
                min-width: 750px; 
                font-size: 16px; 
            }

            thead th,
            tbody td {
                padding: 12px;
                font-size: 15px;
            }

            .search-wrapper input {
                font-size: 16px;
            }

            .no-logs {
                font-size: 17px;
                padding: 12px;
            }
        }

        @media (max-width: 912px) {
            .sidebar {
                width: 200px; 
                padding: 15px;
                background-color: #FFFFFF; 
            }

            .nav-link {
                font-size: 14px; 
                padding: 8px; 
                color: #434343 !important; 
                font-weight: bold; 
                transition: background 0.3s ease, color 0.3s ease;
            }

            .nav-link.active {
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .admin-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
            }

            .menu-section {
                margin-top: auto;
                margin-bottom: auto;
                padding: 10px 0; 
            }

            .main-content {
                margin-left: 200px;
                width: calc(100% - 200px);
            }

            .info-box {
                width: 100%;
                padding: 12px;
            }

            .table-responsive {
                overflow-x: auto;
                display: block;
                width: 100%;
            }

            table {
                min-width: 700px; 
                font-size: 15px; 
            }

            thead th,
            tbody td {
                padding: 8px;
                font-size: 14px;
            }

            .search-wrapper input {
                font-size: 15px;
            }

            .no-logs {
                font-size: 16px;
                padding: 12px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100px;
                padding: 10px;
                background-color: #FFFFFF;
            }

            .sidebar img {
                max-width: 70px; 
            }

            .nav-link {
                text-align: center;
                padding: 10px;
                color: #434343 !important; 
                font-weight: bold;
                font-size: 12px;
            }

            .nav-link span {
                display: none; 
            }

            .nav-link.active {
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .admin-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
            }

            .main-content {
                margin-left: 100px; 
                width: calc(100% - 100px); 
            }

            .header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .date {
                text-align: center; 
                width: 100%; 
            }

            .info-box {
                width: 100%;
                padding: 12px;
            }

            .table-responsive {
                overflow-x: auto;
                display: block;
                width: 100%;
            }

            table {
                min-width: 600px; 
                font-size: 14px;
            }

            thead th,
            tbody td {
                padding: 8px;
                font-size: 13px; 
            }

            .search-wrapper input {
                font-size: 14px;
            }

            .no-logs {
                font-size: 16px;
                padding: 12px;
            }
        }

        @media (max-width: 600px) {
            .sidebar {
                width: 80px;
                padding: 5px;
                background-color: #FFFFFF;
            }

            .nav-link {
                color: #434343 !important;
                font-weight: bold;
            }

            .nav-link i {
                font-size: 20px;
            }

            .nav-link span {
                display: none;
            }

            .nav-link.active {
                background-color: #EC6350 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .nav-link:hover {
                background-color: #EC6350 !important; 
                color: #FFFFFF !important;
            }

            .admin-name.active {
                background-color: #102E47 !important;
                color: #FFFFFF !important;
                font-weight: bold;
            }

            .sign-out.active {
                background-color: #E7EBEE !important;
                color: #102E47 !important;
                font-weight: bold;
            }

            .main-content {
                margin-left: 80px;
                width: calc(100% - 80px);
            }

            .info-box {
                min-width: 100%;
                padding: 12px;
            }

            .table-responsive {
                overflow-x: auto;
                width: 100%;
            }

            table {
                min-width: 100%;
                font-size: 14px; 
            }

            thead th,
            tbody td {
                padding: 8px; 
                font-size: 13px; 
            }

            .search-wrapper input {
                font-size: 14px; 
            }

            .no-logs {
                font-size: 16px; 
                padding: 12px;
            }
        }

        @media (max-width: 360px) {
            .info-box {
                min-width: 100%; 
                padding: 10px;
            }

            .table-responsive {
                overflow-x: auto;
                width: 100%;
            }

            table {
                min-width: 100%; 
                font-size: 12px; 
            }

            thead th, tbody td {
                padding: 6px; 
            }

            .search-wrapper input {
                font-size: 14px; 
            }

            .no-logs {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    
<div class="sidebar">
    <div class="pt-4 pb-1 px-2 text-center">
        <a href="#" class="text-decoration-none">
            <img src="../../../public/assets/images/headerlogo.jpg" alt="Header Logo" class="img-fluid">
        </a>
    </div>

    <div class="menu-section">
        <ul class="nav nav-pills flex-column mb-4">
            <li class="nav-item mb-4">
                <a href="home.php" class="nav-link">
                    <i class="bi bi-grid"></i>
                    <span class="d-none d-sm-inline">Overview</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="monthlyperformance.php" class="nav-link">
                    <i class="bi bi-bar-chart-line"></i>
                    <span class="d-none d-sm-inline">Monthly Performance</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="tourhistory.php" class="nav-link">
                    <i class="bi bi-map"></i>
                    <span class="d-none d-sm-inline">Tour History</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="touristsites.php" class="nav-link">
                    <i class="bi bi-image"></i>
                    <span class="d-none d-sm-inline">Tourist Sites</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="accounts.php?usertype=mngr" class="nav-link">
                    <i class="bi bi-people"></i>
                    <span class="d-none d-sm-inline">Accounts</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="" class="nav-link active">
                    <i class="bi bi-person-vcard"></i>
                    <span class="d-none d-sm-inline">Employee Logs</span>
                </a>
            </li>
        </ul>
    </div>
        
    <ul class="nav nav-pills flex-column mb-4">
        <li class="nav-item mb-3">
            <a href="javascript:void(0);" class="nav-link admin-name active">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline"><?= $adminName; ?></span>
            </a>
        </li>
        <li class="nav-item">
            <a href="javascript:void(0);" class="nav-link sign-out active" onclick="logoutConfirm()">
                <i class="bi bi-box-arrow-right"></i>
                <span class="d-none d-sm-inline">Sign Out</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="content-container">
        <div class="header">
            <h2>Employee Logs</h2>
            <span class="date">
                <h2>
                    <?php 
                        date_default_timezone_set('Asia/Manila');
                        echo date('M d, Y | h:i A'); 
                    ?>
                </h2>
            </span>
        </div>

        <div class="search-container mt-2 mb-3">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchBar" placeholder="Search">
            </div>
        </div>
        <div class="row mt-3 d-flex justify-content-center">
            <div class="col-lg-12 col-md-12 col-12 mb-3">
                <div class="info-box">
                    <div class="table-responsive">
                        <?php if (count($logs) > 0): ?>
                            <table class="table" id="logTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Activity</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($log['name']); ?></td>
                                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                                            <td><?php echo date('d M Y', strtotime($log['datetime'])); ?></td>
                                            <td><?php echo date('g:i A', strtotime($log['datetime'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="no-logs text-center">No matching employee logs found.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12 d-flex justify-content-center">
                <nav>
                    <ul class="pagination">
                        <?php if ($totalPages > 1): ?>
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script src="../../../public/assets/scripts/emplogs.js"></script>
</body>
</html>