<?php
session_start();
include 'db.php';

// SECURITY CHECK
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// SEARCH LOGIC
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

// UPDATE STATUS LOGIC
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    mysqli_query($conn, "UPDATE bookings SET status='Confirmed' WHERE id=$id");
    header("Location: admin_bookings.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Bookings - FlightEase</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #0062E3; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 290px); }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .nav-links a { display: block; color: white; text-decoration: none; padding: 12px; margin: 10px 0; border-radius: 8px; }
        .nav-links a:hover { background: #004bb1; }
        
        /* Search Bar Style */
        .search-box { margin-bottom: 20px; display: flex; gap: 10px; }
        .search-box input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 8px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background: #f8fafc; color: #64748b; }
        
        .status { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-confirmed { background: #dcfce7; color: #166534; }
        
        .btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; display: inline-block; margin-right: 5px; cursor: pointer; border: none; }
        .btn-print { background: #64748b; color: white; }
        .btn-confirm { background: #0062E3; color: white; }

        /* Print Style */
        @media print {
            .sidebar, .search-box, .btn-action, h3 p { display: none !important; }
            .main-content { margin-left: 0; padding: 0; width: 100%; }
            .card { box-shadow: none; border: none; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>FlightEase Admin</h2>
    <div class="nav-links">
        <a href="admin_flights.php">✈ Manage Flights</a>
        <a href="admin_bookings.php" style="background: #004bb1;">📋 View Bookings</a>
        <a href="admin_users.php">👥 Manage Users</a>
        <hr>
        <a href="admin_flights.php?logout=true" style="background: rgba(255,255,255,0.1);">🚪 Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="card">
        <h3>Passenger Bookings</h3>
        
        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search by Passenger Name..." value="<?php echo $search; ?>">
            <button type="submit" class="btn-action btn-confirm">Search</button>
            <button type="button" onclick="window.print()" class="btn-action btn-print">🖨 Print Report</button>
        </form>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Passenger</th>
                    <th>Flight Route</th>
                    <th>Class</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query with Search filter
                $query = "SELECT b.*, f.airline, f.origin, f.destination 
                          FROM bookings b 
                          JOIN flights f ON b.flight_id = f.id";
                
                if ($search != "") {
                    $query .= " WHERE b.first_name LIKE '%$search%' OR b.last_name LIKE '%$search%'";
                }
                
                $query .= " ORDER BY b.id DESC";
                $result = mysqli_query($conn, $query);

                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $status_class = ($row['status'] == 'Confirmed') ? 'status-confirmed' : 'status-pending';
                        echo "<tr>
                                <td>#{$row['id']}</td>
                                <td><b>{$row['first_name']} {$row['last_name']}</b><br><small>{$row['gender']}, {$row['age']}</small></td>
                                <td>{$row['airline']}<br><small>{$row['origin']} to {$row['destination']}</small></td>
                                <td>{$row['class']}</td>
                                <td><span class='status $status_class'>{$row['status']}</span></td>
                                <td>";
                        
                        if($row['status'] == 'Pending') {
                            echo "<a href='?approve={$row['id']}' class='btn-action btn-confirm'>Confirm</a>";
                        }
                        
                        // Link para sa individual ticket/receipt
                       echo "<a href='print_ticket.php?id={$row['id']}' target='_blank' class='btn-action btn-print'>Receipt</a>";
                        
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>Walay nakit-an nga booking.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function printReceipt(id) {
    // Pwede nimo himuan og separate 'print_ticket.php?id=' pero sa pagkakaron, window print lang sa ta
    alert("Printing Receipt for Booking #" + id);
    window.print();
}
</script>

</body>
</html>