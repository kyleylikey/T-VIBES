<?php
include '../../../includes/auth.php';
require_once '../../config/dbconnect.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'addAccount') {
    $name = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $contactnum = htmlspecialchars($_POST['contactnum']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $usertype = 'emp'; 
    $status = 'active'; 

    try {
        $query = "INSERT INTO users (name, username, email, contactnum, hashedpassword, usertype, status) 
                  VALUES (:name, :username, :email, :contactnum, :hashedpassword, :usertype, :status)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contactnum', $contactnum);
        $stmt->bindParam(':hashedpassword', $password);
        $stmt->bindParam(':usertype', $usertype);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add account.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'editAccount') {
    $userid = $_POST['userid'];
    $name = htmlspecialchars($_POST['name']);
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $contactnum = htmlspecialchars($_POST['contactnum']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if (empty($name) || empty($username) || empty($email) || empty($contactnum)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    try {
        $query = "UPDATE users SET name = :name, username = :username, email = :email, contactnum = :contactnum";
        if ($password) {
            $query .= ", hashedpassword = :password";
        }
        $query .= " WHERE userid = :userid";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':contactnum', $contactnum);
        if ($password) {
            $stmt->bindParam(':password', $password);
        }
        $stmt->bindParam(':userid', $userid);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update account.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'disableAccount' || $_POST['action'] === 'enableAccount') {
        $userid = $_POST['userid'];
        $newStatus = $_POST['action'] === 'disableAccount' ? 'inactive' : 'active';
        
        try {
            $query = "UPDATE users SET status = :status WHERE userid = :userid";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':status', $newStatus);
            $stmt->bindParam(':userid', $userid);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update account status.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_userid'])) {
    $deleteUserId = $_POST['delete_userid'];
    
    $deleteQuery = "DELETE FROM users WHERE userid = :userid AND usertype = 'trst'";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bindParam(':userid', $deleteUserId);
    
    if ($deleteStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

$defaultType = 'mngr';
$userType = isset($_GET['usertype']) ? $_GET['usertype'] : $defaultType;
$query = "SELECT userid, name, username, contactnum, email, usertype, status FROM users WHERE usertype = :usertype";
$stmt = $conn->prepare($query);
$stmt->bindParam(':usertype', $userType);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Accounts</title>
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

        .add-account-box {
            height: 290px;
            width: 375px;
            background-color: #E7EBEE;
            border-radius: 10px;
            border: 2px dashed #939393;
            position: relative;
            cursor: pointer;
            padding: 15px;
            margin-top: 15px;
            transition: background-color 0.3s ease, border-color 0.3s ease;
            font-family: 'Nunito', sans-serif !important;
        }

        .plus-sign {
            font-size: 75px;
            color: #939393;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .add-text {
            font-size: 16px;
            color: #939393;
            position: absolute;
            bottom: 10px;
            left: 15px;
            margin: 0;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .add-account-box:hover {
            background-color: rgba(114, 154, 184, 0.2);  
            border-color: #729AB8;
        }

        .add-account-box:hover .plus-sign,
        .add-account-box:hover .add-text {
            color: #729AB8; 
        }

        .info-box {
            height: 290px;
            width: 375px;
            background-color: rgba(114, 154, 184, 0.2); 
            border-radius: 10px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: pointer;
            font-family: 'Nunito', sans-serif !important;
        }

        .info-box i {
            font-size: 40px; 
            position: absolute;
            bottom: 32px;
            left: 18px;
            margin: 0;
        }

        .info-box span {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .info-box p {
            font-size: 16px;
            color: #102E47;
            position: absolute;
            bottom: 10px;
            left: 20px;
            margin: 0;
            font-weight: bold;
        }

        .info-box:hover {
            transform: scale(1.03);
            transition: all 0.3s;
        }

        .disabled-account {
            background-color: #E7EBEE;
            opacity: 0.5;
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

        .btn-custom {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            border: 2px solid #102E47;
            border-radius: 25px;
            background-color: white;
            color: #434343;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-top: 10px;
            min-width: 80px;
            text-align: center;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-custom.active {
            background-color: #102E47 !important;
            color: #FFFFFF !important;
            font-weight: bold;
        }

        .btn-custom:hover {
            background-color: #102E47;
            color: #FFFFFF;
            font-weight: bold;
        }

        .modal-body {
            display: flex;
            align-items: center; 
            gap: 20px;
            color: #434343;
            font-weight: bold;
        }

        .modal-dialog {
            max-width: 50%;
        }

        .modal-content {
            border-radius: 25px;
            font-family: 'Nunito', sans-serif !important; 
            background-color: #E7EBEE;
        }

        .modal-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            border-top: none;
        }

        .edit-modal-footer {
            display: flex;
            justify-content: center;
            gap: 15px;
            padding: 20px;
            border-top: none;
        }

        .modal-title {
            font-size: 24px;
            font-weight: bold;
            color: #434343;
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

        #modal-name {
            color: #102E47;
            font-weight: bold;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px; 
        }

        .modal-body {
            padding: 20px 25px; 
        }

        .modal-body p {
            margin-bottom: 10px; 
            text-align: justify;
        }

        .label-text {
            color: #757575;
            font-weight: normal;
        }

        .info-text {
            color: #434343;
            margin-bottom: 15px; 
            font-weight: bold;
        }

        .no-accounts {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #434343;
            padding: 10px;
            border-radius: 8px;
            width: fit-content;
            margin: auto;
            margin-bottom: 30px;
            font-family: 'Nunito', sans-serif !important;
        }

        @media (max-width: 1280px) {
            .search-wrapper {
                position: relative;
                width: 100%;
            }

            .btn-custom {
                padding: 8px 16px;
                font-size: 15px;
            }

            .modal-dialog {
                max-width: 60%;
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

            .info-box,
            .add-account-box {
                height: 250px;
                width: 320px;
            }

            .plus-sign {
                font-size: 60px;
            }

            .add-text {
                font-size: 14px;
            }

            .info-box i {
                font-size: 35px;
                bottom: 30px; 
                left: 15px;
            }

            .info-box p {
                font-size: 14px;
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

            .btn-custom {
                padding: 8px 14px;
                font-size: 14px;
                min-width: 70px;
            }

            .modal-dialog {
                max-width: 65%;
            }

            .modal-body {
                flex-direction: column;
                align-items: center;
                margin-top: 20px;
            }

            .search-wrapper {
                position: relative;
                width: 95%;
                margin: 10px auto;
            }

            .add-account-box, 
            .info-box {
                width: 85%; 
                height: 270px; 
                margin: 12px auto; 
            }

            .plus-sign {
                font-size: 70px; 
            }

            .add-text, 
            .info-box p {
                font-size: 15px; 
            }

            .info-box i {
                font-size: 38px; 
                bottom: 30px; 
                left: 15px;
            }

            .row.justify-content-start {
                justify-content: center !important; 
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

            .btn-custom {
                padding: 6px 12px;
                font-size: 14px;
            }

            .modal-dialog {
                max-width: 70%;
            }

            .modal-body {
                flex-direction: column;
                align-items: center;
            }

            .search-wrapper {
                position: relative;
                width: 95%;
                margin: 10px auto;
            }

            .add-account-box, 
            .info-box {
                width: 90%; 
                height: 260px; 
                margin: 10px auto; 
            }

            .plus-sign {
                font-size: 65px; 
            }

            .add-text, 
            .info-box p {
                font-size: 15px; 
            }

            .info-box i {
                font-size: 35px; 
                bottom: 30px; 
                left: 15px;
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

            .btn-group {
                flex-direction: column;
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .btn-custom {
                width: 90%;
                padding: 8px;
                font-size: 14px;
                margin: 4px auto;
            }

            .button-container {
                flex-direction: column;
                gap: 10px;
            }

            .modal-dialog {
                max-width: 100%;
            }

            .modal-body p {
                margin-bottom: 10px; 
                text-align: center;
            }

            .search-wrapper {
                position: relative;
                width: 90%;
                margin: 10px auto; 
            }

            .add-account-box, 
            .info-box {
                width: 90%; 
                height: 250px; 
                margin: 10px auto; 
            }

            .plus-sign {
                font-size: 60px; 
            }

            .add-text, 
            .info-box p {
                font-size: 14px; 
            }

            .info-box i {
                font-size: 30px; 
                bottom: 30px; 
                left: 15px;
            }

            .modal-dialog {
                max-width: 90%;
                margin: 10px auto;
            }

            .modal-content {
                padding: 15px;
            }

            .modal-header {
                padding: 15px;
            }

            .modal-body {
                padding: 15px;
            }

            .modal-body form {
                width: 100%;
            }

            .modal-body .form-control {
                margin-bottom: 5px; 
            }

            .modal-body .d-flex {
                flex-direction: column;
                gap: 10px;
            }

            .modal-footer {
                flex-direction: column;
                gap: 10px;
            }

            .btn-custom {
                width: 90%;
            }

            #username {
                margin-bottom: -3px; 
            }
        }

        @media (max-width: 360px) {
            .search-wrapper {
                position: relative;
                width: 90%;
                margin: 10px auto; 
            }

            .add-account-box, 
            .info-box {
                width: 90%; 
                height: 250px; 
                margin: 10px auto; 
            }

            .plus-sign {
                font-size: 60px; 
            }

            .add-text, 
            .info-box p {
                font-size: 14px; 
            }

            .info-box i {
                font-size: 30px; 
                bottom: 30px; 
                left: 15px;
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
                <a href="" class="nav-link active">
                    <i class="bi bi-people"></i>
                    <span class="d-none d-sm-inline">Accounts</span>
                </a>
            </li>
            <li class="nav-item mb-4">
                <a href="employeelogs.php" class="nav-link">
                    <i class="bi bi-person-vcard"></i>
                    <span class="d-none d-sm-inline">Employee Logs</span>
                </a>
            </li>
        </ul>
    </div>
        
    <ul class="nav nav-pills flex-column mb-4">
        <li class="nav-item mb-3">
            <a href="" class="nav-link admin-name active">
                <i class="bi bi-person-circle"></i>
                <span class="d-none d-sm-inline">Manager Name</span>
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
            <h2>Accounts</h2>
            <span class="date">
                <h2> 
                    <?php date_default_timezone_set('Asia/Manila'); echo date('M d, Y | h:i A'); ?> 
                </h2>
            </span>
        </div>
        
        <div class="btn-group" role="group">
            <button type="button" class="btn-custom <?php echo ($userType == 'mngr') ? 'active' : ''; ?>" onclick="filterAccounts('mngr')">Manager</button>
            <button type="button" class="btn-custom <?php echo ($userType == 'emp') ? 'active' : ''; ?>" onclick="filterAccounts('emp')">Employee</button>
            <button type="button" class="btn-custom <?php echo ($userType == 'trst') ? 'active' : ''; ?>" onclick="filterAccounts('trst')">Tourist</button>
        </div>
        
        <div class="search-container mt-2 mb-3">
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchBar" placeholder="Search">
            </div>
        </div>
        
        <div class="mt-3 row justify-content-start">
            <?php if ($userType == 'emp'): ?>
                <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3">
                    <div class="add-account-box" onclick="showAddAccountModal()" style="cursor: pointer;">
                        <div class="plus-sign">+</div>
                        <p class="add-text">Add Account</p>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php foreach ($accounts as $account): ?>
                <div class="d-flex justify-content-center col-12 col-md-6 col-lg-3 mb-3" id="user-<?php echo $account['userid']; ?>">
                    <div class="info-box <?php echo $account['status'] === 'inactive' ? 'disabled-account' : ''; ?>" onclick="showAccountModal('<?php echo $account['userid']; ?>', '<?php echo $account['name']; ?>', '<?php echo $account['username']; ?>', '<?php echo $account['contactnum']; ?>', '<?php echo $account['email']; ?>', '<?php echo $account['usertype']; ?>', '<?php echo $account['status']; ?>')">
                        <i class="bi bi-person-circle"></i>
                        <p><?php echo htmlspecialchars($account['name']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="modal fade" id="addAccountModal" tabindex="-1" aria-labelledby="addAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0 text-center">
                <h5 class="modal-title w-100 fw-bold">Add Account</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <form id="addAccountForm" class="w-75">
                    <div class="d-flex gap-2 mb-3">
                        <input type="text" class="form-control" name="name" placeholder="Full Name" required pattern="^[A-Za-z\s]+$" title="Full name must contain only alphabets and spaces.">
                        <input type="text" class="form-control" name="username" placeholder="Username" required pattern="^\w{3,20}$" title="Username must be 3-20 characters long and can only include letters, numbers, and underscores.">
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" name="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="contactnum" placeholder="Contact Number" required pattern="^\d{11}$" title="Contact number must be exactly 11 digits." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="password" class="form-control" name="password" id="addPassword" placeholder="Password" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" title="Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.">
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword('addPassword', 'addPasswordIcon')" style="cursor: pointer;">
                            <i class="bi bi-eye" id="addPasswordIcon"></i>
                        </span>
                    </div>
                    <input type="hidden" name="action" value="addAccount">
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="submit" class="btn-custom">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 id="modal-name"></h4>
                <div id="modal-buttons"></div> 
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="label-text">Username</p>
                            <p class="info-text" id="modal-username"></p>
                            <br>
                            <p class="label-text">Contact Number</p>
                            <p class="info-text" id="modal-contact"></p>
                            <br>
                        </div>
                        <div class="col-md-6">
                            <p class="label-text">Email</p>
                            <p class="info-text" id="modal-email"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editAccountModal" tabindex="-1" aria-labelledby="editAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="modal-header border-0 text-center">
                <h5 class="modal-title w-100 fw-bold">Edit Account</h5>
                <button type="button" class="btn-close position-absolute end-0 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-flex flex-column align-items-center">
                <form class="w-75" id="editAccountForm">
                    <input type="hidden" id="userid">
                    <div class="d-flex gap-2 mb-3">
                        <input type="text" class="form-control" id="fullName" placeholder="Full Name" required pattern="^[A-Za-z\s]+$" title="Full name must contain only alphabets and spaces.">
                        <input type="text" class="form-control" id="username" placeholder="Username" required pattern="^\w{3,20}$" title="Username must be 3-20 characters long and can only include letters, numbers, and underscores.">
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" id="email" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" id="contactNumber" placeholder="Contact Number" required pattern="^\d{11}$" title="Contact number must be exactly 11 digits." inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                    <div class="mb-3 position-relative">
                        <input type="password" class="form-control" id="password" placeholder="New Password (Leave blank to keep current)" required pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$" title="Password must be at least 8 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character.">
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3" onclick="togglePassword('password', 'editPasswordIcon')" style="cursor: pointer;">
                            <i class="bi bi-eye" id="editPasswordIcon"></i>
                        </span>
                    </div>
                    <div class="edit-modal-footer d-flex justify-content-center">
                        <button type="button" class="btn-custom" id="saveEditChanges">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script src="../../../public/assets/scripts/main.js"></script>
<script>
    function showAddAccountModal() {
        var myModal = new bootstrap.Modal(document.getElementById('addAccountModal'));
        myModal.show();
    }

    function togglePassword(inputId, iconId) {
        var passwordInput = document.getElementById(inputId);
        var icon = document.getElementById(iconId);

        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }

    function showAccountModal(userid, name, username, contact, email, userType, status) {
        document.getElementById('modal-name').innerText = name;
        document.getElementById('modal-username').innerText = username;
        document.getElementById('modal-contact').innerText = contact;
        document.getElementById('modal-email').innerText = email;
        
        document.getElementById('userid').value = userid;
        document.getElementById('fullName').value = name;
        document.getElementById('username').value = username;
        document.getElementById('email').value = email;
        document.getElementById('contactNumber').value = contact;

        let modalButtons = document.getElementById('modal-buttons');
        modalButtons.innerHTML = '';

        if (userType === 'emp') {
            if (status === 'active') {
                modalButtons.innerHTML = `
                    <button class="btn-custom" onclick="showEditAccountModal()">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <button type="button" class="btn-custom" onclick="confirmDisable('${userid}')">
                        <i class="bi bi-person-dash"></i> Disable
                    </button>`;
            } else {
                modalButtons.innerHTML = `
                    <button class="btn-custom" onclick="showEditAccountModal()">
                        <i class="bi bi-pencil-square"></i> Edit
                    </button>
                    <button type="button" class="btn-custom" onclick="confirmEnable('${userid}')">
                        <i class="bi bi-person-check"></i> Enable
                    </button>`;
            }
        } else if (userType === 'trst') {
            modalButtons.innerHTML = `
                <button class="btn-custom" onclick="showEditAccountModal()"> 
                    <i class="bi bi-pencil-square"></i> Edit 
                </button>
                <button type="button" class="btn-custom" onclick="confirmDelete('${userid}')">
                    <i class="fas fa-trash-alt"></i> Delete 
                </button>
            `;
        }

        var modal = new bootstrap.Modal(document.getElementById('accountModal'));
        modal.show();
    }

    function showEditAccountModal() {
        var myModal = new bootstrap.Modal(document.getElementById('editAccountModal'));
        myModal.show();
    }

    function confirmDisable(userid) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: "Disable Employee Account?",
            text: "Are you sure you want to disable this employee?",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup",
                confirmButton: "swal-custom-btn",
                cancelButton: "swal-custom-btn"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateAccountStatus(userid, 'disableAccount');
            }
        });
    }

    function confirmEnable(userid) {
        Swal.fire({
            iconHtml: '<i class="fas fa-check-circle"></i>',
            title: "Enable Employee Account?",
            text: "Are you sure you want to enable this employee?",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup",
                confirmButton: "swal-custom-btn",
                cancelButton: "swal-custom-btn"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateAccountStatus(userid, 'enableAccount');
            }
        });
    }

    function updateAccountStatus(userid, action) {
        fetch('accounts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=${action}&userid=${userid}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: action === 'disableAccount' ? "Employee Disabled Successfully!" : "Employee Enabled Successfully!",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire("Error", data.message, "error");
            }
        })
        .catch(error => {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Error",
                text: "An unexpected error occurred.",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
        });
    }

    function confirmDelete(userid) {
        Swal.fire({
            iconHtml: '<i class="fas fa-trash-alt"></i>',
            title: "Delete Tourist Account?",
            text: "Are you sure you want to delete this tourist?",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup",
                confirmButton: "swal-custom-btn",
                cancelButton: "swal-custom-btn"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                deleteUser(userid);
            }
        });
    }

    function deleteUser(userid) {
        fetch("accounts.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "delete_userid=" + encodeURIComponent(userid)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    iconHtml: '<i class="fas fa-circle-check"></i>',
                    title: "Tourist Deleted Successfully!",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                }).then(() => {
                    location.reload();
                    document.getElementById("user-" + userid).remove();
                });
            } else {
                Swal.fire({
                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                    title: "Error Deleting Tourist",
                    text: "Please try again later.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom",
                        popup: "swal-custom-popup"
                    }
                });
            }
        })
        .catch(error => console.error("Error:", error));
    }

    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("addAccountForm").addEventListener("submit", function (e) {
            e.preventDefault(); 

            Swal.fire({
                iconHtml: '<i class="fas fa-thumbs-up"></i>',
                title: "Confirm New Account Details?",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup",
                    confirmButton: "swal-custom-btn",
                    cancelButton: "swal-custom-btn"
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData(document.getElementById("addAccountForm"));

                    fetch("accounts.php", {
                        method: "POST",
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    iconHtml: '<i class="fas fa-circle-check"></i>',
                                    title: "Account Added Successfully!",
                                    timer: 3000,
                                    showConfirmButton: false,
                                    customClass: {
                                        title: "swal2-title-custom",
                                        icon: "swal2-icon-custom",
                                        popup: "swal-custom-popup"
                                    }
                                }).then(() => location.reload());
                            } else {
                                Swal.fire({
                                    iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                                    title: "Error",
                                    text: data.message || "Failed to add account.",
                                    timer: 3000,
                                    showConfirmButton: false,
                                    customClass: {
                                        title: "swal2-title-custom",
                                        icon: "swal2-icon-custom",
                                        popup: "swal-custom-popup"
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            Swal.fire({
                                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                                title: "Error",
                                text: "An unexpected error occurred.",
                                timer: 3000,
                                showConfirmButton: false,
                                customClass: {
                                    title: "swal2-title-custom",
                                    icon: "swal2-icon-custom",
                                    popup: "swal-custom-popup"
                                }
                            });
                        });
                }
            });
        });
    });

    document.getElementById("saveEditChanges").addEventListener("click", function () {
        const userid = document.getElementById("userid").value;
        const fullName = document.getElementById("fullName").value;
        const username = document.getElementById("username").value;
        const email = document.getElementById("email").value;
        const contactNumber = document.getElementById("contactNumber").value;
        const password = document.getElementById("password").value.trim();

        if (!fullName || !username || !email || !contactNumber) {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Missing Fields",
                text: "All fields are required.",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
            return;
        }

        const originalFullName = document.getElementById("modal-name").innerText;
        const originalUsername = document.getElementById("modal-username").innerText;
        const originalEmail = document.getElementById("modal-email").innerText;
        const originalContact = document.getElementById("modal-contact").innerText;

        if (
            fullName === originalFullName &&
            username === originalUsername &&
            email === originalEmail &&
            contactNumber === originalContact &&
            password === ""
        ) {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "No Changes Made",
                text: "You haven't updated any details.",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
            return;
        }

        Swal.fire({
            iconHtml: '<i class="fas fa-thumbs-up"></i>',
            title: "Confirm Changes?",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup",
                confirmButton: "swal-custom-btn",
                cancelButton: "swal-custom-btn"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append("action", "editAccount");
                formData.append("userid", userid);
                formData.append("name", fullName);
                formData.append("username", username);
                formData.append("email", email);
                formData.append("contactnum", contactNumber);
                formData.append("password", password);

                fetch("accounts.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-check-circle"></i>',
                            title: "Account Updated Successfully!",
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                title: "swal2-title-custom",
                                icon: "swal2-icon-custom",
                                popup: "swal-custom-popup"
                            }
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                            title: "Error",
                            text: data.message || "Failed to update account.",
                            timer: 3000,
                            showConfirmButton: false,
                            customClass: {
                                title: "swal2-title-custom",
                                icon: "swal2-icon-custom",
                                popup: "swal-custom-popup"
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                        title: "Error",
                        text: "An unexpected error occurred.",
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    });
                });
            }
        });
    });

    function filterAccounts(type) {
        const url = 'accounts.php?usertype=' + type;
        window.location.href = url;
    }

    document.addEventListener("DOMContentLoaded", function () {
        const searchBar = document.getElementById("searchBar");
        const accountBoxes = document.querySelectorAll(".info-box");
        const addAccountBox = document.querySelector(".add-account-box");
        
        const noAccountsMessage = document.createElement("div");
        noAccountsMessage.className = "no-accounts";
        noAccountsMessage.textContent = "No matching accounts found.";
        noAccountsMessage.style.display = "none"; 
        document.querySelector(".content-container").appendChild(noAccountsMessage);

        searchBar.addEventListener("input", function () {
            const searchQuery = searchBar.value.toLowerCase().trim();
            let hasMatches = false;

            accountBoxes.forEach(box => {
                const accountName = box.querySelector("p").textContent.toLowerCase();

                if (accountName.includes(searchQuery)) {
                    box.style.display = "flex"; 
                    hasMatches = true;
                } else {
                    box.style.display = "none"; 
                }
            });

            if (addAccountBox) {
                addAccountBox.style.display = hasMatches ? "flex" : "none";
            }

            noAccountsMessage.style.display = hasMatches ? "none" : "block";
        });
    });
</script>
</body>
</html>