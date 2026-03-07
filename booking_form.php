<?php
session_start();
include 'db.php';

// Data setup
$total_travelers = isset($_GET['pax']) ? (int)$_GET['pax'] : 1;
$flight_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Sample flight details - i-replace ni og dynamic data gikan sa imong database
$flight = ['origin' => 'Cebu', 'destination' => 'Manila', 'price' => 2500]; 
$total_price = $flight['price'] * $total_travelers;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Details</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f6f7f9; padding: 20px; color: #333; }
        .main-wrapper { 
            display: grid; 
            grid-template-columns: 1fr 350px; 
            gap: 25px; 
            max-width: 1200px; 
            margin: auto; 
        }

        .form-card { 
            background: white; border-radius: 12px; padding: 25px; 
            margin-bottom: 20px; border: 1px solid #e6e8ec;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); 
        }

        .grid-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
        
        label { display: block; font-size: 13px; font-weight: 600; color: #4a4a4a; margin-bottom: 6px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        
        .sidebar-sticky { position: sticky; top: 20px; height: fit-content; }
        .btn-submit { width: 100%; padding: 15px; background: #007bff; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        h3 { margin-top: 0; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="main-wrapper">
    <form action="process_booking.php" method="POST">
        <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">

        <div class="form-card">
            <h3>Mga detalye sa pakikipag-ugnayan</h3>
            <div class="grid-row">
                <div><label>Pangalan *</label><input type="text" name="contact_fname" required></div>
                <div><label>Apelyido *</label><input type="text" name="contact_lname" required></div>
            </div>
            <div class="grid-row">
                <div><label>Email ID *</label><input type="email" name="email" required></div>
                <div><label>Bansang tinitirhan *</label>
                    <select name="country"><option>Philippines</option></select>
                </div>
            </div>
            <label>Mobile number *</label>
            <div style="display: grid; grid-template-columns: 100px 1fr; gap: 15px;">
                <select name="country_code"><option value="+63">+63</option></select>
                <input type="tel" name="mobile_number" placeholder="Mobile number" required>
            </div>
        </div>

        <?php for ($i = 1; $i <= $total_travelers; $i++): ?>
        <div class="form-card">
            <h3>Pasahero <?php echo $i; ?>: (Adult, 18 taong gulang pataas)</h3>
            <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Ang mga detalye ng pasahero ay dapat pareho sa nasa iyong pasaporte o photo ID</p>
            
            <div style="margin-bottom: 15px;">
                <label>Kasarian *</label>
                <input type="radio" name="gender[<?php echo $i; ?>]" value="Lalaki" required> Lalaki
                <input type="radio" name="gender[<?php echo $i; ?>]" value="Babae" style="margin-left: 15px;"> Babae
            </div>
            
            <div class="grid-row">
                <div><label>Una at gitnang pangalan *</label><input type="text" name="fname[]" required></div>
                <div><label>Apelyido *</label><input type="text" name="lname[]" required></div>
            </div>
            
            <div class="grid-row">
                <div><label>Araw</label><input type="text" placeholder="DD" name="day[]" required></div>
                <div><label>Buwan</label>
                    <select name="month[]" required>
                        <option value="">Pumili</option>
                        <option value="1">Enero</option>
                    </select>
                </div>
            </div>
            <div class="grid-row">
                <div><label>Taon</label><input type="text" placeholder="YYYY" name="year[]" required></div>
                <div><label>Nasyonalidad *</label>
                    <select name="nationality[]" required>
                        <option value="">Pumili</option>
                        <option value="PH">Philippines</option>
                    </select>
                </div>
            </div>
        </div>
        <?php endfor; ?>
        
        <button type="submit" class="btn-submit">I-proceso ang Booking</button>
    </form>

    <div class="sidebar-sticky">
        <div class="form-card">
            <h3>Booking Summary</h3>
            <p>Flight: <b><?php echo $flight['origin']; ?> - <?php echo $flight['destination']; ?></b></p>
            <hr>
            <p>Total Travelers: <b><?php echo $total_travelers; ?></b></p>
            <h3>Total: ₱<?php echo number_format($total_price, 2); ?></h3>
        </div>
    </div>
</div>

</body>
</html>