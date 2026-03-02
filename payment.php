<?php 
include 'db.php'; 
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
if (!isset($_GET['booking_id'])) { header("Location: home.php"); exit(); }

$booking_id = $_GET['booking_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Payment - FlightEase</title>
    <style>
        :root { --primary: #0056b3; --success: #28a745; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .payment-card { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; }
        .method-list { margin: 30px 0; display: flex; flex-direction: column; gap: 15px; }
        .method-item { border: 2px solid #eee; border-radius: 15px; padding: 15px; display: flex; align-items: center; cursor: pointer; transition: 0.3s; }
        .method-item:hover { border-color: var(--primary); background: #f8fbff; }
        .method-item input { margin-right: 15px; width: 20px; height: 20px; }
        .method-item img { height: 30px; margin-left: auto; }
        .btn-pay { width: 100%; padding: 18px; background: var(--success); color: white; border: none; border-radius: 12px; font-size: 18px; font-weight: bold; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-pay:hover { background: #218838; transform: scale(1.02); }
        h2 { color: #333; margin-top: 0; }
        .ref { font-size: 13px; color: #888; margin-top: 15px; }
    </style>
</head>
<body>
    <div class="payment-card">
        <div style="font-size: 50px;">💳</div>
        <h2>Payment Option</h2>
        <p style="color: #666;">Pilia ang imong pamaagi sa pagbayad:</p>

        <form action="process_payment.php" method="POST">
            <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
            <div class="method-list">
                <label class="method-item">
                    <input type="radio" name="pay_method" value="GCash" required>
                    <span>GCash / E-Wallet</span>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/GCash_logo.svg/1024px-GCash_logo.svg.png">
                </label>
                
                <label class="method-item">
                    <input type="radio" name="pay_method" value="Card">
                    <span>Credit / Debit Card</span>
                    <img src="https://cdn-icons-png.flaticon.com/512/633/633611.png">
                </label>

                <label class="method-item">
                    <input type="radio" name="pay_method" value="OTC">
                    <span>Over-the-Counter</span>
                    <img src="https://cdn-icons-png.flaticon.com/512/2830/2830284.png">
                </label>
            </div>

            <button type="submit" class="btn-pay">COMPLETE PAYMENT</button>
        </form>
        <div class="ref">Booking Reference: #FL-<?php echo $booking_id; ?></div>
    </div>
</body>
</html>