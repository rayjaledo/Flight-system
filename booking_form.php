<?php 
session_start();
include 'db.php'; 

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

$user_id = $_SESSION['user_id'];

// Query para makuha ang bookings kauban ang flight details (JOIN)
$sql = "SELECT b.*, f.airline, f.origin, f.destination, f.price, f.flight_date 
        FROM bookings b 
        JOIN flights f ON b.flight_id = f.id 
        WHERE b.user_id = '$user_id' 
        ORDER BY b.booking_date DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - FlightEase</title>
    <style>
        :root { --primary: #0062E3; --dark: #1e293b; --bg: #f8fafc; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); margin: 0; padding: 40px 10%; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h2 { margin: 0; color: var(--dark); font-size: 28px; }
        
        /* Ticket Design */
        .ticket {
            background: white; border-radius: 15px; display: flex; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px; border-left: 8px solid var(--primary);
        }
        
        .ticket-main { padding: 25px; flex: 3; border-right: 2px dashed #e2e8f0; position: relative; }
        .ticket-stub { padding: 25px; flex: 1; background: #fdfdfd; display: flex; flex-direction: column; justify-content: center; text-align: center; }
        
        .airline-label { font-size: 12px; font-weight: bold; color: var(--primary); text-transform: uppercase; margin-bottom: 5px; display: block; }
        .route { display: flex; align-items: center; gap: 15px; margin: 15px 0; }
        .city { font-size: 24px; font-weight: 800; color: var(--dark); }
        
        .info-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 20px; }
        .label { font-size: 11px; color: #64748b; text-transform: uppercase; display: block; }
        .value { font-size: 14px; font-weight: 600; color: var(--dark); }
        
        .status-badge { background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .barcode { font-family: 'Libre Barcode 39', cursive; font-size: 40px; margin-top: 10px; opacity: 0.3; }

        .btn-home { background: var(--primary); color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+39&display=swap" rel="stylesheet">
</head>
<body>

<div class="header">
    <div>
        <h2>My Boarding Passes</h2>
        <p style="color: #64748b; margin-top: 5px;">You have <?php echo mysqli_num_rows($result); ?> confirmed bookings.</p>
    </div>
    <a href="home.php" class="btn-home">Book New Flight</a>
</div>

<?php if(mysqli_num_rows($result) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="ticket">
            <div class="ticket-main">
                <span class="airline-label">✈️ <?php echo $row['airline']; ?></span>
                <div class="route">
                    <span class="city"><?php echo $row['origin']; ?></span>
                    <span style="color: #cbd5e1;">————————</span>
                    <span class="city"><?php echo $row['destination']; ?></span>
                </div>
                
                <div class="info-row">
                    <div>
                        <span class="label">Passenger</span>
                        <span class="value"><?php echo strtoupper($row['passenger_name']); ?></span>
                    </div>
                    <div>
                        <span class="label">Flight Date</span>
                        <span class="value"><?php echo date('M d, Y', strtotime($row['flight_date'])); ?></span>
                    </div>
                    <div>
                        <span class="label">Class</span>
                        <span class="value"><?php echo $row['class']; ?></span>
                    </div>
                </div>
            </div>
            
            <div class="ticket-stub">
                <span class="label" style="margin-bottom: 5px;">Status</span>
                <div><span class="status-badge">CONFIRMED</span></div>
                <div class="barcode">12345678</div>
                <span class="label" style="margin-top: 5px;">Seat: AUTO</span>
            </div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div style="text-align: center; padding: 100px; background: white; border-radius: 20px;">
        <h3 style="color: #94a3b8;">No bookings found.</h3>
        <p style="color: #94a3b8;">Your future trips will appear here.</p>
    </div>
<?php endif; ?>

</body>
</html>