<?php
include '../../../../includes/auth.php';
require_once '../../../config/dbconnect.php';


$database = new Database();
$db = $database->getConnection();

// Get individual site opdays instead of calculating the binary value in SQL
$sitesOpDays = [];

if (isset($_SESSION['userid']) && isset($_SESSION['tour_destinations']) && !empty($_SESSION['tour_destinations'])) {
    // Extract site IDs from session
    $siteIds = array_map(function($destination) {
        return $destination['siteid'];
    }, $_SESSION['tour_destinations']);
    
    if (!empty($siteIds)) {
        // Create placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($siteIds), '?'));
        
        // Prepare the query to get individual site opdays
        $sitesQuery = "SELECT siteid, opdays FROM sites WHERE siteid IN ($placeholders)";
        $sitesStmt = $db->prepare($sitesQuery);
        
        // Bind all site IDs as parameters
        foreach ($siteIds as $index => $siteId) {
            $sitesStmt->bindParam($index + 1, $siteIds[$index]);
        }
        
        $sitesStmt->execute();
        $sitesOpDays = $sitesStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Set a default empty value for all_opdays_and_binary - it will be calculated in JS
$getDate = ['all_opdays_and_binary' => '0000000'];

if (isset($_SESSION['userid']) && isset($_SESSION['tour_destinations']) && !empty($_SESSION['tour_destinations'])) {
    // Extract site IDs from session
    $siteIds = array_map(function($destination) {
        return $destination['siteid'];
    }, $_SESSION['tour_destinations']);
    
    if (!empty($siteIds)) {
        // Create placeholders for the IN clause
        $placeholders = implode(',', array_fill(0, count($siteIds), '?'));
        
        // Prepare the query to get common available days
        $availabilityQuery = "SELECT 
            BIN(BIT_AND(s.opdays) & 127) AS all_opdays_and_binary
        FROM 
            sites s
        WHERE 
            s.siteid IN ($placeholders)";
        
        $availabilityStmt = $db->prepare($availabilityQuery);
        
        // Bind all site IDs as parameters
        foreach ($siteIds as $index => $siteId) {
            $availabilityStmt->bindParam($index + 1, $siteIds[$index]);
        }
        
        $availabilityStmt->execute();
        $getDate = $availabilityStmt->fetch(PDO::FETCH_ASSOC);
        
        // Default to '0000000' if no results or if BIT_AND returns null
        if (!$getDate || $getDate['all_opdays_and_binary'] === null) {
            $getDate = ['all_opdays_and_binary' => '0000000'];
        }
    } else {
        $getDate = ['all_opdays_and_binary' => '0000000'];
    }
} else {
    $getDate = ['all_opdays_and_binary' => '0000000'];
}


if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['userid']) && !isset($_SESSION['tour_destinations'])) {
    $userid = $_SESSION['userid'];
    
    $stmt = $db->prepare("SELECT tourid FROM tour WHERE userid = :userid AND status = 'request' ORDER BY created_at DESC SELECT TOP 1");
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        $tourid = $result['tourid'];
        
        $stmt = $db->prepare("SELECT t.siteid, t.date, t.companions, s.sitename, s.siteimage, s.price 
                             FROM tour t 
                             JOIN sites s ON t.siteid = s.siteid 
                             WHERE t.tourid = :tourid AND t.userid = :userid");
        $stmt->bindParam(':tourid', $tourid, PDO::PARAM_INT);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!empty($destinations)) {
            $_SESSION['tour_destinations'] = array_map(function($dest) {
                return [
                    'siteid' => $dest['siteid'],
                    'sitename' => $dest['sitename'],
                    'siteimage' => $dest['siteimage'],
                    'price' => $dest['price']
                ];
            }, $destinations);
            
            $_SESSION['selected_tour_date'] = $destinations[0]['date'];
            $_SESSION['tour_people_count'] = $destinations[0]['companions'];
            $_SESSION['tour_ui_state'] = 'confirmed';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SESSION['tour_ui_state'])) {
    if ($_SESSION['tour_ui_state'] === 'edit') {
    } 

    else if (isset($_SESSION['tour_destinations']) && !empty($_SESSION['tour_destinations']) && !isset($_SESSION['tour_ui_state'])) {
        $_SESSION['tour_ui_state'] = 'edit';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['siteid'])) {
        $siteid = $_POST['siteid'];

        $stmt = $db->prepare("SELECT siteid, sitename, siteimage, price FROM sites WHERE siteid = :siteid");
        $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
        $stmt->execute();
        $site = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($site) {
            if (!isset($_SESSION['tour_destinations'])) {
                $_SESSION['tour_destinations'] = [];
            }

            foreach ($_SESSION['tour_destinations'] as $dest) {
                if ($dest['siteid'] == $siteid) {
                    echo json_encode(['success' => false, 'message' => 'This destination is already in your tour list.']);
                    exit();
                }
            }

            $_SESSION['tour_destinations'][] = $site;
            echo json_encode(['success' => true]);
            exit();
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid site.']);
            exit();
        }
    }

    if (isset($_POST['remove_siteid'])) {
        $remove_siteid = $_POST['remove_siteid'];
        if (isset($_SESSION['tour_destinations'])) {
            foreach ($_SESSION['tour_destinations'] as $index => $dest) {
                if ($dest['siteid'] == $remove_siteid) {
                    unset($_SESSION['tour_destinations'][$index]);
                    $_SESSION['tour_destinations'] = array_values($_SESSION['tour_destinations']);
                    echo json_encode(['success' => true]);
                    exit();
                }
            }
        }
        echo json_encode(['success' => false, 'message' => 'Destination not found.']);
        exit();
    }

    if (isset($_POST['selected_date']) && isset($_POST['create_request'])) {
        $selectedDate = $_POST['selected_date'];
        $_SESSION['selected_tour_date'] = $selectedDate;
        $_SESSION['tour_ui_state'] = 'confirmed';

        if (!isset($_SESSION['userid'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            exit();
        }

        $userid = $_SESSION['userid'];
        $companions = isset($_SESSION['tour_people_count']) ? $_SESSION['tour_people_count'] : 1;
 
        if (isset($_SESSION['tour_destinations']) && !empty($_SESSION['tour_destinations'])) {
            $successCount = 0;
 
            $db->beginTransaction();
 
            try {
                $stmt = $db->query("SELECT MAX(tourid) as max_id FROM tour");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $newTourId = ($result['max_id'] ? $result['max_id'] + 1 : 1);
 
                foreach ($_SESSION['tour_destinations'] as $destination) {
                    $siteid = $destination['siteid'];
 
                    $stmt = $db->prepare("INSERT INTO tour (tourid, siteid, userid, status, date, companions, created_at) 
                                    VALUES (:tourid, :siteid, :userid, 'request', :date, :companions, NOW())");
                    $stmt->bindParam(':tourid', $newTourId, PDO::PARAM_INT);
                    $stmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
                    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $stmt->bindParam(':date', $selectedDate);
                    $stmt->bindParam(':companions', $companions, PDO::PARAM_INT);
 
                    if ($stmt->execute()) {
                        $successCount++;
                    }
                }
 
                if ($successCount == count($_SESSION['tour_destinations'])) {
                    $db->commit();
                    echo json_encode(['success' => true, 'message' => 'Tour requests created successfully']);
                } else {
                    $db->rollback();
                    echo json_encode(['success' => false, 'message' => 'Some tour requests failed to be created']);
                }
                exit();
            } catch (Exception $e) {
                $db->rollback();
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No destinations selected']);
            exit();
        }
    }
    
    if (isset($_POST['edit_mode'])) {
        $_SESSION['tour_ui_state'] = 'edit'; 
        echo json_encode(['success' => true]);
        exit();
    }

    if (isset($_POST['selected_date']) && isset($_POST['save_changes']) && isset($_POST['update_db']) && isset($_POST['people_count'])) {
        $selectedDate = $_POST['selected_date'];
        $companions = (int)$_POST['people_count'];
        $destinations = json_decode($_POST['destinations'] ?? '[]', true);
        
        if (!isset($_SESSION['userid'])) {
            echo json_encode(['success' => false, 'message' => 'User not logged in']);
            exit();
        }
        
        $userid = $_SESSION['userid'];
        
        $stmt = $db->prepare("SELECT DISTINCT tourid FROM tour WHERE userid = :userid ORDER BY created_at DESC SELECT TOP 1");
        $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            echo json_encode(['success' => false, 'message' => 'Could not find tour request']);
            exit();
        }
        
        $tourid = $result['tourid'];
        
        $db->beginTransaction();
        
        try {
            $updateStmt = $db->prepare("UPDATE tour SET date = :date, companions = :companions 
                                        WHERE tourid = :tourid AND userid = :userid");
            $updateStmt->bindParam(':date', $selectedDate);
            $updateStmt->bindParam(':companions', $companions, PDO::PARAM_INT);
            $updateStmt->bindParam(':tourid', $tourid, PDO::PARAM_INT);
            $updateStmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $updateStmt->execute();
            
            $currentStmt = $db->prepare("SELECT siteid FROM tour WHERE tourid = :tourid AND userid = :userid");
            $currentStmt->bindParam(':tourid', $tourid, PDO::PARAM_INT);
            $currentStmt->bindParam(':userid', $userid, PDO::PARAM_INT);
            $currentStmt->execute();
            $currentDestinations = $currentStmt->fetchAll(PDO::FETCH_COLUMN);
            
            $destinationsToRemove = array_diff($currentDestinations, $destinations);
            if (!empty($destinationsToRemove)) {
                $placeholders = implode(',', array_fill(0, count($destinationsToRemove), '?'));
                $deleteStmt = $db->prepare("DELETE FROM tour WHERE tourid = ? AND userid = ? AND siteid IN ($placeholders)");
                $deleteParams = array_merge([$tourid, $userid], $destinationsToRemove);
                $deleteStmt->execute($deleteParams);
            }
 
            $destinationsToAdd = array_diff($destinations, $currentDestinations);
            if (!empty($destinationsToAdd)) {
                foreach ($destinationsToAdd as $siteid) {
                    $insertStmt = $db->prepare("INSERT INTO tour (tourid, siteid, userid, status, date, companions, created_at) 
                                                VALUES (:tourid, :siteid, :userid, 'request', :date, :companions, NOW())");
                    $insertStmt->bindParam(':tourid', $tourid, PDO::PARAM_INT);
                    $insertStmt->bindParam(':siteid', $siteid, PDO::PARAM_INT);
                    $insertStmt->bindParam(':userid', $userid, PDO::PARAM_INT);
                    $insertStmt->bindParam(':date', $selectedDate);
                    $insertStmt->bindParam(':companions', $companions, PDO::PARAM_INT);
                    $insertStmt->execute();
                }
            }
 
            $_SESSION['selected_tour_date'] = $selectedDate;
            $_SESSION['tour_people_count'] = $companions;
            $_SESSION['tour_ui_state'] = 'confirmed';
 
            if (!empty($destinations)) {
                $placeholders = implode(',', array_fill(0, count($destinations), '?'));
                $siteStmt = $db->prepare("SELECT siteid, sitename, siteimage, price FROM sites WHERE siteid IN ($placeholders)");
                $siteStmt->execute($destinations);
                $sites = $siteStmt->fetchAll(PDO::FETCH_ASSOC);
                $_SESSION['tour_destinations'] = $sites;
            } else {
                $_SESSION['tour_destinations'] = [];
            }
 
            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Tour request updated successfully']);
        } catch (Exception $e) {
            $db->rollback();
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit();
    }

    
    if (isset($_POST['action']) && $_POST['action'] === "get_availability") {
        $datesAvailability = [];
        $currentYear = date("Y");
        $currentMonth = date("m");

        if (!empty($_POST['sites'])) {
            // Decode the JSON string into an array
            $siteIds = json_decode($_POST['sites'], true);
            
            // Ensure we have an array to work with
            if (!is_array($siteIds)) {
                $siteIds = [$siteIds];
            }
            
            $siteIdPlaceholders = implode(',', array_fill(0, count($siteIds), '?'));

            $stmt = $db->prepare("SELECT date, COUNT(*) as bookings FROM tour WHERE siteid IN ($siteIdPlaceholders) AND YEAR(date) = ? AND MONTH(date) = ? GROUP BY date");
            $params = array_merge($siteIds, [$currentYear, $currentMonth]);
            $stmt->execute($params);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($bookings as $booking) {
                $bookedDate = $booking['date'];
                $bookingCount = (int) $booking['bookings'];

                if ($bookingCount >= 10) {
                    $datesAvailability[$bookedDate] = 'unavailable';
                } else {
                    $datesAvailability[$bookedDate] = 'available';
                }
            }
        }

        echo json_encode(['success' => true, 'availability' => $datesAvailability]);
        exit();
    }
    if (isset($_POST['people_count'])) {
        $_SESSION['tour_people_count'] = (int)$_POST['people_count'];
        echo json_encode(['success' => true]);
        exit();
    }

    if (isset($_POST['reset_ui'])) {
        if (isset($_SESSION['tour_ui_state'])) {
            unset($_SESSION['tour_ui_state']);
        }

        if (isset($_SESSION['tour_destinations'])) {
            unset($_SESSION['tour_destinations']);
        }
        if (isset($_SESSION['selected_tour_date'])) {
            unset($_SESSION['selected_tour_date']);
        }
 
        echo json_encode(['success' => true]);
        exit();
    }
}

if (isset($_POST['action']) && $_POST['action'] === "submit_request") {
    if (!isset($_SESSION['userid'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in']);
        exit();
    }

    $userid = $_SESSION['userid'];

    $stmt = $db->prepare("SELECT tourid FROM tour WHERE userid = :userid AND status = 'request' ORDER BY created_at DESC SELECT TOP 1");
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'No pending tour request found']);
        exit();
    }

    $tourid = $result['tourid'];

    $stmt = $db->prepare("UPDATE tour SET status = 'submitted' WHERE tourid = :tourid AND userid = :userid");
    $stmt->bindParam(':tourid', $tourid, PDO::PARAM_INT);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);

    if ($stmt->execute()) {
        unset($_SESSION['tour_ui_state']);
        unset($_SESSION['tour_destinations']);
        unset($_SESSION['selected_tour_date']);
        unset($_SESSION['tour_people_count']);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update tour status']);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour Request - Taal Heritage Town</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;700&family=Raleway:wght@300;400;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            padding: 20px;
        }

        .nav-link {
            font-size: 20px; 
            font-family: 'Raleway', sans-serif !important;
        }

        header a.nav-link {
            color: #434343 !important;
            font-weight: normal !important;
            transition: color 0.3s ease, font-weight 0.3s ease;
        }

        header a.nav-link:hover {
            color: #729AB8 !important;
        }

        header a.nav-link.active {
            color: #729AB8 !important;
            font-weight: bold !important;
        }

        .navbar-nav .btn-danger {
            background-color: transparent !important;
            border: 2px solid #EC6350 !important;
            color: #EC6350 !important;
            transition: all 0.3s ease;
            font-weight: bold !important;
        }

        .navbar-nav .btn-danger:hover {
            background-color: #EC6350 !important;
            color: #FFFFFF !important;
            font-weight: bold !important;
        }

        .main-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            align-items: flex-start;
            margin-top: 20px;
        }

        .tour-container {
            background-color: #e9ecef;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 60%;
        }

        .destination-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .destination-number {
            font-size: 18px;
            font-weight: bold;
            color: #102E47;
            width: 36px;
            height: 36px;
            background-color: #fff;
            border: 2px solid #102E47;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
        }

        .destination-item {
            background-color: #fff;
            padding: 16px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            flex-grow: 1;
            justify-content: space-between;
        }

        .destination-image {
            width: 80px;
            height: 80px;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            font-size: 30px;
            color: #6c757d;
            flex-shrink: 0;
        }

        .destination-details span {
            font-weight: bold;
            color: #102E47;
            font-size: 18px;
        }

        .destination-price {
            font-weight: bold;
            color: #434343;
        }

		.delete-btn {
            background-color: #A9221C;
            color: #fff;
            border: none;
            padding: 8px;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.2s;
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
		}

		.delete-btn:hover {
			background-color: #8a1b16;
		}

		.swal-btn-confirm {
            background-color: #102E47 !important;
            color: #fff !important;
            border-radius: 20px !important;
            padding: 8px 24px !important;
            font-size: 16px;
            font-weight: bold;
		}

		.swal-btn-cancel {
			background-color: #fff !important;
			color: #102E47 !important;
			border: 1px solid #102E47 !important;
			border-radius: 20px !important;
			padding: 8px 24px !important;
			font-size: 16px;
			font-weight: bold;
		}

		.swal-btn-confirm:hover {
			background-color: #0d2538 !important;
		}
		.swal-btn-cancel:hover {
			background-color: #f1f1f1 !important;
		}

        .people-counter {
            margin-top: 20px;
            text-align: center;
        }

        .people-counter {
            color: #102E47 !important;
            font-family: 'Raleway', sans-serif !important;
            font-weight: bold !important;
        }

        .counter-btn {
            background-color: #A9221C;
            color: white;
            border: none;
            padding: 6px;
            border-radius: 50%;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-weight: 900;
        }

        .counter-btn:hover {
            background-color: #8a1b16;
        }

        #tour-date {
            display: none;
            margin-top: 12px;
        }

        .estimated-fees-container {
            background-color: #E7EBEE !important;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 30%;
            align-self: flex-start;
        }

        .estimated-fees {
            background-color: #FFFFFF;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: left;
            color: #434343 !important;
        }

        .estimated-fees h5 {
            color: #102E47;
            font-weight: bold;
            font-family: 'Raleway', sans-serif !important;
        }

        .submit-btn {
            background-color: #EC6350;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 24px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            display: none;
            margin-top: 12px;
        }

        .edit-btn {
            background-color: #EC6350;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 24px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            display: none;
            margin-top: 12px;
        }

        .btn-action {
            border: 2px solid #EC6350 !important;
            color: #EC6350 !important;
            border-radius: 25px !important;
            padding: 10px 20px !important;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
            font-weight: bold !important;
            font-family: 'Nunito', sans-serif !important;
        }

        .btn-action:hover {
            background-color: #EC6350;
            color: #FFFFFF !important;
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
            font-family: 'Raleway', sans-serif !important;
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

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            width: 40%; 
            max-width: 500px; 
            border-radius: 25px !important; 
            text-align: center;
            transform: scale(0.8);
            transition: transform 0.3s ease-in-out, opacity 0.3s ease-in-out;
            position: relative; 
        }

        .close-btn {
            position: absolute;
            top: 25px;
            right: 30px;
            cursor: pointer;
            font-size: 30px;
            background: none;
            border: none;
            outline: none;
        }

        .modal.show {
            display: block;
            opacity: 1;
        }

        .modal.show .modal-content {
            transform: scale(1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border: none !important;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 24px;
            font-family: 'Raleway', sans-serif !important;
            color: #434343; 
            font-weight: bold;
        }

        .modal-body {
            padding: 20px 0;
            font-size: 16px;
            border: none !important;
        }

        .modal-footer {
            display: flex;
            justify-content: center !important; 
            padding-top: 10px;
            border: none !important;
        }

        .modal-footer .btn {
            padding: 10px 20px;
            border: 2px solid #EC6350; 
            background-color: #FFFFFF; 
            color: #EC6350; 
            cursor: pointer;
            border-radius: 25px; 
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }

        .modal-footer .btn:hover {
            background-color: #EC6350; 
            color: #FFFFFF; 
        }

        .calendar {
            text-align: center;
            font-family: 'Nunito', sans-serif !important;
        }

        .month {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            font-size: 20px;
            font-weight: bold;
            font-family: 'Raleway', sans-serif !important;
        }

        .weekdays, .days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .weekdays span, .days span {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .available {
            background-color: #729AB8;
            color: #FFFFFF;
            cursor: pointer;
        }

        .selected {
            background-color: #434343;
            color: #FFFFFF;
            cursor: pointer;
        }

        .disabled {
            color: #ccc;
            pointer-events: none;
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }

        .legend-item {
            width: 15px;
            height: 15px;
            display: inline-block;
            border-radius: 3px;
        }

        .legend-item.available {
            background-color: #729AB8;
        }

        .prev, .next {
            width: 35px;
            height: 35px;
            border: 2px solid #102E47;
            border-radius: 50%;
            background-color: #FFFFFF;
            color: #434343;
            font-size: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .prev:hover, .next:hover {
            background-color: #102E47;
            color: #FFFFFF;
        }

        .selected-date-container {
            margin: 15px 0;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
        }

        .selected-date-label {
            font-weight: bold;
            color: #102E47;
            margin-bottom: 5px;
            font-family: 'Raleway', sans-serif !important;
        }

        .selected-date-value {
            font-size: 16px;
            color: #434343;
            font-family: 'Nunito', sans-serif !important;
        }

        #edit-btn {
            margin-top: 10px;
            border: 2px solid #EC6350; 
            color: #EC6350; 
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #edit-btn:hover {
            background-color: #EC6350; 
            color: #FFFFFF; 
        }

        @media (min-width: 769px) and (max-width: 1280px) {
            .main-container {
                flex-direction: column;
                gap: 20px;
                margin-top: 20px;
            }
            
            .tour-container {
                width: 100%;
                padding: 24px;
            }
            
            .estimated-fees-container {
                width: 100%;
                padding: 24px;
                margin-top: 10px;
            }
            
            .destination-item {
                padding: 16px;
                gap: 12px;
            }
            
            .destination-image {
                width: 80px;
                height: 80px;
            }
            
            .destination-details span {
                font-size: 18px;
            }
            
            .destination-wrapper {
                gap: 12px;
                margin-bottom: 12px;
            }
            
            .destination-number {
                width: 36px;
                height: 36px;
                font-size: 18px;
            }
            
            .delete-btn {
                width: 40px;
                height: 40px;
            }
            
            .people-counter {
                margin-top: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }
            
            .counter-btn {
                width: 32px;
                height: 32px;
            }
            
            .actions {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-top: 20px;
            }
            
            .btn-action {
                padding: 10px 20px !important;
                min-width: 200px;
            }
            
            .modal-content {
                width: 70%;
                margin: 10% auto;
            }
            
            .month {
                font-size: 20px;
            }
            
            .weekdays span, .days span {
                padding: 10px;
                font-size: 16px;
            }
            
            .prev, .next {
                width: 35px;
                height: 35px;
            }
            
            .estimated-fees h5 {
                font-size: 20px;
            }
            
            .total-price {
                font-size: 18px;
            }
            
            .submit-btn, .edit-btn {
                padding: 12px;
                font-size: 16px;
            }
            
            #calendar-days {
                gap: 3px;
            }
            
            .legend {
                flex-wrap: wrap;
                justify-content: center;
                margin-top: 20px;
            }
        }

        @media (min-width: 601px) and (max-width: 768px) {
            .main-container {
                flex-direction: column;
                gap: 20px;
                margin-top: 20px;
            }
            
            .tour-container, .estimated-fees-container {
                width: 100%;
                padding: 22px;
            }
            
            .destination-item {
                padding: 15px;
                gap: 10px;
            }
            
            .destination-image {
                width: 75px;
                height: 75px;
            }
            
            .destination-details span {
                font-size: 17px;
            }
            
            .destination-wrapper {
                gap: 10px;
                margin-bottom: 10px;
            }
            
            .destination-number {
                width: 34px;
                height: 34px;
                font-size: 17px;
            }
            
            .delete-btn {
                width: 38px;
                height: 38px;
            }
            
            .people-counter {
                margin-top: 18px;
            }
            
            .counter-btn {
                width: 30px;
                height: 30px;
            }
            
            .btn-action {
                padding: 10px 18px !important;
                font-size: 16px !important;
            }
            
            .modal-content {
                width: 80%;
                margin: 12% auto;
            }
            
            .month {
                font-size: 19px;
            }
            
            .weekdays span, .days span {
                padding: 9px;
                font-size: 15px;
            }
            
            .estimated-fees h5 {
                font-size: 19px;
            }
            
            .total-price {
                font-size: 17px;
            }
        }

        @media (max-width: 600px) {
            .main-container {
                flex-direction: column;
                gap: 15px;
                margin-top: 15px;
            }
            
            .tour-container, .estimated-fees-container {
                width: 100%;
                padding: 20px;
            }
            
            .destination-item {
                padding: 14px;
                gap: 10px;
            }
            
            .destination-image {
                width: 70px;
                height: 70px;
            }
            
            .destination-details span {
                font-size: 16px;
            }
            
            .destination-wrapper {
                gap: 10px;
                margin-bottom: 10px;
            }
            
            .destination-number {
                width: 32px;
                height: 32px;
                font-size: 16px;
            }
            
            .delete-btn {
                width: 36px;
                height: 36px;
            }
            
            .people-counter {
                margin-top: 15px;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                align-items: center;
                gap: 10px;
            }
            
            .counter-btn {
                width: 30px;
                height: 30px;
            }
            
            #counter-input {
                width: 60px;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 10px !important;
                padding: 10px 15px !important;
            }
            
            .actions {
                display: flex;
                flex-direction: column;
                width: 100%;
                margin-top: 15px;
            }
            
            .submit-btn, .edit-btn {
                width: 100%;
                padding: 12px;
            }
            
            .modal-content {
                width: 95%;
                padding: 15px;
                margin: 15% auto;
            }
            
            .month {
                font-size: 18px;
            }
            
            .weekdays span, .days span {
                padding: 8px;
                font-size: 14px;
            }
            
            .prev, .next {
                width: 30px;
                height: 30px;
                font-size: 18px;
            }
            
            #month-select, #year-select {
                font-size: 16px;
                padding: 4px;
                max-width: 100px;
            }
            
            .modal-header h2 {
                font-size: 20px;
            }
            
            .close-btn {
                top: 20px;
                right: 20px;
                font-size: 26px;
            }
            
            .legend {
                margin-top: 25px;
                flex-wrap: wrap;
            }
            
            .estimated-fees h5 {
                font-size: 18px;
            }
            
            .total-price {
                font-size: 16px;
            }
        }

        @media (max-width: 320px) {
            .main-container {
                flex-direction: column;
                gap: 15px;
                margin-top: 10px;
            }
            
            .tour-container, .estimated-fees-container {
                width: 100%;
                padding: 15px;
            }
            
            .destination-item {
                flex-direction: column;
                padding: 12px;
                gap: 8px;
                align-items: flex-start;
            }
            
            .destination-image {
                width: 60px;
                height: 60px;
                margin: 0 auto;
            }
            
            .destination-details {
                text-align: center;
                width: 100%;
            }
            
            .destination-details span {
                font-size: 16px;
            }
            
            .destination-actions {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 5px;
            }
            
            .destination-wrapper {
                gap: 8px;
            }
            
            .destination-number {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }
            
            .delete-btn {
                width: 32px;
                height: 32px;
            }
            
            .people-counter {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .counter-btn {
                width: 28px;
                height: 28px;
            }
            
            #counter-input {
                width: 50px;
            }
            
            .btn-action {
                width: 100%;
                margin-bottom: 10px !important;
                padding: 8px 15px !important;
                font-size: 14px !important;
            }
            
            .actions {
                display: flex;
                flex-direction: column;
                width: 100%;
            }
            
            .submit-btn, .edit-btn {
                width: 100%;
                padding: 10px;
                font-size: 14px;
            }
            
            .modal-content {
                width: 90%;
                padding: 15px;
                margin: 15% auto;
            }
            
            .month {
                font-size: 16px;
            }
            
            .weekdays span, .days span {
                padding: 5px;
                font-size: 12px;
            }
            
            .prev, .next {
                width: 28px;
                height: 28px;
                font-size: 16px;
            }
            
            #month-select, #year-select {
                font-size: 14px;
                padding: 2px;
                max-width: 80px;
            }
            
            .modal-header h2 {
                font-size: 18px;
            }
            
            .close-btn {
                top: 15px;
                right: 15px;
                font-size: 24px;
            }
            
            .legend {
                margin-top: 20px;
                font-size: 12px;
            }
            
            .estimated-fees h5 {
                font-size: 16px;
            }
            
            .total-price {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>

<?php include '../../templates/headertours.php'; ?>
<?php include '../../templates/toursnav.php'; ?>

<div class="main-container">
    <div class="tour-container">
        <?php if (!empty($_SESSION['tour_destinations'])): ?>
            <?php foreach ($_SESSION['tour_destinations'] as $index => $site): ?>
                <div class="destination-wrapper" data-index="<?php echo $index + 1; ?>" data-siteid="<?php echo $site['siteid']; ?>" data-price="<?php echo $site['price']; ?>">
                    <div class="destination-number"><?php echo $index + 1; ?></div>
                    <div class="destination-item">
                        <img src="../../../../public/uploads/<?php echo htmlspecialchars($site['siteimage']); ?>" alt="Destination Image" class="destination-image">
                        <div class="destination-details">
                            <span><?php echo htmlspecialchars($site['sitename']); ?></span>
                        </div>
                        <div class="destination-actions">
                            <span class="destination-price">₱ <?php echo number_format($site['price'], 2); ?></span>
                            <!-- Update this line in the HTML section where destination buttons are created -->
                            <button class="delete-btn" onclick="confirmRemoveDestination(<?php echo $site['siteid']; ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center; font-weight: bold; margin-top: 50px; margin-bottom: 50px; color: #434343;">No destinations added yet.</p>
        <?php endif; ?>
        <div class="people-counter">
            <label>How Many People?</label>
            <button class="counter-btn" id="minus-btn">-</button>
            <input type="number" id="counter-input" value="1" min="1" max="255" style="width: 40px; text-align: center;">
            <button class="counter-btn" id="plus-btn">+</button>

            <input type="date" class="form-control" id="tour-date" 
                data-availabledate="<?php echo isset($getDate['all_opdays_and_binary']) ? 
                                    htmlspecialchars($getDate['all_opdays_and_binary']) : '0000000'; ?>" 
                style="display: none;">            
            <div class="actions text-center mt-3">
                <button id="addMoreDestinations" class="btn btn-action mb-3">Add More Destinations</button>
                <button id="check-btn" class="btn btn-action mb-3" onclick="verifyAvailableDate()">Check Availability</button>
            </div>
        </div>
    </div>

    <div class="estimated-fees-container">
        <div class="estimated-fees">
            <h5>Estimated Fees</h5>
            <div>
                
            </div>
            <p class="total-price"><strong id="estimatedFees"></strong></p>
            <button class="submit-btn" id="submit-btn">Submit Request</button>
        </div>
    </div>
</div>

<div id="availabilityModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Available Dates</h2>
            <button class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <div class="modal-body">
            <div class="calendar">
                <div class="month">
                    <button class="prev" onclick="prevMonth()" aria-label="Previous Month">&#10094;</button>

                    <label for="month-select" class="visually-hidden">Select Month</label>
                    <select id="month-select" onchange="changeMonth()">
                        <option value="0">January</option>
                        <option value="1">February</option>
                        <option value="2">March</option>
                        <option value="3">April</option>
                        <option value="4">May</option>
                        <option value="5">June</option>
                        <option value="6">July</option>
                        <option value="7">August</option>
                        <option value="8">September</option>
                        <option value="9">October</option>
                        <option value="10">November</option>
                        <option value="11">December</option>
                    </select>

                    <label for="year-select" class="visually-hidden">Select Year</label>
                    <select id="year-select" onchange="changeYear()"></select>

                    <button class="next" onclick="nextMonth()" aria-label="Next Month">&#10095;</button>
                </div>

                <div class="weekdays">
                    <span>Su</span><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span>
                </div>
                <div class="days" id="calendar-days"></div>

                <div class="legend">
                    <span class="legend-item available"></span> Available
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn">Confirm Selections</button>
        </div>
    </div>
</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
<script>  
document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById('tour-date');
    const minusBtn = document.getElementById("minus-btn");
    const plusBtn = document.getElementById("plus-btn");
    const counterInput = document.getElementById("counter-input");
    <?php if (isset($_SESSION['tour_people_count'])): ?>
    counterInput.value = <?php echo $_SESSION['tour_people_count']; ?>;
    <?php endif; ?>
    const submitBtn = document.getElementById("submit-btn");
    submitBtn.style.display = "none";

    <?php if (isset($_SESSION['tour_ui_state']) && $_SESSION['tour_ui_state'] === 'edit'): ?>
    document.getElementById("addMoreDestinations").style.display = "inline-block";
    document.getElementById("check-btn").style.display = "inline-block";
    document.getElementById("submit-btn").style.display = "none";
 
    let saveChangesBtn = document.createElement("button");
    saveChangesBtn.id = "save-changes-btn";
    saveChangesBtn.classList.add("btn", "btn-action");
    saveChangesBtn.innerHTML = `Save Changes`;
 
    saveChangesBtn.style.border = "2px solid #EC6350";
    saveChangesBtn.style.color = "#EC6350";
    saveChangesBtn.style.padding = "10px 15px";
    saveChangesBtn.style.cursor = "pointer";
    saveChangesBtn.style.borderRadius = "25px";
    saveChangesBtn.style.fontWeight = "bold";
    saveChangesBtn.style.transition = "all 0.3s ease";
    saveChangesBtn.style.width = "20%";
    saveChangesBtn.style.marginTop = "50px";
    saveChangesBtn.style.margin = "auto";
    saveChangesBtn.style.display = "block";
    
    saveChangesBtn.addEventListener("mouseover", function() {
        this.style.backgroundColor = "#EC6350";
        this.style.color = "#FFFFFF";
    });
    
    saveChangesBtn.addEventListener("mouseout", function() {
        this.style.backgroundColor = "#E7EBEE";
        this.style.color = "#EC6350";
    });
    
    saveChangesBtn.addEventListener("click", function() {
        saveChanges();
    });
    
    document.querySelector(".actions").appendChild(saveChangesBtn);
    unlockTourSelections();
    isEditMode = true;
    
    <?php if (isset($_SESSION['selected_tour_date']) && !empty($_SESSION['selected_tour_date'])): ?>
    previouslySelectedDate = "<?php echo $_SESSION['selected_tour_date']; ?>";
    <?php endif; ?>
    
    <?php if (isset($_SESSION['tour_people_count'])): ?>
    originalPeopleCount = <?php echo $_SESSION['tour_people_count']; ?>;
    <?php endif; ?>
    
    <?php elseif (isset($_SESSION['tour_ui_state']) && $_SESSION['tour_ui_state'] === 'confirmed'): ?>
    document.getElementById("addMoreDestinations").style.display = "none";
    document.getElementById("check-btn").style.display = "none";
    document.getElementById("submit-btn").style.display = "block";
    
    <?php if (isset($_SESSION['selected_tour_date']) && !empty($_SESSION['selected_tour_date'])): ?>
    document.getElementById("tour-date").value = "<?php echo $_SESSION['selected_tour_date']; ?>";
    selectedDate = "<?php echo $_SESSION['selected_tour_date']; ?>";
    previouslySelectedDate = selectedDate;
    initializeConfirmedUI(); 
    <?php endif; ?>

    <?php if (isset($_SESSION['tour_destinations']) && !empty($_SESSION['tour_destinations'])): ?>
        <?php foreach ($_SESSION['tour_destinations'] as $index => $destination): ?>
        restoreDestination(<?php echo json_encode($destination); ?>);
        <?php endforeach; ?>
    <?php endif; ?>
    
    <?php else: ?>
    document.getElementById("addMoreDestinations").style.display = "inline-block";
    document.getElementById("check-btn").style.display = "inline-block";
    document.getElementById("submit-btn").style.display = "none";
    <?php endif; ?>

    minusBtn.addEventListener("click", function () {
        let currentValue = parseInt(counterInput.value);
        if (currentValue > 1) {
            counterInput.value = currentValue - 1;
        }
        updateEstimatedFees();
        updatePeopleCount();
    });

    plusBtn.addEventListener("click", function () {
        let currentValue = parseInt(counterInput.value);
        if (currentValue < 255) {
            counterInput.value = currentValue + 1;
        }
        updateEstimatedFees();
        updatePeopleCount();
    });

    document.getElementById("addMoreDestinations").addEventListener("click", function () {
        window.location.href = "http://localhost/T-VIBES/src/views/frontend/explore.php";
    });

    const style = document.createElement('style');
    style.textContent = `
        .submit-btn {
            border: 2px solid #EC6350;
            background-color: #FFFFFF;
            color: #EC6350;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 50%;
            margin-top: 10px;
            border-radius: 25px;
            margin: auto;
        }
        
        .submit-btn:hover {
            background-color: #EC6350;
            color: #FFFFFF;
        }
    `;
    document.head.appendChild(style);
    
    updateEstimatedFees();
});

