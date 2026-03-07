<?php
session_start();
include 'db.php'; // Siguruha nga husto ang connection sa user_system

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    
    // Simple query para i-check ang match
    $query = "SELECT * FROM admins WHERE username='$user' AND password='$pass'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin_id'] = $row['id'];
        header("Location: admin_flights.php");
        exit();
    } else {
        $error = "Invalid username or password!"; // Mugawas kini kon dili match
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - FlightEase</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .card { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 320px; text-align: center; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #0062E3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; width: 100%; }
        .err { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Admin Login</h2>
        <?php if(isset($error)) echo "<div class='err'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="user" placeholder="Username" required>
            <input type="password" name="pass" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>