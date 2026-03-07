<?php
session_start();
include 'db.php';

// SECURITY CHECK
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// LOGOUT LOGIC
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_login.php");
    exit();
}

// DELETE USER LOGIC (Optional)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: admin_users.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - Cheapflights</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 250px; background: #0062E3; height: 100vh; color: white; padding: 20px; position: fixed; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 290px); }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .nav-links a { display: block; color: white; text-decoration: none; padding: 12px; margin: 10px 0; border-radius: 8px; }
        .nav-links a:hover { background: #004bb1; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; font-size: 14px; }
        th { background: #f8fafc; color: #64748b; }
        .btn-delete { background: #dc2626; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Cheapflights</h2>
    <div class="nav-links">
        <a href="admin_flights.php">✈ Manage Flights</a>
        <a href="admin_bookings.php">📋 View Bookings</a>
        <a href="admin_users.php" style="background: #004bb1;">👥 Manage Users</a>
        <hr>
        <a href="?logout=true" style="background: rgba(255,255,255,0.1);">🚪 Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="card">
        <h3>Registered Users</h3>
        
        
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // I-query ang users table
                $result = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                if(mysqli_num_rows($result) > 0) {
                   while($row = mysqli_fetch_assoc($result)) {
    // Siguruha nga kini nga mga 'keys' (pananglitan: 'username') 
    // anaa gyud sa imong database table.
    $display_name = isset($row['first_name']) ? $row['first_name'] . " " . $row['last_name'] : $row['username'];
    $display_email = isset($row['email']) ? $row['email'] : "No Email Provided";

    echo "<tr>
            <td>#{$row['id']}</td>
            <td><b>{$display_name}</b></td>
            <td>{$display_email}</td>
            <td>
                <a href='?delete={$row['id']}' class='btn-delete' onclick='return confirm(\"Sigurado ka?\")'>Delete</a>
            </td>
          </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>No users registered yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>