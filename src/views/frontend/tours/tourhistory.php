
<?php
// Sample data for demonstration
$tours = [
    ['created' => 'DD M YYYY', 'planned' => 'DD M YYYY', 'people' => 2, 'destinations' => ['Destination Name']],
    ['created' => 'DD M YYYY', 'planned' => 'DD M YYYY', 'people' => 2, 'destinations' => ['Destination Name', 'Destination Name']],
    ['created' => 'DD M YYYY', 'planned' => 'DD M YYYY', 'people' => 2, 'destinations' => ['Destination Name']],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Requests</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #333;
        }

        .table-container {
            margin-top: 20px;
        }

        .table-header {
            font-weight: bold;
            color: #102E47;
        }

        .tour-row {
            background-color: #EDF1F5;
            border-radius: 12px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .load-more-btn {
            background-color: white;
            color: #102E47;
            border: 2px solid #A9BCC9;
            border-radius: 30px;
            padding: 8px 24px;
            font-weight: bold;
            transition: 0.3s;
        }

        .load-more-btn:hover {
            background-color: #D9E2EC;
            color: #102E47;
        }
    </style>
</head>
<body>
<?php include '../../templates/headertours.php'; ?>
    <?php include '../../templates/toursnav.php'; ?>

<div class="container table-container">
    <div class="row table-header py-2">
        <div class="col-3">Date Created</div>
        <div class="col-3">Planned Date/s</div>
        <div class="col-2">Number of People</div>
        <div class="col-4">Destinations</div>
    </div>

    <?php foreach ($tours as $tour) : ?>
        <div class="row align-items-center tour-row">
            <div class="col-3"><?= $tour['created'] ?></div>
            <div class="col-3"><?= $tour['planned'] ?></div>
            <div class="col-2"><?= $tour['people'] ?></div>
            <div class="col-4">
                <?php foreach ($tour['destinations'] as $destination) : ?>
                    <div><?= $destination ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="text-center mt-4">
        <button class="load-more-btn">Load More</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
