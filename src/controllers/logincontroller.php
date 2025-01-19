<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/dbconnect.php';
require_once '../models/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $user->setUsername($_POST['username']);
    $plainPassword = $_POST['password'];

    if ($user->login($plainPassword)) {
        $_SESSION['user_id'] = $user->getId();
        header("Location: ../../public/index.php");
        exit();
    } else {
        echo "Invalid username or password";
    }
}
?>