<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // I-save una nato sa Session ang info sa pasahero
    $_SESSION['temp_booking'] = [
        'flight_id' => $_POST['flight_id'],
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'age' => $_POST['age'],
        'gender' => $_POST['gender'],
        'class' => $_POST['class']
    ];
} else {
    header("Location: results.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - FlightEase</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .pay-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 400px; }
        .method { border: 2px solid #e2e8f0; padding: 15px; border-radius: 12px; margin-bottom: 10px; cursor: pointer; display: flex; align-items: center; gap: 10px; }
        .method:hover { border-color: #0062E3; background: #f0f7ff; }
        .btn-pay { width: 100%; background: #16a34a; color: white; border: none; padding: 15px; border-radius: 12px; font-weight: bold; font-size: 16px; margin-top: 20px; cursor: pointer; }
    </style>
</head>
<body>

<div class="pay-card">
    <h2 style="margin-top:0;">Payment Method</h2>
    <p style="color: #64748b; margin-bottom: 25px;">Select how you'd like to pay for your flight.</p>
    
    <div class="method">
        <input type="radio" name="pay" checked> 
        <span>💳 Credit / Debit Card</span>
    </div>
    <div class="method">
        <input type="radio" name="pay"> 
        <span>📱 GCash / Maya</span>
    </div>

    <div style="margin-top: 20px;">
        <label style="font-size:12px; font-weight:bold; color:gray;">CARD NUMBER</label>
        <input type="text" placeholder="**** **** **** 1234" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; margin-top:5px; box-sizing: border-box;">
    </div>

    <form action="confirm_booking.php" method="POST">
        <button type="submit" class="btn-pay">PAY NOW</button>
    </form>
</div>

</body>
</html>