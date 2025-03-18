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
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .tour-row:hover {
            background-color: #D9E2EC;
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

        .status-pending {
            color: #E74C3C;
            font-weight: bold;
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

    <?php foreach ($tours as $index => $tour) : ?>
        <div class="row align-items-center tour-row" data-bs-toggle="modal" data-bs-target="#tourModal<?= $index ?>">
            <div class="col-3"><?= $tour['created'] ?></div>
            <div class="col-3"><?= $tour['planned'] ?></div>
            <div class="col-2"><?= $tour['people'] ?></div>
            <div class="col-4">
                <?php foreach ($tour['destinations'] as $destination) : ?>
                    <div><?= $destination ?></div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="tourModal<?= $index ?>" tabindex="-1" aria-labelledby="modalLabel<?= $index ?>" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content p-4">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabel<?= $index ?>">Tour Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php foreach ($tour['destinations'] as $i => $destination) : ?>
                            <div class="d-flex align-items-center mb-3" style="background-color: #EDF1F5; padding: 12px; border-radius: 8px;">
                                <div class="me-3">
                                    <div style="width: 36px; height: 36px; background-color: #fff; border: 2px solid #102E47; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                        <?= $i + 1 ?>
                                    </div>
                                </div>
                                <div>
                                    <strong><?= $destination ?></strong>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div class="row mt-4">
                            <div class="col-6">
                                <strong>Date Created:</strong>
                                <div><?= $tour['created'] ?></div>
                            </div>
                            <div class="col-6">
                                <strong>Number of People:</strong>
                                <div><?= $tour['people'] ?></div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <strong>Estimated Fees:</strong>
                                <?php foreach ($tour['destinations'] as $destination) : ?>
                                    <div><?= $destination ?> <span class="text-muted">x2</span></div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-6">
                                <strong>Total:</strong>
                                <div>₱ 0.00</div>
                            </div>
                        </div>

                        <div class="mt-4 status-pending">
                            Status: Pending
                        </div>
                    </div>
                </div>
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
