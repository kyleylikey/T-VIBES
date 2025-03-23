<?php
session_start();

require_once '../../../controllers/tourist/tourrequestcontroller.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tour Requests</title>
    <link rel="stylesheet" href="../../../../public/assets/styles/index.css">
	<link rel="stylesheet" href="../../../../public/assets/styles/main.css">
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
        <?php $i=1; foreach ($userTourRequest as $request):?>
			<div class="destination-wrapper" data-index="<?= $i ?>" data-price=" <?php echo $request['price'];?>">
                <div class="destination-number"><?= $i ?></div>
                <div class="destination-item">
                    <img src="../../../../public/uploads/<?= $request['siteimage'] ?>" alt="Destination Image" class="destination-image"></img>
                    <div class="destination-details">
                        <span><?php echo $request['sitename'];?></span>
                    </div>
                    <div class="destination-actions">
                        <span class="destination-price">₱ <?php echo $request['price'];?></span>
                        <button class="delete-btn" onclick="deleteDestination(this)">
                            <i class="text-white bi bi-trash-fill"></i>
                        </button>
                    </div>
                </div>
            </div>

        <?php
        $i++;
        endforeach;?>

        <!-- People Counter -->
        <div class="people-counter">
            <label>How Many People?</label>
            <button class="counter-btn" id="minus-btn">-</button>
            <input type="number" id="counter-input" value="1" min="1" max="255" style="width: 40px; text-align: center;">
            <button class="counter-btn" id="plus-btn">+</button>
        </div>

        <!-- Date Picker -->
        <input type="date" class="form-control" id="tour-date" data-availabledate="<?php echo $getDate['all_opdays_and_binary']; ?>">

		<div class="actions text-center mt-3">
    <button id="addMoreDestinations" class="btn btn-pill" style="background-color: #EC6350; color: #fff; border-radius: 50px; padding: 10px 24px;">
        Add More Destinations
    </button>
    <button id="check-btn" class="btn btn-pill" style="background-color: #EC6350; color: #fff; border-radius: 50px; padding: 10px 24px;" onclick="verifyAvailableDate()">
        Check Availability
    </button>
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

<!--Delete Button-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function deleteDestination(button) {
        Swal.fire({
            iconHtml: '<i class="bi bi-trash" style="color: #EC6350; font-size: 48px;"></i>',
            title: '<span style="font-size: 20px; color: #102E47;">Delete This Destination?</span>',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'swal-btn-confirm',
                cancelButton: 'swal-btn-cancel'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const destination = button.closest('.destination-wrapper');
                destination.remove(); // Remove from DOM

                // Reorder the numbering after deletion
                document.querySelectorAll('.destination-wrapper').forEach((item, index) => {
                    item.querySelector('.destination-number').innerText = index + 1;
                });

                updateTotalCost(); // Update the total fees

                // Success alert
                Swal.fire({
                    icon: 'success',
                    title: 'Successfully Removed!',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    }
</script>


<!--Sweetalerts-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function verifyAvailableDate() {
    // Get the binary representation of available days
    const availableDateBinary = dateInput.getAttribute('data-availabledate');
    
    // Check if the binary value is 0 or invalid
    const hasCommonDays = availableDateBinary && parseInt(availableDateBinary, 2) > 0;
    
    if (!hasCommonDays) {
        // No common days available
        Swal.fire({
            icon: 'error',
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
            confirmButtonText: 'Understand',
            confirmButtonColor: '#EC6350'
        });
        return false;
    }
    
    // If we have common days, show the date input
    dateInput.style.display = 'block';
    return true;
}

// Update the check button click handler
checkBtn.addEventListener('click', () => {
    // Verify if there are common available days first
    if (verifyAvailableDate()) {
        // Only show date picker if verification passes
        dateInput.style.display = 'block';
    }
});

// Run verification when page loads if tours are already selected
document.addEventListener('DOMContentLoaded', function() {
    // Your existing code for initializing fees
    document.querySelectorAll('.destination-wrapper').forEach(dest => {
        const priceText = dest.querySelector('.destination-price').innerText;
        const price = parseFloat(priceText.replace('₱', '').trim());
        dest.setAttribute('data-price', price);
    });
    
    // Calculate initial fees
    updateTotalCost();
    
    // Check if there are destinations selected
    if (document.querySelectorAll('.destination-wrapper').length > 0) {
        // Optional: Check availability on page load
        // verifyAvailableDate(); 
    }
});
// Run this when the page loads to set initial fees
document.addEventListener('DOMContentLoaded', function() {
    // Update price attribute for each destination from the price display
    document.querySelectorAll('.destination-wrapper').forEach(dest => {
        const priceText = dest.querySelector('.destination-price').innerText;
        const price = parseFloat(priceText.replace('₱', '').trim());
        dest.setAttribute('data-price', price);
    });
    
    // Calculate initial fees
    updateTotalCost();
});

    // Update your existing updateTotalCost function
    function updateTotalCost() {
        let totalCost = 0;
        let peopleCount = parseInt(counterInput.value);

        feesList.innerHTML = '';
        document.querySelectorAll('.destination-wrapper').forEach(dest => {
            const name = dest.querySelector('.destination-details span').innerText;
            const price = parseFloat(dest.getAttribute('data-price'));
            
            // Check if price is valid
            if (!isNaN(price)) {
                const itemCost = price * peopleCount;
                feesList.innerHTML += `<div class="fee-item my-2">
                    <div class="d-flex justify-content-between">
                        <span>${name}</span>
                        <span>${price} x ${peopleCount}</span>
                    </div>
                    <div class="text-end">₱${itemCost.toFixed(2)}</div>
                </div>`;
                totalCost += itemCost;
            }
        });
        
        // Add a separator before total
        if (document.querySelectorAll('.destination-wrapper').length > 0) {
            feesList.innerHTML += '<hr class="my-2">';
        }
        
        totalCostDisplay.textContent = totalCost.toFixed(2);
    }
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

	//ADD MORE DESTINATION
	document.addEventListener("DOMContentLoaded", function () {
    const calendarDates = document.querySelectorAll(".calendar-date");
    const addMoreBtn = document.getElementById("addMoreDestinations");

    calendarDates.forEach(date => {
        date.addEventListener("click", function () {
            // Do not hide the "Add More Destinations" button
            console.log("Date selected:", this.innerText);
        });
    });

    addMoreBtn.addEventListener("click", function () {
        window.location.href = "../explore.php";
    });
});

</script>
</body>
</html>
