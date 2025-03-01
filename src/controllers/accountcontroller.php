<?php
require_once  __DIR__ .'/../config/dbconnect.php';
require_once  __DIR__ .'/../models/User.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

$database = new Database();
$db = $database->getConnection();
$userModel = new User($db);
$accounts = $userModel->getUserList();
$usertypes = ['mngr' => 'Manager', 'emp' => 'Employee', 'trst' => 'Tourist'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'addEmpAccount') {
        // Add Account Logic (as before)
        $name = $_POST['name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $contactnum = $_POST['contactnum'];
        $plainPassword = $_POST['password'];

        if ($userModel->doesUserExist($email, $username)) {
            echo json_encode(['status' => 'error', 'message' => 'User already exists.']);
            exit();
        }

        $result = $userModel->addEmpAccount($name, $username, $email, $contactnum, $plainPassword);
        echo json_encode(['status' => $result ? 'success' : 'error', 'message' => $result ? 'Employee account created successfully.' : 'Failed to create employee account.']);
        exit();

    } elseif ($action === 'editEmpAccount') {
        // Edit Account Logic
        $accountid = $_POST['accountid'];
        // Retrieve each field; if a field is empty, leave it as null
        $name = !empty(trim($_POST['name'])) ? $_POST['name'] : null;
        $username = !empty(trim($_POST['username'])) ? $_POST['username'] : null;
        $email = !empty(trim($_POST['email'])) ? $_POST['email'] : null;
        $contactnum = !empty(trim($_POST['contactnum'])) ? $_POST['contactnum'] : null;
        $plainPassword = !empty(trim($_POST['password'])) ? $_POST['password'] : null;
        
        // Build update array with only non-empty fields
        $updateData = [];
        if (!is_null($name)) {
            $updateData['name'] = $name;
        }
        if (!is_null($username)) {
            $updateData['username'] = $username;
        }
        if (!is_null($email)) {
            $updateData['email'] = $email;
        }
        if (!is_null($contactnum)) {
            $updateData['contactnum'] = $contactnum;
        }
        if (!is_null($plainPassword)) {
            // Always hash the new password if provided
            $updateData['hashedpassword'] = password_hash($plainPassword, PASSWORD_BCRYPT);
        }
        
        // Make sure there is something to update
        if (empty($updateData)) {
            echo json_encode(['status' => 'error', 'message' => 'No changes were made.']);
            exit();
        }
        
        // Call an update method on the model (see next step)
        $result = $userModel->updateEmpAccount($accountid, $updateData);
        
        echo json_encode(['status' => $result ? 'success' : 'error', 'message' => $result ? 'Account updated successfully.' : 'Failed to update account.']);
        exit();
    }

    elseif ($action === 'disableEmpAcc') {
        $userid = $_POST['userid'] ?? null;

        $result = $userModel->disableEmpAcc($userid);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Employee account disabled successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to disable employee account.']);
        }
        exit();
    }
    elseif ($action === 'enableEmpAcc') {
        $userid = $_POST['userid'] ?? null;

        $result = $userModel->enableEmpAcc($userid);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Employee account enabled successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to enable employee account.']);
        }
        exit();
    }
    elseif ($action === 'deleteTrstAcc') {
        $userid = $_POST['userid'] ?? null;

        $result = $userModel->deleteTrstAcc($userid);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Tourist account deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete employee account.']);
        }
        exit();
    }
}


