<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
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
            <li><a class="active" href="#home"><i class="bi bi-grid-fill"></i>Overview</a></li>
            <li><a href="#requests"><i class="bi bi-map"></i>Tour Requests</a></li>
            <li><a href="#tours"><i class="bi bi-geo"></i>Upcoming Tours</a></li>
            <li><a href="#reviews"><i class="bi bi-pencil-square"></i>Reviews</a></li>
            <li><a href="#sites"><i class="bi bi-image"></i>Tourist Sites</a></li>
        </ul> 
        <div class="accountcontainer">
            <ul>
                <li><button><i class="bi bi-arrow-left-square-fill"></i>Log out</button></li>
            </ul>
        </div>
    </div>
    <div class="dashboardcontainer">
        <div class="content">
            <h1>Welcome, Employee!</h1>
            <p>This is your dashboard. You can view your tasks, schedule, and other important information here.</p>
        </div>
    </div>
</body>
</html>