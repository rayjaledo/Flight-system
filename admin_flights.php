<?php
session_start();
include 'db.php';

// 1. LOGOUT LOGIC & SECURITY CHECK
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// 2. SAVE FLIGHT LOGIC
if (isset($_POST['save_flight'])) {
    $airline = mysqli_real_escape_string($conn, $_POST['airline']);
    $origin = mysqli_real_escape_string($conn, $_POST['origin']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $f_date = $_POST['f_date'];
    $dep_time = $_POST['dep_time'];
    $arr_time = $_POST['arr_time'];
    $price = $_POST['price'];

    $sql = "INSERT INTO flights (airline, origin, destination, flight_date, departure_time, arrival_time, price) 
            VALUES ('$airline', '$origin', '$destination', '$f_date', '$dep_time', '$arr_time', '$price')";
    
    if (mysqli_query($conn, $sql)) {
        $msg = "Flight added successfully!";
    }
}
// 3. DELETE FLIGHT LOGIC
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // Seguradohon nga number ang ID
    $delete_query = "DELETE FROM flights WHERE id = $id";
    
    if (mysqli_query($conn, $delete_query)) {
        // Human ma-delete, i-refresh ang page aron mawala sa listahan
        header("Location: admin_flights.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - FlightEase</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #0062E3; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: 100%; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        input, select { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ddd; border-radius: 6px; }
        .btn { background: #0062E3; color: white; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-red { background: #dc2626; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8fafc; }
        .nav-links a { display: block; color: white; text-decoration: none; padding: 12px; margin: 10px 0; border-radius: 8px; }
        .nav-links a:hover { background: #004bb1; }
        h4 { margin: 0; font-size: 12px; opacity: 0.8; }
        h2 { margin: 5px 0 0 0; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Cheapflights ✈</h2>
    <div class="nav-links">
        <a href="admin_flights.php">Manage Flights</a>
        <a href="admin_bookings.php">View Bookings</a> <hr>
        <a href="admin_users.php">Manage Users</a>
        <a href="?logout=true" style="background: rgba(255,255,255,0.1);">Logout</a>
    </div>
</div>

<div class="main-content">

    <div style="display: flex; gap: 20px; margin-bottom: 20px;">
        <?php
        // Pagkuha sa Stats gikan sa database
        $total_flights = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM flights"));
        $total_bookings = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bookings"));
        $total_income_res = mysqli_query($conn, "SELECT SUM(f.price) as total FROM bookings b JOIN flights f ON b.flight_id = f.id WHERE b.status='Confirmed'");
        $total_income = mysqli_fetch_assoc($total_income_res);
        ?>
        
        <div style="flex: 1; background: #0062E3; color: white; padding: 20px; border-radius: 12px;">
            <h4>✈ TOTAL FLIGHTS</h4>
            <h2><?php echo $total_flights; ?></h2>
        </div>
        <div style="flex: 1; background: #16a34a; color: white; padding: 20px; border-radius: 12px;">
            <h4>📋 TOTAL BOOKINGS</h4>
            <h2><?php echo $total_bookings; ?></h2>
        </div>
        <div style="flex: 1; background: #ca8a04; color: white; padding: 20px; border-radius: 12px;">
            <h4>💰 TOTAL INCOME</h4>
            <h2>₱<?php echo number_format($total_income['total'] ?? 0, 2); ?></h2>
        </div>
    </div>

    <div class="card">
        <h3>Add New Flight</h3>
        <?php if(isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>
        <form method="POST">
            <input type="text" name="airline" placeholder="Airline Name (e.g. Cebu Pacific)" required>
            <div style="display:flex; gap:10px;">
                <input type="text" name="origin" placeholder="Origin" required>
                <input type="text" name="destination" placeholder="Destination" required>
            </div>
            <input type="date" name="f_date" required>
            <div style="display:flex; gap:10px;">
                <input type="time" name="dep_time" required>
                <input type="time" name="arr_time" required>
            </div>
            <input type="number" name="price" placeholder="Price (PHP)" required>
            <button type="submit" name="save_flight" class="btn">Save Flight</button>
        </form>
    </div>

    <div class="card">
    <h3>Current Flights</h3>
    <table>
        <thead>
            <tr>
                <th>Airline</th>
                <th>Route</th>
                <th>Date</th>
                <th>Time</th>
                <th>Price</th>
                <th>Action</th>
            </tr>
        </thead>
        
        <tbody>
            <?php
            // I-fetch ang tanang flights gikan sa database
            $flights = mysqli_query($conn, "SELECT * FROM flights ORDER BY id DESC");
            while($f = mysqli_fetch_assoc($flights)) {
                echo "<tr>
                        <td>{$f['airline']}</td>
                        <td>" . ucfirst($f['origin']) . " to " . ucfirst($f['destination']) . "</td>
                        <td>{$f['flight_date']}</td>
                        <td>{$f['departure_time']} - {$f['arrival_time']}</td>
                        <td>₱" . number_format($f['price'], 2) . "</td>
                        <td>
                            <a href='admin_flights.php?delete={$f['id']}' 
                               class='btn btn-red' 
                               onclick='return confirm(\"Sigurado ka nga i-delete kini?\")' 
                               style='padding: 5px 10px; font-size: 12px; text-decoration:none; background:#dc2626; color:white; border-radius:4px;'>Delete</a>
                        </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>

</body>
</html>