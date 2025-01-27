<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../config/dbconnect.php';
require_once '../models/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();

        $user = new User($db);
        $user->setUsername($_POST['username']);
        $plainPassword = $_POST['password'];

        $loginResult = $user->login($plainPassword);

        if ($loginResult === true) {
            if (!$user->isActive()) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Your account is inactive. Please verify your email to activate your account.'
                ]);
            } else {
                $_SESSION['user_id'] = $user->getId();
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Login successful.',
                    'redirect' => '../../public/index.php'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid username or password.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'An internal error occurred. Please try again later.'
        ]);
    }
    exit();
}
?>