function initializeConfirmedUI() {
    updateUIAfterSelection();
    
    let editBtn = document.getElementById("edit-btn");
    if (!editBtn) {
        editBtn = document.createElement("button");
        editBtn.id = "edit-btn";
        editBtn.classList.add("btn", "btn-action");
        editBtn.innerHTML = `<i class="bi bi-pencil-square"></i> Edit`;
        
        editBtn.addEventListener("click", function() {
            switchToEditMode();
        });
        
        document.querySelector(".actions").appendChild(editBtn);
    }
    
    lockTourSelections();
}

function switchToEditMode() {
    previouslySelectedDate = document.getElementById("tour-date").value;
    originalPeopleCount = document.getElementById("counter-input").value;
    
    window.originalSelectedSites = getSelectedSites();
    
    document.getElementById("tour-date").value = "";
    isEditMode = true;
    selectedDate = null;
    
    document.getElementById("submit-btn").style.display = "none";
    
    let selectedDateDisplay = document.getElementById("selected-date-display");
    if (selectedDateDisplay) {
        selectedDateDisplay.style.display = "none";
    }
    
    document.getElementById("edit-btn").remove();
    
    document.getElementById("addMoreDestinations").style.display = "inline-block";
    document.getElementById("check-btn").style.display = "inline-block";
    
    let saveChangesBtn = document.createElement("button");
    saveChangesBtn.id = "save-changes-btn";
    saveChangesBtn.classList.add("btn", "btn-action");
    saveChangesBtn.innerHTML = `Save Changes`;
    
    saveChangesBtn.style.border = "2px solid #EC6350";
    saveChangesBtn.style.color = "#EC6350";
    saveChangesBtn.style.padding = "10px 15px";
    saveChangesBtn.style.cursor = "pointer";
    saveChangesBtn.style.borderRadius = "25px";
    saveChangesBtn.style.fontWeight = "bold";
    saveChangesBtn.style.transition = "all 0.3s ease";
    saveChangesBtn.style.width = "20%";
    saveChangesBtn.style.marginTop = "50px";
    saveChangesBtn.style.margin = "auto";
    saveChangesBtn.style.display = "block";
    
    saveChangesBtn.addEventListener("mouseover", function() {
        this.style.backgroundColor = "#EC6350";
        this.style.color = "#FFFFFF";
    });
    
    saveChangesBtn.addEventListener("mouseout", function() {
        this.style.backgroundColor = "#E7EBEE";
        this.style.color = "#EC6350";
    });
    
    saveChangesBtn.addEventListener("click", function() {
        saveChanges();
    });
    
    document.querySelector(".actions").appendChild(saveChangesBtn);
    
    unlockTourSelections();
    
    fetch("tourrequest.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "edit_mode=true"
    })
    .then(response => response.json());
}

