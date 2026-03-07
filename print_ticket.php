<?php
include 'db.php';

if (!isset($_GET['id'])) {
    die("Booking ID not found.");
}

$id = $_GET['id'];

// I-fetch ang data sa passenger ug flight
$sql = "SELECT b.*, f.airline, f.origin, f.destination, f.departure_time, f.arrival_time, f.flight_date, f.price 
        FROM bookings b 
        JOIN flights f ON b.flight_id = f.id 
        WHERE b.id = '$id'";

$res = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

if (!$data) { die("Ticket not found."); }
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket - <?php echo $data['first_name']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #ddd; padding: 20px; }
        .ticket { background: white; width: 600px; margin: 0 auto; border: 2px dashed #333; padding: 20px; position: relative; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .airline-name { font-size: 24px; font-weight: bold; color: #0062E3; }
        .section { margin: 20px 0; display: flex; justify-content: space-between; }
        .label { font-size: 12px; color: #666; text-transform: uppercase; }
        .value { font-size: 16px; font-weight: bold; }
        .footer { border-top: 1px solid #eee; padding-top: 15px; text-align: center; font-size: 12px; }
        .qr-code { width: 80px; height: 80px; background: #000; margin: 0 auto; color: white; display: flex; align-items: center; justify-content: center; font-size: 10px; }
        
        @media print {
            body { background: white; padding: 0; }
            .ticket { border: 2px solid #000; width: 100%; box-shadow: none; }
            button { display: none; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" style="margin-bottom: 10px; padding: 10px; cursor:pointer;">🖨 Print Ticket</button>

    <div class="ticket">
        <div class="header">
            <div class="airline-name">FLIGHTEASE E-TICKET</div>
            <div>Booking Reference: #<?php echo $data['id']; ?></div>
        </div>

        <div class="section">
            <div>
                <div class="label">Passenger Name</div>
                <div class="value"><?php echo strtoupper($data['first_name'] . " " . $data['last_name']); ?></div>
            </div>
            <div>
                <div class="label">Class</div>
                <div class="value"><?php echo $data['class']; ?></div>
            </div>
        </div>

        <div class="section">
            <div>
                <div class="label">From</div>
                <div class="value"><?php echo $data['origin']; ?></div>
            </div>
            <div>
                <div class="label">To</div>
                <div class="value"><?php echo $data['destination']; ?></div>
            </div>
        </div>

        <div class="section">
            <div>
                <div class="label">Airline</div>
                <div class="value"><?php echo $data['airline']; ?></div>
            </div>
            <div>
                <div class="label">Flight Date</div>
                <div class="value"><?php echo date('M d, Y', strtotime($data['flight_date'])); ?></div>
            </div>
        </div>

        <div class="section">
            <div>
                <div class="label">Departure</div>
                <div class="value"><?php echo $data['departure_time']; ?></div>
            </div>
            <div>
                <div class="label">Status</div>
                <div class="value"><?php echo strtoupper($data['status']); ?></div>
            </div>
        </div>

        <div class="footer">
            <div class="qr-code">SCAN ME</div>
            <p>Please present this ticket at the check-in counter 2 hours before departure.</p>
        </div>
    </div>

</body>
</html>