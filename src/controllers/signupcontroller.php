<?php
require_once '../config/dbconnect.php';
require_once '../models/User.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);

    $user->setUsername($_POST['username']);
    $user->setPassword($_POST['password']); 

    $fullname = htmlspecialchars(strip_tags($_POST['fullname']));
    $contact = htmlspecialchars(strip_tags($_POST['contact']));
    $email = htmlspecialchars(strip_tags($_POST['email']));
    $usertype = 'trst'; 
    $status = 'active'; 

    try {
        $query = "SELECT * FROM Users WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "error", "message" => "Email is already registered."]);
            exit();
        }

        $query = "SELECT * FROM Users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $user->getUsername());
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "error", "message" => "Username is already taken."]);
            exit();
        }

        $query = "INSERT INTO Users (username, name, hashedpassword, contactnum, usertype, status, email) 
                  VALUES (:username, :name, :hashedpassword, :contact, :usertype, :status, :email)";

        $stmt = $db->prepare($query);

        $stmt->bindParam(':username', $user->getUsername());
        $stmt->bindParam(':name', $fullname);
        $stmt->bindParam(':hashedpassword', $user->getPassword());
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':usertype', $usertype);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Account successfully created. You can now log in."]);
        } else {
            echo json_encode(["status" => "error", "message" => "An error occurred during registration. Please try again."]);
        }
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "An unexpected error occurred: " . $e->getMessage()]);
    }
}
?>