function saveChanges() {
    if (document.querySelectorAll(".destination-wrapper").length === 0) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: "No Destinations Selected",
            text: "Please add at least one destination before saving changes.",
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
    
    let currentDateValue = document.getElementById("tour-date").value;
    
    if (currentDateValue === "" && previouslySelectedDate) {
        currentDateValue = previouslySelectedDate;
    }
    
    let dateUnchanged = (currentDateValue === previouslySelectedDate);
    
    let peopleCount = document.getElementById("counter-input").value;
    
    let peopleCountUnchanged = true;
    if (typeof originalPeopleCount !== 'undefined') {
        peopleCountUnchanged = (parseInt(peopleCount) === parseInt(originalPeopleCount));
    }
    
    const currentSelectedSites = getSelectedSites();
    
    let originalSelectedSites = [];

    try {
        originalSelectedSites = <?php 
            if (isset($_SESSION['tour_destinations'])) {
                $sites = array_map(function($dest) { return (int)$dest['siteid']; }, $_SESSION['tour_destinations']);
                echo json_encode($sites);
            } else {
                echo '[]';
            }
        ?>;
        
        if (typeof window.originalSelectedSites === 'undefined') {
            window.originalSelectedSites = [...originalSelectedSites];
        }
        
        originalSelectedSites = window.originalSelectedSites || originalSelectedSites;
    } catch (e) {
        console.error("Error getting original sites:", e);
    }
    
    let destinationsUnchanged = true;

    const origSites = window.originalSelectedSites || originalSelectedSites;

    if (currentSelectedSites.length !== origSites.length) {
        destinationsUnchanged = false;
    } else {
        const sortedCurrent = [...currentSelectedSites].sort((a, b) => a - b).map(String);
        const sortedOriginal = [...origSites].sort((a, b) => a - b).map(String);
        
        for (let i = 0; i < sortedCurrent.length; i++) {
            if (sortedCurrent[i] !== sortedOriginal[i]) {
                destinationsUnchanged = false;
                break;
            }
        }
    }
    
    if (dateUnchanged && peopleCountUnchanged && destinationsUnchanged) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: "No changes made!",
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup"
            }
        });
        
        isEditMode = false;
        document.getElementById("save-changes-btn").remove();
        document.getElementById("addMoreDestinations").style.display = "none";
        document.getElementById("check-btn").style.display = "none";
        document.getElementById("submit-btn").style.display = "block";
        
        document.getElementById("tour-date").value = previouslySelectedDate;
        
        let selectedDateDisplay = document.getElementById("selected-date-display");
        if (selectedDateDisplay) {
            selectedDateDisplay.style.display = "block";
        }
        
        let editBtn = document.createElement("button");
        editBtn.id = "edit-btn";
        editBtn.classList.add("btn", "btn-action");
        editBtn.innerHTML = `<i class="bi bi-pencil-square"></i> Edit`;
        
        editBtn.addEventListener("click", function() {
            switchToEditMode();
        });
        
        document.querySelector(".actions").appendChild(editBtn);
        
        lockTourSelections();
        
        updateUIAfterSelection(); 
        return;
    }
    
    selectedDate = currentDateValue || previouslySelectedDate;
    document.getElementById("tour-date").value = selectedDate;
    
    const selectedSites = getSelectedSites();
    
    fetch("tourrequest.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "selected_date=" + encodeURIComponent(selectedDate) + 
              "&people_count=" + encodeURIComponent(peopleCount) + 
              "&save_changes=true" + 
              "&update_db=true" + 
              "&destinations=" + encodeURIComponent(JSON.stringify(selectedSites))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            isEditMode = false;
            previouslySelectedDate = selectedDate;
            
            document.getElementById("save-changes-btn").remove();
            document.getElementById("addMoreDestinations").style.display = "none";
            document.getElementById("check-btn").style.display = "none";
            document.getElementById("submit-btn").style.display = "block";
            
            updateUIAfterSelection();
            updateEstimatedFees();
            
            Swal.fire({
                iconHtml: '<i class="fas fa-check-circle"></i>',
                title: "Changes Saved!",
                text: "Your tour request has been updated successfully.",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom", 
                    popup: "swal-custom-popup"
                }
            });
        } else {
            Swal.fire({
                iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                title: "Error!",
                text: data.message || "Failed to save changes. Please try again.",
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
            title: "Error!",
            text: "An unexpected error occurred. Please try again.",
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

function confirmRemoveDestination(siteid) {
    Swal.fire({
        title: "Delete This Destination?",
        text: "Are you sure you want to remove this destination?",
        iconHtml: '<i class="fas fa-trash-alt"></i>',
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
            removeDestination(siteid);
        }
    });
}

function removeDestination(siteid) {
    // Find the destination element by siteid
    const destinationElement = document.querySelector(`.destination-wrapper[data-siteid="${siteid}"]`);
    if (destinationElement) {
        destinationElement.remove();
        updateDestinationIndexes();
        updateEstimatedFees();
        
        // Update sitesOpDays by removing the deleted site
        sitesOpDays = sitesOpDays.filter(site => site.siteid != siteid);
        
        // Recalculate common available days
        const commonAvailableDays = calculateCommonAvailableDays(sitesOpDays);
        
        // Update the data attribute
        const dateInput = document.getElementById("tour-date");
        dateInput.setAttribute('data-availabledate', commonAvailableDays);
                
        if (document.querySelectorAll(".destination-wrapper").length === 0) {
            if (isEditMode) {
                resetToInitialState();
            }
        }
    }
    
    fetch("tourrequest.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "remove_siteid=" + siteid
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: "Destination Deleted Successfully!",
                iconHtml: '<i class="fas fa-check-circle"></i>',
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            }).then(() => {
                if (!document.getElementById("edit-btn")) {
                    window.location.reload(); 
                }
            });
        } else {
            console.error("Server error removing destination:", data.message);
        }
    })
    .catch(error => {
        console.error("Fetch error:", error);
    });
}
function resetToInitialState() {
    const saveChangesBtn = document.getElementById("save-changes-btn");
    if (saveChangesBtn) {
        saveChangesBtn.remove();
    }
    
    const editBtn = document.getElementById("edit-btn");
    if (editBtn) {
        editBtn.remove();
    }
    
    document.getElementById("submit-btn").style.display = "none";
    
    document.getElementById("addMoreDestinations").style.display = "inline-block";
    document.getElementById("check-btn").style.display = "inline-block";
    
    document.getElementById("tour-date").value = "";
    selectedDate = null;
    
    let selectedDateDisplay = document.getElementById("selected-date-display");
    if (selectedDateDisplay) {
        selectedDateDisplay.style.display = "none";
    }
    
    isEditMode = false;
    
    const selectedSites = getSelectedSites();
    
    fetch("tourrequest.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "selected_date=" + encodeURIComponent(previouslySelectedDate || "") + 
              "&people_count=" + encodeURIComponent(document.getElementById("counter-input").value) + 
              "&save_changes=true" + 
              "&update_db=true" + 
              "&destinations=" + encodeURIComponent(JSON.stringify([]))
    })
    .then(response => response.json())
    .then(data => {
        fetch("tourrequest.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "reset_ui=true"
        })
        .then(response => response.json())
        .catch(error => {
            console.error("Error resetting UI state:", error);
        });
        
        Swal.fire({
            iconHtml: '<i class="fas fa-check-circle"></i>',
            title: "Tour Request Deleted",
            text: "Your tour request has been removed from the system.",
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup"
            }
        }).then(() => {
            window.location.reload();
        });
    })
    .catch(error => {
        console.error("Error resetting tour request in database:", error);
    });
    
    updateEstimatedFees();
}

