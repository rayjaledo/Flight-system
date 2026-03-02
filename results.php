<?php 
session_start();
include 'db.php'; 

// Protection: Kinahanglan naka-login
if (!isset($_SESSION['user'])) { 
    header("Location: login.php"); 
    exit(); 
}

// No-Back Protection
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Fetch Data gikan sa GET request
$from = mysqli_real_escape_string($conn, $_GET['origin'] ?? '');
$to = mysqli_real_escape_string($conn, $_GET['destination'] ?? '');
$class = mysqli_real_escape_string($conn, $_GET['class'] ?? 'Economy');

// Query sa Database
$sql = "SELECT * FROM flights WHERE origin LIKE '%$from%' AND destination LIKE '%$to%'";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results - FlightEase</title>
    <style>
        :root { --primary: #0062E3; --bg: #f1f5f9; --dark: #0f172a; }
        
        body { 
            margin: 0; font-family: 'Inter', 'Segoe UI', sans-serif; 
            background: var(--bg); color: var(--dark); 
        }

        /* Modern Blue Header */
        .blue-header {
            background: linear-gradient(135deg, #0062E3 0%, #004bb1 100%);
            color: white;
            padding: 60px 20px 100px;
            text-align: center;
        }
        .blue-header h1 { margin: 0; font-size: 32px; font-weight: 800; }
        .blue-header p { opacity: 0.9; font-size: 16px; margin-top: 10px; }

        .container { max-width: 900px; margin: -50px auto 50px; padding: 0 20px; }

        .back-nav { margin-bottom: 20px; }
        .back-nav a { color: white; text-decoration: none; font-weight: 600; font-size: 14px; }
        .back-nav a:hover { text-decoration: underline; }

        /* Ticket Card Design */
        .ticket-card { 
            background: white; border-radius: 20px; display: flex; 
            margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); 
            overflow: hidden; border: 1px solid rgba(0,0,0,0.05);
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .ticket-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); }

        .ticket-info { padding: 30px; flex: 2; }
        .airline-name { font-size: 22px; font-weight: 800; color: var(--primary); margin-bottom: 5px; }
        .route { font-size: 18px; color: #334155; font-weight: 700; margin: 15px 0; display: flex; align-items: center; gap: 10px; }
        
        .badges { display: flex; gap: 8px; }
        .badge { background: #f1f5f9; color: #475569; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; border: 1px solid #e2e8f0; }

        .ticket-price { 
            padding: 30px; flex: 1; background: #fafafa; 
            text-align: center; border-left: 2px dashed #e2e8f0; 
            display: flex; flex-direction: column; justify-content: center; 
            position: relative;
        }
        /* Ticket punch holes effect */
        .ticket-price::before, .ticket-price::after {
            content: ''; position: absolute; left: -11px; width: 20px; height: 20px; background: var(--bg); border-radius: 50%;
        }
        .ticket-price::before { top: -10px; }
        .ticket-price::after { bottom: -10px; }

        .price { font-size: 30px; color: var(--dark); font-weight: 900; margin-bottom: 15px; }
        
        .btn-book { 
            background: #FFD200; color: #000; text-decoration: none; 
            padding: 12px; border-radius: 12px; font-weight: 800; 
            transition: 0.3s; text-align: center; font-size: 14px;
        }
        .btn-book:hover { background: #e6bd00; transform: scale(1.05); }

        /* Empty State */
        .no-results { background: white; padding: 60px; border-radius: 24px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .no-results h3 { font-size: 24px; color: #64748b; }
    </style>
</head>
<body>

<div class="blue-header">
    <div class="container" style="margin: 0 auto;">
        <div class="back-nav"><a href="home.php">← Back to Flight Search</a></div>
        <h1>Flights to <?php echo strtoupper(htmlspecialchars($to)); ?></h1>
        <p>Found <?php echo mysqli_num_rows($result); ?> available flights for your trip</p>
    </div>
</div>

<div class="container">
    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="ticket-card">
                <div class="ticket-info">
                    <div class="airline-name">✈️ <?php echo $row['airline']; ?></div>
                    <div class="route">
                        <span><?php echo strtoupper($row['origin']); ?></span>
                        <span style="color: #cbd5e1; font-size: 14px;">────────</span>
                        <span><?php echo strtoupper($row['destination']); ?></span>
                    </div>
                    <div class="badges">
                        <span class="badge">📅 <?php echo date('M d, Y', strtotime($row['flight_date'])); ?></span>
                        <span class="badge">💺 <?php echo htmlspecialchars($class); ?></span>
                    </div>
                </div>
                <div class="ticket-price">
                    <div class="price">₱<?php echo number_format($row['price'], 2); ?></div>
                    <a href="booking_form.php?flight_id=<?php echo $row['id']; ?>&class=<?php echo $class; ?>" class="btn-book">BOOK NOW</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-results">
            <span style="font-size: 64px;">🔍</span>
            <h3>No flights found.</h3>
            <p>Try searching for different destinations or check back later.</p>
            <br>
            <a href="home.php" class="btn-book" style="display:inline-block; padding: 12px 30px;">Go Back</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>