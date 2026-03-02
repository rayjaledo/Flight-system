<?php 
session_start();
include 'db.php'; 

// 1. Kung naka-login na, i-diretso sa home
if (isset($_SESSION['user'])) { 
    header("Location: home.php"); 
    exit(); 
}

// 2. PHP Logic para sa Registration
$msg = "";
$msg_style = "";

if (isset($_POST['register'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);
    
    // I-check kung naa na ba ang username
    $check_user = mysqli_query($conn, "SELECT * FROM users WHERE username='$user'");
    
    if (mysqli_num_rows($check_user) > 0) {
        $msg = "Username already taken!";
        $msg_style = "background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;";
    } else {
        // I-insert ang bag-ong user
        $sql = "INSERT INTO users (username, password) VALUES ('$user', '$pass')";
        if (mysqli_query($conn, $sql)) {
            $msg = "Account created! You can now login.";
            $msg_style = "background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0;";
        } else {
            $msg = "Error: Could not register.";
            $msg_style = "background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup - FlightEase</title>
    <style>
        :root { --primary: #0062E3; --hover: #0056b3; }
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
        h2 { color: var(--primary); font-weight: 800; margin-bottom: 5px; }
        
        .alert { padding: 12px; border-radius: 10px; font-size: 14px; margin-bottom: 20px; font-weight: 500; }
        
        input { 
            width: 100%; padding: 14px; margin: 10px 0; border: 1px solid #ddd; 
            border-radius: 12px; box-sizing: border-box; outline: none; font-size: 15px; 
            background: #f8fafc;
        }
        input:focus { border-color: var(--primary); background: white; }

        .btn-action { 
            width: 100%; padding: 14px; background: var(--primary); color: white; 
            border: none; border-radius: 12px; font-size: 16px; font-weight: 700; 
            cursor: pointer; transition: 0.3s; margin-top: 15px; 
        }
        .btn-action:hover { background: var(--hover); transform: translateY(-2px); }
        
        .footer-text { margin-top: 25px; font-size: 14px; color: #475569; }
        .footer-text a { color: var(--primary); text-decoration: none; font-weight: 700; }
    </style>
</head>
<body>
    <div class="box">
        <h2>✈️ FlightEase</h2>
        <p style="color:#64748b; margin-bottom: 25px;">Create an account to book flights</p>

        <?php if ($msg != ""): ?>
            <div class="alert" style="<?php echo $msg_style; ?>">
                <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register" class="btn-action">Sign Up</button>
        </form>
        <p class="footer-text">May account na? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
