<?php 
session_start();
include 'db.php'; 

// Protection
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

$flight_id = $_GET['flight_id'] ?? '';
$class = $_GET['class'] ?? 'Economy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Passenger Info - FlightEase</title>
    <style>
        :root { --primary: #0062E3; --hover: #0056b3; }
        body { 
            margin: 0; font-family: 'Inter', sans-serif; 
            /* Same background sa home/login para consistent */
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1350&q=80'); 
            background-size: cover; background-position: center;
            display: flex; justify-content: center; align-items: center; min-height: 100vh; 
        }

        .box { 
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px);
            padding: 40px; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); 
            width: 100%; max-width: 450px; text-align: center; 
        }

        h2 { color: var(--primary); font-weight: 800; margin-bottom: 5px; }
        .flight-badge { 
            background: #eef4ff; color: var(--primary); 
            padding: 5px 15px; border-radius: 20px; 
            font-size: 13px; font-weight: 700; display: inline-block; margin-bottom: 25px;
        }

        .form-group { text-align: left; margin-bottom: 15px; }
        label { display: block; font-size: 14px; font-weight: 600; color: #475569; margin-bottom: 5px; margin-left: 5px; }

        input, select { 
            width: 100%; padding: 12px; border: 1px solid #ddd; 
            border-radius: 12px; box-sizing: border-box; outline: none; 
            font-size: 15px; background: #f8fafc; transition: 0.3s;
        }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(0,98,227,0.1); }

        .row { display: flex; gap: 15px; }
        .row .form-group { flex: 1; }

        .btn-confirm { 
            width: 100%; padding: 15px; background: var(--primary); color: white; 
            border: none; border-radius: 12px; font-weight: 700; font-size: 16px;
            cursor: pointer; transition: 0.3s; margin-top: 20px;
        }
        .btn-confirm:hover { background: var(--hover); transform: translateY(-2px); }

        .back-btn { display: block; margin-top: 20px; color: #64748b; text-decoration: none; font-size: 14px; }
        .back-btn:hover { color: var(--primary); }
    </style>
</head>
<body>

<div class="box">
    <h2>Passenger Details</h2>
    <div class="flight-badge">Flight ID: #<?php echo htmlspecialchars($flight_id); ?> | Class: <?php echo htmlspecialchars($class); ?></div>

    <form action="confirm_booking.php" method="POST">
        <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>">
        <input type="hidden" name="class" value="<?php echo $class; ?>">

        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" placeholder="Juan" required>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" placeholder="Dela Cruz" required>
        </div>

        <div class="row">
            <div class="form-group">
                <label>Age</label>
                <input type="number" name="age" placeholder="25" required>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-confirm">CONFIRM BOOKING</button>
    </form>

    <a href="results.php" class="back-btn">← Cancel and go back</a>
</div>

</body>
</html>