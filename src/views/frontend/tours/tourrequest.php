
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour Requests</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            padding: 20px;
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
            color: #102E47;
        }
        .people-counter {
            margin-top: 20px;
            text-align: center;
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
        }
        .counter-btn:hover {
            background-color: #8a1b16;
        }
        #tour-date {
            display: none;
            margin-top: 12px;
        }
        .estimated-fees-container {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 30%;
            align-self: flex-start;
        }
        .estimated-fees {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: left;
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
    </style>
</head>
<body>
<?php include '../../templates/headertours.php'; ?>
    <?php include '../../templates/toursnav.php'; ?>

<div class="main-container">
    <!-- Tour Container -->
    <div class="tour-container">
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="destination-wrapper" data-price="<?= $i * 100 ?>">
                <div class="destination-number"><?= $i ?></div>
                <div class="destination-item">
                    <div class="destination-image"><i class="bi bi-image"></i></div>
                    <div class="destination-details">
                        <span>Destination Name <?= $i ?></span>
                    </div>
                    <div class="destination-price">₱ <?= $i * 100 ?>.00</div>
                </div>
            </div>
        <?php endfor; ?>

        <!-- People Counter -->
        <div class="people-counter">
            <label>How Many People?</label>
            <button class="counter-btn" id="minus-btn">-</button>
            <input type="number" id="counter-input" value="1" min="1" max="255" style="width: 40px; text-align: center;">
            <button class="counter-btn" id="plus-btn">+</button>
        </div>

        <!-- Date Picker -->
        <input type="date" class="form-control" id="tour-date">

        <div class="mt-3 text-center">
            <button class="btn btn-danger" id="check-btn">Check Availability</button>
            <button class="btn btn-secondary" id="add-btn">Add More Destinations</button>
            <button class="edit-btn" id="edit-btn">Edit</button>
        </div>
    </div>

    <!-- Estimated Fees -->
    <div class="estimated-fees-container">
        <div class="estimated-fees">
            <h4>Estimated Fees</h4>
            <div id="estimated-fees"></div>
            <div>Total: ₱<span id="total-cost">0.00</span></div>
            <button class="submit-btn" id="submit-btn">Submit Request</button>
        </div>
    </div>
</div>
<script>
    const dateInput = document.getElementById('tour-date');
    const checkBtn = document.getElementById('check-btn');
    const addBtn = document.getElementById('add-btn');
    const editBtn = document.getElementById('edit-btn');
    const submitBtn = document.getElementById('submit-btn');
    const feesList = document.getElementById('estimated-fees');
    const totalCostDisplay = document.getElementById('total-cost');

    const minusBtn = document.getElementById('minus-btn');
    const plusBtn = document.getElementById('plus-btn');
    const counterInput = document.getElementById('counter-input');

    // Counter functionality
    minusBtn.addEventListener('click', () => {
        let value = parseInt(counterInput.value);
        if (value > 1) {
            counterInput.value = value - 1;
            updateTotalCost();
        }
    });

    plusBtn.addEventListener('click', () => {
        let value = parseInt(counterInput.value);
        if (value < 255) {
            counterInput.value = value + 1;
            updateTotalCost();
        }
    });

    // Update total cost based on people count
    function updateTotalCost() {
        let totalCost = 0;
        let peopleCount = parseInt(counterInput.value);

        feesList.innerHTML = '';
        document.querySelectorAll('.destination-wrapper').forEach(dest => {
            const name = dest.querySelector('.destination-details span').innerText;
            const price = parseFloat(dest.getAttribute('data-price'));
            feesList.innerHTML += `<div>${name} x${peopleCount} - ₱${(price * peopleCount).toFixed(2)}</div>`;
            totalCost += price * peopleCount;
        });

        totalCostDisplay.textContent = totalCost.toFixed(2);
    }

    checkBtn.addEventListener('click', () => {
        dateInput.style.display = 'block';
    });

    dateInput.addEventListener('change', () => {
        updateTotalCost();
        submitBtn.style.display = 'block';

        // Hide buttons, show edit button
        checkBtn.style.display = 'none';
        addBtn.style.display = 'none';
        editBtn.style.display = 'block';
    });

    editBtn.addEventListener('click', () => {
        checkBtn.style.display = 'block';
        addBtn.style.display = 'block';
        dateInput.style.display = 'none';
        submitBtn.style.display = 'none';
        editBtn.style.display = 'none';
    });

    // Ensure total updates when manually changing the input value
    counterInput.addEventListener('change', updateTotalCost);
</script>

<!--Sweetalerts-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('submit-btn').addEventListener('click', () => {
        Swal.fire({
            icon: 'success',
            html: `
                <div style="font-size: 18px; font-weight: bold; margin-top: 10px; color: #102E47;">
                    Your reservation request has been submitted and is awaiting review.<br>
                    Please wait for confirmation.
                </div>
            `,
            showConfirmButton: false,
            timer: 3000 // Closes after 3 seconds
        });
    });
</script>
</body>
</html>