function updateDestinationIndexes() {
    document.querySelectorAll(".destination-wrapper").forEach((element, index) => {
        const siteId = element.getAttribute("data-siteid") || element.getAttribute("data-index");
        
        if (!element.hasAttribute("data-siteid")) {
            element.setAttribute("data-siteid", siteId);
        }
        
        element.setAttribute("data-index", siteId);
        
        const deleteBtn = element.querySelector(".delete-btn");
        if (deleteBtn) {
            deleteBtn.onclick = function() {
                confirmRemoveDestination(siteId);
            };
        }
    });
}

let originalUpdateUIAfterSelection = updateUIAfterSelection;
updateUIAfterSelection = function() {
    originalUpdateUIAfterSelection();
    updateEstimatedFees();
}

let currentDate = new Date();
let siteAvailability = {}; 
let selectedDate = null;
let previouslySelectedDate = null;
let isEditMode = false;
let originalPeopleCount = null; 

async function fetchAvailability() {
    try {
        const response = await fetch("tourrequest.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" }, // Changed from application/json
            body: "action=get_availability&sites=" + JSON.stringify(getSelectedSites())
        });

        // Get text first to inspect what's being returned
        const text = await response.text();
        
        
        
        // Try to parse if it looks like JSON
        let data;
        try {
            data = JSON.parse(text);
            
            if (data.success) {
                let modifiedAvailability = {};
                for (let date in data.availability) {
                    if (data.availability[date] === "unavailable") {
                        modifiedAvailability[date] = "unavailable";
                    }
                }
                siteAvailability = modifiedAvailability;
                renderCalendar();
            } else {
                console.error("Error fetching availability:", data.message);
            }
        } catch (parseError) {
            console.error("Error parsing JSON response:", parseError);
            console.error("Response wasn't valid JSON");
        }
    } catch (error) {
        console.error("Request failed:", error);
    }
}

