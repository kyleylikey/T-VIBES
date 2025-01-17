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
    $user->setPassword($_POST['password']);

    if ($user->login()) {
        $_SESSION['user_id'] = $user->getId();
        header("Location: /index.php");
    } else {
        echo "Invalid username or password";
    }
}
?>