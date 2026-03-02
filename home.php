<?php 
session_start();

// Kung WALA naka-login, balik sa login page
if (!isset($_SESSION['user'])) { 
    header("Location: login.php"); 
    exit(); 
}

// 4. Cache Control: Pugngan ang browser sa pag-cache aron dili magamit ang "Back" button human sa logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlightEase - Modern Flight Booking</title>
    <style>
        :root { 
            --primary: #0062E3; 
            --secondary: #FFD200; 
            --bg-light: #f8fafc;
            --dark: #1e293b; 
        }

        body { 
            margin: 0; 
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif; 
            background: var(--bg-light); 
            color: var(--dark);
        }
        
        /* Navbar */
        .navbar { 
            background: white; 
            padding: 15px 10%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo { 
            font-size: 24px; 
            font-weight: 800; 
            text-decoration: none; 
            color: var(--primary); 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        .nav-links { display: flex; align-items: center; gap: 25px; }
        .nav-links a { 
            color: #64748b; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 15px;
            transition: 0.2s;
        }
        .nav-links a:hover { color: var(--primary); }
        .logout-btn { 
            background: #fef2f2; 
            color: #ef4444 !important; 
            padding: 8px 18px; 
            border-radius: 20px; 
            font-size: 14px !important;
        }
        .logout-btn:hover { background: #fee2e2; }

        /* Hero Section */
        .hero { 
            background: linear-gradient(135deg, rgba(0,98,227,0.9), rgba(0,98,227,0.7)), 
                        url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1350&q=80'); 
            background-size: cover; 
            background-position: center; 
            height: 450px; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            align-items: center; 
            color: white; 
            text-align: center; 
            padding: 0 20px; 
        }
        .hero h1 { font-size: 52px; margin: 0; font-weight: 800; letter-spacing: -1px; }
        .hero p { font-size: 20px; opacity: 0.95; margin-top: 15px; font-weight: 400; }

        /* Search Box */
        .search-container { 
            max-width: 1050px; 
            width: 92%; 
            background: white; 
            margin: -60px auto 50px; 
            padding: 40px; 
            border-radius: 24px; 
            box-shadow: 0 20px 50px rgba(0,0,0,0.08); 
        }
        .search-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 25px; 
        }
        .input-box { display: flex; flex-direction: column; }
        .input-box label { 
            font-size: 13px; 
            font-weight: 700; 
            color: #94a3b8; 
            text-transform: uppercase; 
            margin-bottom: 10px; 
            letter-spacing: 0.5px;
        }
        .input-box input, .input-box select { 
            padding: 14px; 
            border: 2px solid #f1f5f9; 
            border-radius: 12px; 
            font-size: 16px; 
            outline: none; 
            transition: 0.3s; 
            background: #f8fafc;
            color: var(--dark);
        }
        .input-box input:focus, .input-box select:focus { 
            border-color: var(--primary); 
            background: white;
            box-shadow: 0 0 0 4px rgba(0,98,227,0.05); 
        }

        .search-btn { 
            grid-column: 1 / -1; 
            background: var(--secondary); 
            color: #000; 
            border: none; 
            padding: 18px; 
            border-radius: 14px; 
            font-size: 18px; 
            font-weight: 800; 
            cursor: pointer; 
            transition: 0.3s; 
            margin-top: 15px; 
            box-shadow: 0 4px 15px rgba(255, 210, 0, 0.3);
        }
        .search-btn:hover { 
            background: #f5c700; 
            transform: translateY(-2px); 
            box-shadow: 0 6px 20px rgba(255, 210, 0, 0.4);
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="home.php" class="logo">✈️ FlightEase</a>
    <div class="nav-links">
        <a href="my_bookings.php">My Bookings</a>
        <a href="logout.php" class="logout-btn">Logout (<?php echo htmlspecialchars($_SESSION['user']); ?>)</a>
    </div>
</div>

<div class="hero">
    <h1>Ready for your next adventure?</h1>
    <p>Book the cheapest flights to your favorite destinations.</p>
</div>

<div class="search-container">
    <form action="results.php" method="GET">
        <div class="search-grid">
            <div class="input-box">
                <label>From (Origin)</label>
                <input type="text" name="origin" placeholder="e.g. Cebu" required>
            </div>
            <div class="input-box">
                <label>To (Destination)</label>
                <input type="text" name="destination" placeholder="e.g. Manila" required>
            </div>
            <div class="input-box">
                <label>Departure Date</label>
                <input type="date" name="date" required>
            </div>
            <div class="input-box">
                <label>Travel Class</label>
                <select name="class">
                    <option value="Economy">Economy Class</option>
                    <option value="Business">Business Class</option>
                    <option value="First">First Class</option>
                </select>
            </div>
        </div>
        <button type="submit" class="search-btn">SEARCH AVAILABLE FLIGHTS</button>
    </form>
</div>

</body>
</html>