function getSelectedSites() {
    let selectedSites = [];
    document.querySelectorAll(".destination-wrapper").forEach(site => {
        const siteId = parseInt(site.getAttribute("data-siteid") || site.getAttribute("data-index"), 10);
        if (!isNaN(siteId)) {
            selectedSites.push(siteId);
        }
    });
    return selectedSites;
}

function renderCalendar() {
    const daysContainer = document.getElementById("calendar-days");
    const monthSelect = document.getElementById("month-select");
    const yearSelect = document.getElementById("year-select");
    let year = currentDate.getFullYear();
    let month = currentDate.getMonth();
    monthSelect.value = month;
    yearSelect.value = year;

    // Get the binary representation of available days
    const commonAvailableDays = calculateCommonAvailableDays(sitesOpDays);
    
    // Convert to array of bits for easier access - reading from left to right
    // Left to right: Sun(0), Mon(1), Tue(2), Wed(3), Thu(4), Fri(5), Sat(6)
    const daysAvailable = commonAvailableDays.padStart(7, '0').split('').map(bit => bit === '1');
    
    let firstDay = new Date(year, month, 1).getDay(); // 0 is Sunday, 6 is Saturday
    let lastDate = new Date(year, month + 1, 0).getDate();
    let today = new Date();
    let days = "";

    // Add empty cells for days before the first day of the month
    for (let x = 0; x < firstDay; x++) {
        days += `<span class="prev-month disabled"></span>`;
    }

    for (let i = 1; i <= lastDate; i++) {
        let dateStr = `${year}-${(month + 1).toString().padStart(2, "0")}-${i.toString().padStart(2, "0")}`;
        let className = "available";
        
        // Check site-specific availability
        if (siteAvailability[dateStr]) {
            className = siteAvailability[dateStr] === "unavailable" ? "disabled" : "available";
        }
        
        // Check if this day of the week is available according to binary opdays
        const date = new Date(year, month, i);
        const dayOfWeek = date.getDay(); // 0 is Sunday, 6 is Saturday
        
        // For binary read left to right (Sun=0, Mon=1, etc.), use dayOfWeek directly as index
        if (!daysAvailable[dayOfWeek]) {
            className = "disabled";
        }
        
        let isSelected = (isEditMode && dateStr === previouslySelectedDate);
        
        if (isSelected) {
            className += " selected";
            selectedDate = dateStr; 
        }
        
        // Also disable past dates
        let isPast = year < today.getFullYear() || 
                    (year === today.getFullYear() && month < today.getMonth()) || 
                    (year === today.getFullYear() && month === today.getMonth() && i < today.getDate());
        
        days += `<span class="${isPast ? 'disabled' : className}" data-date="${dateStr}" onclick="selectDate('${dateStr}', this)">${i}</span>`;
    }
    daysContainer.innerHTML = days;
    
    // Update the day legend
    updateDayLegend(daysAvailable);
}

