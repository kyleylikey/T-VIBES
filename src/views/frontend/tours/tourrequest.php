<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Requests</title>
    <link rel="stylesheet" href="../../../../public/assets/styles/main.css">
    <link rel="stylesheet" href="../../../../public/assets/styles/tours.css">
    <link rel="stylesheet" href="../../../../public/assets/styles/tourrequest.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <style>
        .rounded-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .delete-btn, .counter-btn {
            background-color: #A9221C;
            border: none;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .delete-btn:hover, .counter-btn:hover {
            background-color: #8a1b16;
        }
        .destination-item {
            cursor: grab;
            background-color: #f8f9fa;
            padding: 16px;
            margin-bottom: 8px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background-color 0.2s ease;
        }
        .destination-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-grow: 1;
        }
        .destination-image {
            width: 80px;
            height: 80px;
            background-color: #e9ecef;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 12px;
            font-size: 36px;
            color: #6c757d;
        }
        .destination-number {
            margin-right: 12px;
            font-weight: bold;
        }
        .destination-details span:first-child {
            font-weight: bold;
            color: #102E47;
        }
        .trash-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-left: auto;
        }
        .people-counter label {
            font-weight: bold;
            color: #102E47;
            margin-bottom: 8px;
            display: block;
        }
        .counter-btn {
            font-size: 20px;
            width: 40px;
            height: 40px;
        }
        input[type="number"] {
            width: 60px;
            text-align: center;
            border-radius: 6px;
            border: 1px solid #ccc;
            padding: 6px;
            margin: 0;
        }
        .actions {
            margin-top: 20px;
            display: flex;
            gap: 12px;
        }
        .estimated-fees h2 {
            font-weight: bold;
            color: #102E47;
        }

        /* Confirmation Modal Styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal-content {
            background-color: #fff;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            width: 320px;
            text-align: center;
        }
        .modal-content i {
            font-size: 40px;
            color: #A9221C;
            margin-bottom: 12px;
        }
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 16px;
        }
        .modal-buttons button {
            padding: 10px 24px;
            border-radius: 24px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
            border: none;
        }
        .modal-buttons .confirm-btn {
            background-color: #102E47;
            color: white;
        }
        .modal-buttons .cancel-btn {
            background-color: #fff;
            color: #102E47;
            border: 2px solid #102E47;
        }
    </style>
</head>
<body>
    <?php include '../../templates/headertours.php'; ?>
    <?php include '../../templates/toursnav.php'; ?>

    <div class="content">
        <div class="tour-container" id="destination-list">
            <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="destination-wrapper">
                    <div class="destination-number"><?= $i ?></div>
                    <div class="destination-item">
                        <div class="destination-info">
                            <div class="destination-image"><i class="bi bi-image"></i></div>
                            <div class="destination-details">
                                <span>Destination Name <?= $i ?></span>
                            </div>
                            <div class="trash-container">
                                <span class="destination-price">₱ 0.00</span>
                                <button class="delete-btn rounded-circle"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>

        <!-- Counter -->
        <div class="people-counter">
            <label for="counter-input">How Many People?</label>
            <div>
                <button class="counter-btn" id="minus-btn">-</button>
                <input type="number" value="1" min="1" max="255" id="counter-input">
                <button class="counter-btn" id="plus-btn">+</button>
            </div>
        </div>
    </div>

    <?php include '../../templates/footer.html'; ?>

    <script>
    // Draggable destinations
    new Sortable(document.getElementById('destination-list'), {
        animation: 150,
        handle: '.destination-item',
        onEnd: () => updateDestinationNumbers()
    });

    function updateDestinationNumbers() {
        document.querySelectorAll('.destination-number').forEach((el, index) => {
            el.textContent = index + 1;
        });
    }

    // Confirmation modal
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const destination = e.target.closest('.destination-wrapper');

            const modal = document.createElement('div');
            modal.classList.add('modal-overlay');
            modal.innerHTML = `
                <div class="modal-content">
                    <i class="bi bi-trash"></i>
                    <p>Delete This Destination?</p>
                    <div class="modal-buttons">
                        <button class="confirm-btn">Yes</button>
                        <button class="cancel-btn">No</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            modal.querySelector('.confirm-btn').addEventListener('click', () => {
                destination.remove();
                updateDestinationNumbers();
                modal.remove();
            });

            modal.querySelector('.cancel-btn').addEventListener('click', () => {
                modal.remove();
            });
        });
    });

    // Counter functionality (fixed)
    const counterInput = document.getElementById('counter-input');
    const minusBtn = document.getElementById('minus-btn');
    const plusBtn = document.getElementById('plus-btn');

    minusBtn.addEventListener('click', () => {
        let value = parseInt(counterInput.value);
        if (value > 1) counterInput.value = value - 1;
    });

    plusBtn.addEventListener('click', () => {
        let value = parseInt(counterInput.value);
        if (value < 255) counterInput.value = value + 1;
    });
</script>

</body>
</html>
