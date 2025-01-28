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
</head>
<body>
    <?php include '../../templates/header.php'; ?>
    <?php include '../../templates/toursnav.php'; ?>
    <div class="content">
        <div class="tourcontainer">
            <table class="sitelist">
                <tr class="site">
                    <td class="sitenumber">
                        <h2 class="circle">1</h2>
                    </td>
                    <td class="sitecontainer">
                        <div class="siteimage">
                            <i class="bi bi-image"></i>
                        </div>
                        <div class="siteinfo">
                            <div class="sitename"><h3>Site Name</h3></div>
                            <div></div>
                            <div class="price"><h3>P100.00</h3></div>
                            <div class="filler3"></div>
                            <div class="filler2"></div>
                            <div class="btndel"><button><i class="bi bi-trash"></i></button></div>
                        </div>
                    </td>
                </tr>
                <tr class="site">
                    <td class="sitenumber">
                        <h2 class="circle">2</h2>
                    </td>
                    <td class="sitecontainer">
                        <div class="siteimage">
                            <i class="bi bi-image"></i>
                        </div>
                        <div class="siteinfo">
                            <div class="sitename"><h3>Site Name</h3></div>
                            <div></div>
                            <div class="price"><h3>P100.00</h3></div>
                            <div class="filler3"></div>
                            <div class="filler2"></div>
                            <div class="btndel"><button><i class="bi bi-trash"></i></button></div>
                        </div>
                    </td>
                </tr>
                <tr class="site">
                    <td class="sitenumber">
                        <h2 class="circle">3</h2>
                    </td>
                    <td class="sitecontainer">
                        <div class="siteimage">
                            <i class="bi bi-image"></i>
                        </div>
                        <div class="siteinfo">
                            <div class="sitename"><h3>Site Name</h3></div>
                            <div></div>
                            <div class="price"><h3>P100.00</h3></div>
                            <div class="filler3"></div>
                            <div class="filler2"></div>
                            <div class="btndel"><button><i class="bi bi-trash"></i></button></div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="addmorecontainer">
                <a href="/T-VIBES/public/" class="addmore"><i class="bi bi-plus-lg"></i>&nbspAdd More Sites</a>
            </div>
            <div class="pax">
                <h2>How Many Tourists?</h2>
                <div class="pax-input">
                    <button type="button" id="minus-btn">-</button>
                    <input type="number" name="pax" id="pax" min="1" max="255" value="1" oninput="if(this.value > 255) this.value = 255; if(this.value < 1) this.value = 1;">
                    <style>
                        /* Remove the up and down arrows from the input number field */
                        input[type=number]::-webkit-inner-spin-button, 
                        input[type=number]::-webkit-outer-spin-button { 
                            -webkit-appearance: none; 
                            margin: 0; 
                        }
                        input[type=number] {
                            appearance: textfield;
                            -moz-appearance: textfield;
                        }
                    </style>
                    <button type="button" id="plus-btn">+</button>
                </div>
            </div>
            <div class="selectdate">
                <h2>Selected Date</h2>
                <input type="date" name="tour_date" id="tour_date" min="<?php echo date('Y-m-d'); ?>">
            </div>
            <div class="accommodation">
                Any special requests? (e.g. PWD/Senior Accommodation) Please contact us <a href="#contact">here</a>
            </div>
        </div>
        <div class="tourfees">
            <h2>Estimated Fees</h2>
            <table>
                <tr>
                    <td>Site Name</td>
                    <td>P300.00</td>
                </tr>
                <tr>
                    <td>Site Name</td>
                    <td>P0.00</td>
                </tr>
                <tr>
                    <td>Site Name</td>
                    <td>P300.00</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="font-weight: bold;">P600.00</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="font-weight: bold;">x 17 pax</td>
                </tr>
                <tfoot>
                    <tr>
                        <td>&nbsp</td>
                    </tr>
                    <tr>
                        <td><h2>Total:</h2></td>
                        <td><h2>P1500</h2></td>
                    </tr>
                </tfoot>
            </table>
            <button class="submit">Submit Request</button>
        </div>
    </div>
    <script src="../../../../public/assets/scripts/tours.js"></script> 
</body>
</html>