function updateDayLegend(daysAvailable) {
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const legendContainer = document.querySelector('.legend');
    
    // Clear existing legend
    legendContainer.innerHTML = '';
    
    // Add legend for available days
    const legendText = document.createElement('span');
    legendText.innerHTML = '<span class="legend-item available"></span> Available on: ';
    legendContainer.appendChild(legendText);
    
    // Create span for each available day - reading left to right
    const availableDays = [];
    for (let i = 0; i < 7; i++) {
        if (daysAvailable[i]) {
            availableDays.push(dayNames[i]);
        }
    }
    
    if (availableDays.length > 0) {
        const daysText = document.createElement('span');
        daysText.textContent = availableDays.join(', ');
        daysText.style.fontWeight = 'bold';
        daysText.style.marginLeft = '5px';
        legendContainer.appendChild(daysText);
    } else {
        const noDaysText = document.createElement('span');
        noDaysText.textContent = 'None (please modify your selections)';
        noDaysText.style.color = '#A9221C';
        noDaysText.style.fontWeight = 'bold';
        noDaysText.style.marginLeft = '5px';
        legendContainer.appendChild(noDaysText);
    }
}

function updateDayLegend(daysAvailable) {
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const legendContainer = document.querySelector('.legend');
    
    // Clear existing legend
    legendContainer.innerHTML = '';
    
    // Add legend for available days
    const legendText = document.createElement('span');
    legendText.innerHTML = '<span class="legend-item available"></span> Available on: ';
    legendContainer.appendChild(legendText);
    
    // Create span for each available day
    const availableDays = [];
    for (let i = 0; i < 7; i++) {
        if (daysAvailable[6 - i]) {
            availableDays.push(dayNames[i]);
        }
    }
    
    if (availableDays.length > 0) {
        const daysText = document.createElement('span');
        daysText.textContent = availableDays.join(', ');
        daysText.style.fontWeight = 'bold';
        daysText.style.marginLeft = '5px';
        legendContainer.appendChild(daysText);
    } else {
        const noDaysText = document.createElement('span');
        noDaysText.textContent = 'None (please modify your selections)';
        noDaysText.style.color = '#A9221C';
        noDaysText.style.fontWeight = 'bold';
        noDaysText.style.marginLeft = '5px';
        legendContainer.appendChild(noDaysText);
    }
}
window.selectDate = function (dateStr, element) {
    if (element.classList.contains("disabled")) return;

    document.querySelectorAll(".days span").forEach(span => {
        span.classList.remove("selected");
    });

    element.classList.add("selected");
    selectedDate = dateStr;
    
    document.getElementById("tour-date").value = dateStr;
};

