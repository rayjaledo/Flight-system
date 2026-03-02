<?php 
session_start();
include 'db.php'; 

// Kung naka-login na, i-kick padulong sa home
if (isset($_SESSION['user'])) { 
    header("Location: home.php"); 
    exit(); 
}

// 2. No-Cache Protection
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// 3. Login Logic - Kini ang kulang sa imong code kaina
$error_msg = "";
if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    
    // I-query ang database aron i-match ang username ug password
    $sql = "SELECT * FROM users WHERE username='$user' AND password='$pass'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        // Successful Login! I-save ang session
        $_SESSION['user'] = $user;
        header("Location: home.php");
        exit();
    } else {
        // Failed Login
        $error_msg = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - FlightEase</title>
    <style>
        :root { --primary: #0062E3; --hover: #0056b3; --danger: #ef4444; }
        body { 
            margin: 0; font-family: 'Inter', sans-serif; 
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1350&q=80'); 
            background-size: cover; background-position: center;
            display: flex; justify-content: center; align-items: center; height: 100vh; 
        }
        .box { 
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 45px; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); 
            width: 100%; max-width: 360px; text-align: center; 
        }
        h2 { color: var(--primary); font-weight: 800; margin-bottom: 10px; }
        p.subtitle { color: #64748b; margin-bottom: 25px; font-size: 15px; }
        
        input { 
            width: 100%; padding: 14px; margin: 10px 0; border: 1px solid #ddd; 
            border-radius: 12px; box-sizing: border-box; outline: none; font-size: 15px; 
            background: #f8fafc; transition: 0.3s;
        }
        input:focus { border-color: var(--primary); background: white; }
        
        .btn-action { 
            width: 100%; padding: 14px; background: var(--primary); color: white; 
            border: none; border-radius: 12px; font-weight: 700; cursor: pointer; 
            transition: 0.3s; margin-top: 15px; font-size: 16px;
        }
        .btn-action:hover { background: var(--hover); transform: translateY(-2px); }
        
        .error-box { background: #fef2f2; color: var(--danger); padding: 10px; border-radius: 8px; font-size: 14px; margin-bottom: 15px; border: 1px solid #fee2e2; }
        .footer-text { margin-top: 25px; font-size: 14px; color: #475569; }
        .footer-text a { color: var(--primary); text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
    <div class="box">
        <h2>✈️ Welcome Back</h2>
        <p class="subtitle">Please enter your details to login</p>

        <?php if ($error_msg): ?>
            <div class="error-box"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn-action">Login</button>
        </form>
        
        <p class="footer-text">Wala pang account? <a href="signup.php">Sign Up</a></p>
    </div>
</body>
</html>