function prevMonth() {
    if (currentDate.getMonth() > new Date().getMonth() || currentDate.getFullYear() > new Date().getFullYear()) {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    }
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

function changeMonth() {
    currentDate.setMonth(parseInt(document.getElementById("month-select").value));
    renderCalendar();
}

function changeYear() {
    currentDate.setFullYear(parseInt(document.getElementById("year-select").value));
    renderCalendar();
}

function populateYearOptions() {
    const yearSelect = document.getElementById("year-select");
    const currentYear = new Date().getFullYear();
    yearSelect.innerHTML = "";

    for (let i = currentYear; i <= currentYear + 5; i++) {
        let option = document.createElement("option");
        option.value = i;
        option.textContent = i;
        if (i === currentYear) {
            option.selected = true;  
        }
        yearSelect.appendChild(option);
    }
}
function verifyAvailableDate() {
    // Recalculate common available days
    const commonAvailableDays = calculateCommonAvailableDays(sitesOpDays);
    
    // Update the data attribute
    const dateInput = document.getElementById("tour-date");
    dateInput.setAttribute('data-availabledate', commonAvailableDays);
    
    // Check if there are common days available
    const hasCommonDays = parseInt(commonAvailableDays, 2) > 0;

    
    if (!hasCommonDays) {
        // No common days available
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: 'No Common Available Days',
            html: `
                <div class="text-start mt-3">
                    <p>The destinations you've selected don't have any common available days.</p>
                    <p>Options:</p>
                    <ul>
                        <li>Remove some destinations</li>
                        <li>Choose different destinations with compatible schedules</li>
                    </ul>
                </div>
            `,
            confirmButtonText: "Understand",
            customClass: {
                title: "swal2-title-custom",
                icon: "swal2-icon-custom",
                popup: "swal-custom-popup",
                confirmButton: "swal-custom-btn",
                cancelButton: "swal-custom-btn"
            }
        });
        return false;
    }
    
    // If we have common days, show the date input
    showModal();
    return true;
}

function showModal() {

    if (document.querySelectorAll(".destination-wrapper").length === 0) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: "No Destinations Selected",
            text: "Please add at least one destination before checking availability.",
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

        let modal = document.getElementById("availabilityModal");
        modal.style.display = "block";
        setTimeout(() => {
            modal.classList.add("show");
        }, 10);

        currentDate = new Date();
        populateYearOptions();

        document.getElementById("month-select").value = currentDate.getMonth();
        document.getElementById("year-select").value = currentDate.getFullYear();

        renderCalendar();
        fetchAvailability();
}

function closeModal() {
    let modal = document.getElementById("availabilityModal");
    modal.classList.remove("show");
    setTimeout(() => {
        modal.style.display = "none";
    }, 300);
    
    if (!isEditMode && selectedDate && document.getElementById("tour-date").value) {
        lockTourSelections();
    }
}

function calculateCommonAvailableDays(sitesOpDays) {
    if (!sitesOpDays || sitesOpDays.length === 0) {
        return '0000000';
    }
    
    // Start with all bits set (7 days available)
    let commonDays = 127; // 1111111 in binary
    
    // Perform binary AND on all sites' opdays
    sitesOpDays.forEach(site => {
        // Convert to a number if it's a string
        const opdays = typeof site.opdays === 'string' ? parseInt(site.opdays, 2) : site.opdays;
        commonDays &= opdays;
    });
    
    // Convert to 7-digit binary string (padded with leading zeros)
    return commonDays.toString(2).padStart(7, '0');
}

// Pass PHP site opdays data to JavaScript
let sitesOpDays = <?php echo json_encode($sitesOpDays); ?>;

document.addEventListener("DOMContentLoaded", function() {
    // Calculate common available days on page load
    const commonAvailableDays = calculateCommonAvailableDays(sitesOpDays);
    
    // Set the data-availabledate attribute
    const dateInput = document.getElementById("tour-date");
    if (dateInput) {
        dateInput.setAttribute('data-availabledate', commonAvailableDays);
    }
});

document.querySelector(".modal-footer .btn").addEventListener("click", function () {
    if (!selectedDate) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: "Please select a date first!",
            text: "Check availability to select a date for your tour.",
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
    
    if (isEditMode && selectedDate === previouslySelectedDate) {
        Swal.fire({
            iconHtml: '<i class="fas fa-exclamation-circle"></i>',
            title: "No changes made!",
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

    if (isEditMode) {
        document.getElementById("tour-date").value = selectedDate;
        closeModal();
    } else {
        fetch("tourrequest.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "selected_date=" + selectedDate + "&create_request=true"
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById("tour-date").value = selectedDate;
                isEditMode = false;
                previouslySelectedDate = selectedDate;
                closeModal();
                updateUIAfterSelection();
                updateEstimatedFees();
                
                Swal.fire({
                    iconHtml: '<i class="fas fa-check-circle"></i>',
                    title: "Tour Request Created!",
                    text: "Your tour request has been created successfully. Click 'Submit Request' to finalize it.",
                    timer: 3000,
                    showConfirmButton: false,
                    customClass: {
                        title: "swal2-title-custom",
                        icon: "swal2-icon-custom", 
                        popup: "swal-custom-popup"
                    }
                });
            }
        });
    }
});

function updateUIAfterSelection() {
    document.getElementById("addMoreDestinations").style.display = "none";
    document.getElementById("check-btn").style.display = "none";
    
    document.getElementById("submit-btn").style.display = "block";
    
    let dateValue = document.getElementById("tour-date").value;
    if (!dateValue || dateValue.trim() === '') {
        if (selectedDate && selectedDate.trim() !== '') {
            dateValue = selectedDate;
        } else if (previouslySelectedDate && previouslySelectedDate.trim() !== '') {
            dateValue = previouslySelectedDate;
            document.getElementById("tour-date").value = previouslySelectedDate; 
        }
    }
    
    let formattedDate = "No date selected";
    
    if (dateValue && dateValue.trim() !== '') {
        try {
            const dateObj = new Date(dateValue);
            if (!isNaN(dateObj.getTime())) {
                formattedDate = dateObj.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            } else {
                console.error("Invalid date value:", dateValue);
                formattedDate = "Please select a valid date";
            }
        } catch (e) {
            console.error("Error formatting date:", e);
            formattedDate = "Date format error";
        }
    }
    
    let selectedDateDisplay = document.getElementById("selected-date-display");
    if (!selectedDateDisplay) {
        selectedDateDisplay = document.createElement("div");
        selectedDateDisplay.id = "selected-date-display";
        selectedDateDisplay.className = "selected-date-container";
        
        const tourDateInput = document.getElementById("tour-date");
        tourDateInput.parentNode.insertBefore(selectedDateDisplay, document.querySelector(".actions"));
    } else {
        selectedDateDisplay.style.display = "block";
    }
    
    selectedDateDisplay.innerHTML = `
        <div class="selected-date-label">Selected Date:</div>
        <div class="selected-date-value">${formattedDate}</div>
    `;
    
    let editBtn = document.getElementById("edit-btn");
    if (!editBtn) {
        editBtn = document.createElement("button");
        editBtn.id = "edit-btn";
        editBtn.classList.add("btn", "btn-action");
        editBtn.innerHTML = `<i class="bi bi-pencil-square"></i> Edit`;
        
        editBtn.addEventListener("click", function() {
            switchToEditMode();
        });
        
        document.querySelector(".actions").appendChild(editBtn);
    }
    
    document.getElementById("tour-date").style.display = "none";
    
    lockTourSelections();
}

function updateEstimatedFees() { 
    const destinationWrappers = document.querySelectorAll(".destination-wrapper"); 
    const peopleCount = parseInt(document.getElementById("counter-input").value); 
    const estimatedFeesContainer = document.querySelector(".estimated-fees div"); 
    const estimatedFeesTotal = document.getElementById("estimatedFees"); 
     
    let totalPrice = 0; 
     
    if (destinationWrappers.length > 0) { 
        estimatedFeesContainer.innerHTML = ''; 
        
        destinationWrappers.forEach(destination => { 
            const destinationName = destination.querySelector(".destination-details span").textContent; 
            const destinationPrice = parseFloat(destination.getAttribute("data-price")); 
             
            const destinationEntry = document.createElement("div"); 
            destinationEntry.className = "destination-fee-item"; 
            destinationEntry.style.display = "flex"; 
            destinationEntry.style.justifyContent = "space-between"; 
            destinationEntry.innerHTML = ` 
                <span>${destinationName}</span> 
                <span style="text-align: right; font-weight: bold;">₱ ${destinationPrice.toFixed(2)}</span> 
            `; 
            estimatedFeesContainer.appendChild(destinationEntry); 
             
            totalPrice += destinationPrice; 
        }); 
         
        const peopleEntry = document.createElement("div"); 
        peopleEntry.className = "people-fee-item"; 
        peopleEntry.style.display = "flex"; 
        peopleEntry.style.justifyContent = "space-between"; 
        peopleEntry.innerHTML = ` 
            <span>Number of People:</span> 
            <span style="text-align: right; font-weight: bold;">${peopleCount}</span> 
        `; 
        estimatedFeesContainer.appendChild(peopleEntry); 
         
        totalPrice = totalPrice * peopleCount; 
         
        const separator = document.createElement("hr"); 
        separator.style.margin = "10px 0"; 
        estimatedFeesContainer.appendChild(separator); 

        const totalPriceEntry = document.createElement("div"); 
        totalPriceEntry.className = "total-price-item"; 
        totalPriceEntry.style.display = "flex"; 
        totalPriceEntry.style.justifyContent = "space-between"; 
        totalPriceEntry.style.fontSize = "1.2rem"; 
        totalPriceEntry.style.fontWeight = "bold"; 
        totalPriceEntry.innerHTML = ` 
            <span style="color: #102E47; font-family: 'Raleway', sans-serif !important;">Total Price:</span> 
            <span style="text-align: right; color: #EC6350; font-weight: bold;">₱ ${totalPrice.toFixed(2)}</span> 
        `; 
        estimatedFeesContainer.appendChild(totalPriceEntry); 

    } else { 
        estimatedFeesContainer.innerHTML = '<p>No destinations added yet.</p>'; 
        estimatedFeesTotal.textContent = '₱ 0.00'; 
    } 
}

function lockTourSelections() {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    deleteButtons.forEach(button => {
        button.disabled = true;
        button.style.opacity = 0.5;
        button.style.cursor = "not-allowed";
        button.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
 
            Swal.fire({
                iconHtml: '<i class="fas fa-lock"></i>',
                title: "Locked Selection",
                text: "You cannot remove destinations after date confirmation. Please click Edit to make changes.",
                timer: 3000,
                showConfirmButton: false,
                customClass: {
                    title: "swal2-title-custom",
                    icon: "swal2-icon-custom",
                    popup: "swal-custom-popup"
                }
            });
            
            return false;
        };
    });
 
    const minusBtn = document.getElementById("minus-btn");
    const plusBtn = document.getElementById("plus-btn");
    const counterInput = document.getElementById("counter-input");
 
    minusBtn.disabled = true;
    plusBtn.disabled = true;
    counterInput.readOnly = true;
 
    minusBtn.style.opacity = 0.5;
    plusBtn.style.opacity = 0.5;
    counterInput.style.opacity = 0.8;
    minusBtn.style.cursor = "not-allowed";
    plusBtn.style.cursor = "not-allowed";
}

function unlockTourSelections() {
    const deleteButtons = document.querySelectorAll(".delete-btn");
    deleteButtons.forEach(button => {
        button.disabled = false;
        button.style.opacity = 1;
        button.style.cursor = "pointer";
        
        // Get the parent wrapper and use data-siteid attribute
        const wrapper = button.closest(".destination-wrapper");
        const siteid = wrapper.getAttribute("data-siteid");
        
        // Set the correct click handler with the proper siteid
        button.onclick = function() {
            confirmRemoveDestination(siteid);
        };
    });
    
    // Rest of the function remains the same
    const minusBtn = document.getElementById("minus-btn");
    const plusBtn = document.getElementById("plus-btn");
    const counterInput = document.getElementById("counter-input");
 
    minusBtn.disabled = false;
    plusBtn.disabled = false;
    counterInput.readOnly = false;
 
    minusBtn.style.opacity = 1;
    plusBtn.style.opacity = 1;
    counterInput.style.opacity = 1;
    minusBtn.style.cursor = "pointer";
    plusBtn.style.cursor = "pointer";
}

function updatePeopleCount() {
    let peopleCount = document.getElementById("counter-input").value;
    fetch("tourrequest.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "people_count=" + peopleCount
    })
    .then(response => response.json());
}

function restoreDestination(destinationData) {
    const destinationsContainer = document.querySelector(".destinations-container");
    if (!destinationsContainer) {
        console.error("Destinations container not found");
        return;
    }
    
    const destinationWrapper = document.createElement("div");
    destinationWrapper.className = "destination-wrapper";
    destinationWrapper.setAttribute("data-index", destinationData.siteid);
    destinationWrapper.setAttribute("data-siteid", destinationData.siteid);
    destinationWrapper.setAttribute("data-price", destinationData.price);
    
    destinationWrapper.innerHTML = `
        <div class="destination-image">
            <img src="${destinationData.siteimage}" alt="${destinationData.sitename}">
        </div>
        <div class="destination-details">
            <span>${destinationData.sitename}</span>
            <div class="destination-price">₱ ${parseFloat(destinationData.price).toFixed(2)}</div>
        </div>
        <div class="destination-actions">
            <button class="delete-btn" onclick="confirmRemoveDestination(${destinationData.siteid})">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    `;
    
    destinationsContainer.appendChild(destinationWrapper);
    updateEstimatedFees();
}

document.getElementById("submit-btn").addEventListener("click", function() {
    Swal.fire({
        iconHtml: '<i class="fas fa-thumbs-up"></i>',
        title: "Submit Request?",
        text: "Are you sure you want to submit your tour request?",
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
            fetch("tourrequest.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "action=submit_request"
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-circle-check"></i>',
                        title: "Your reservation request has been submitted and is awaiting review.",
                        text: "Please wait for confirmation.",
                        timer: 3000,
                        showConfirmButton: false,
                        customClass: {
                            title: "swal2-title-custom",
                            icon: "swal2-icon-custom",
                            popup: "swal-custom-popup"
                        }
                    }).then(() => {
                        window.location.reload(); 
                    });
                } else {
                    Swal.fire({
                        iconHtml: '<i class="fas fa-exclamation-circle"></i>',
                        title: "Submission Failed",
                        text: data.message || "Something went wrong. Please try again.",
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
    });
});
</script>
</body>
</html>