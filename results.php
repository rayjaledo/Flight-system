<?php 
session_start();
include 'db.php'; 

if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }

// 1. Limpyohan ang Destination
$raw_to = $_GET['destination'] ?? '';
$to_array = explode(" (", $raw_to);
$to = mysqli_real_escape_string($conn, $to_array[0]); 

$from = mysqli_real_escape_string($conn, $_GET['origin'] ?? '');
$class = mysqli_real_escape_string($conn, $_GET['class'] ?? 'Economy');

// 2. I-format ang Date
$departure_date = "";
if (isset($_GET['dates']) && !empty($_GET['dates'])) {
    $dates = explode(" to ", $_GET['dates']);
    $departure_date = mysqli_real_escape_string($conn, $dates[0]);
}

// 3. SQL Query
$sql = "SELECT * FROM flights WHERE origin = '$from' AND destination = '$to'";
$sql .= " ORDER BY price ASC";
$result = mysqli_query($conn, $sql);

// 4. Function para sa images
function getCityImage($city) {
    $city = strtolower(trim($city));
    $images = [
        'manila'    => 'https://thumbs.dreamstime.com/b/skyline-manila-pasig-river-philippines-144586293.jpg',
        'cebu'      => 'https://images.unsplash.com/photo-1624523178088-750530737482?auto=format&fit=crop&w=600&q=80',
        'davao'     => 'https://tourism.davaocity.gov.ph/wp-content/themes/dccustomtheme/public/images/mountain-bg.webp',
        'boracay'   => 'https://a.cdn-hotels.com/gdcs/production67/d1485/a24503e6-6f87-40bc-9d1d-88cfe7eace6a.jpg',
        'bacolod'   => 'https://twomonkeystravelgroup.com/wp-content/uploads/2020/06/Travel-Guide-to-Bacolod-Philippines-a-DIY-Guide-to-the-City-of-Smiles4.jpg',
        'iloilo'    => 'https://media-cdn.tripadvisor.com/media/photo-c/1280x250/0f/1b/f1/8c/simply-breathtaking.jpg',
        'siargao'   => 'https://www.jonnymelon.com/wp-content/uploads/2018/10/daku-island-7.jpg',
        'palawan'   => 'https://www.traveltourxp.com/wp-content/uploads/2016/12/Tourist-Attractions-In-Palawan.jpg',
        'bohol'     => 'https://a.cdn-hotels.com/gdcs/production110/d1175/2ea00603-df4f-4ef0-910e-b5202325fba8.jpg',
        'tacloban'  => 'https://thumbs.dreamstime.com/b/tacloban-city-leyte-philippines-oct-aerial-provincial-library-downtown-258605830.jpg',
        'zamboanga' => 'https://tzmedia.b-cdn.net/media/images/ph/place/max/0de0e4356dacb6820f27ad07bff26ca2.jpg?1617125175'
    ];
    foreach ($images as $key => $url) {
        if (strpos($city, $key) !== false) return $url;
    }
    return "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=600&q=80";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Flights - FlightEase</title>
    <style>
        :root { --primary: #0062E3; --dark: #00253c; --skeleton: #e2e8f0; }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; margin: 0; padding: 20px 10%; }
        
        /* Navigation / Logo Styling */
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 0; }
        .logo { font-size: 26px; font-weight: 800; text-decoration: none; color: var(--dark); display: flex; align-items: center; gap: 10px; }
        .logo-img { height: 30px; width: auto; }

        .header { margin-top: 20px; margin-bottom: 30px; }
        .header h2 { font-size: 32px; color: var(--dark); margin: 0; }
        
        /* Grid and Cards */
        .deals-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        .ticket-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); transition: 0.3s; cursor: pointer; }
        .ticket-card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        
        .img-wrap { height: 180px; width: 100%; overflow: hidden; position: relative; background: var(--skeleton); }
        .ticket-img { width: 100%; height: 100%; object-fit: cover; display: block; transition: 0.5s; }
        
        .content { padding: 20px; }
        .content h3 { margin: 0; font-size: 22px; color: var(--dark); }
        
        /* Airline Info with Mini Icon */
        .airline-info { display: flex; align-items: center; gap: 8px; color: #64748b; margin: 8px 0; font-size: 14px; }
        .mini-icon { height: 16px; width: auto; }
        
        .content .date-info { color: #64748b; margin: 5px 0; font-size: 14px; display: flex; align-items: center; gap: 5px; }
        
        .footer { display: flex; justify-content: space-between; align-items: center; margin-top: 20px; }
        .price { font-size: 24px; font-weight: 800; color: var(--primary); }
        .btn-book { background: var(--primary); color: white; padding: 10px 20px; border-radius: 10px; font-weight: bold; text-decoration: none; }
        
        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 1000; }
        .modal-box { background: white; padding: 30px; border-radius: 20px; width: 400px; position: relative; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="home.php" class="logo">
        Cheapflights <img src="https://img.freepik.com/premium-vector/airplane-icon-flat-transportation-plane-symbol-sign-air-fly-isolated-white-background_89223-500.jpg?w=360" class="logo-img">
    </a>
    <a href="home.php" style="text-decoration:none; color:var(--primary); font-weight:bold;">← Change Search</a>
</div>

<div class="header">
    <h2>Flights to <?php echo strtoupper($to); ?></h2>
    <p>Found <?php echo mysqli_num_rows($result); ?> available flights from <?php echo $from; ?></p>
</div>

<div class="deals-grid">
    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="ticket-card" onclick="openBooking('<?php echo $row['id']; ?>', '<?php echo $row['destination']; ?>')">
                <div class="img-wrap">
                    <img src="<?php echo getCityImage($row['destination']); ?>" class="ticket-img" alt="destination">
                </div>
                <div class="content">
                    <h3><?php echo $row['destination']; ?></h3>
                    
                    <p class="airline-info">
                        <img src="https://img.freepik.com/free-vector/airplane-with-circle-flight-path_78370-4778.jpg?semt=ais_rp_progressive&w=740&q=80" class="mini-icon">
                        <?php echo $row['airline']; ?> • <?php echo $class; ?>
                    </p>
                    
                    <p class="date-info">
                        📅 <?php echo date('M d, Y', strtotime($row['flight_date'])); ?> • <?php echo date('h:i A', strtotime($row['departure_time'])); ?>
                    </p>
                    
                    <div class="footer">
                        <div class="price">₱<?php echo number_format($row['price']); ?></div>
                        <div class="btn-book">Book Now</div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
            <img src="https://cdn-icons-png.flaticon.com/512/6134/6134065.png" width="100" style="opacity:0.5">
            <p style="color:#64748b; font-size:18px; margin-top:20px;">No flights found for this route.</p>
        </div>
    <?php endif; ?>
</div>

<div class="modal-overlay" id="bookModal">
    <div class="modal-box">
        <h3 id="mTitle" style="margin-top:0;">Confirm Booking</h3>
        <form action="confirm_booking.php" method="POST">
            <input type="hidden" name="flight_id" id="fId">
            <input type="hidden" name="class" value="<?php echo $class; ?>">
            
            <label style="font-size:12px; font-weight:bold; color:gray;">FIRST NAME</label>
            <input type="text" name="first_name" required style="width:100%; padding:10px; margin: 5px 0 15px; border-radius:8px; border:1px solid #ddd;">
            
            <label style="font-size:12px; font-weight:bold; color:gray;">LAST NAME</label>
            <input type="text" name="last_name" required style="width:100%; padding:10px; margin: 5px 0 15px; border-radius:8px; border:1px solid #ddd;">
            
            <div style="display:flex; gap:10px;">
                <div style="flex:1;">
                    <label style="font-size:12px; font-weight:bold; color:gray;">AGE</label>
                    <input type="number" name="age" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                </div>
                <div style="flex:1;">
                    <label style="font-size:12px; font-weight:bold; color:gray;">GENDER</label>
                    <select name="gender" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
            </div>

            <button type="submit" style="width:100%; background:var(--primary); color:white; border:none; padding:15px; border-radius:12px; font-weight:bold; margin-top:20px; cursor:pointer;">CONFIRM BOOKING</button>
            <p onclick="closeBooking()" style="text-align:center; color:#64748b; cursor:pointer; font-size:14px; margin-top:15px;">← Cancel and go back</p>
        </form>
    </div>
</div>

<script>
    function openBooking(id, city) {
        document.getElementById('fId').value = id;
        document.getElementById('mTitle').innerText = "Book flight to " + city;
        document.getElementById('bookModal').style.display = 'flex';
    }
    function closeBooking() {
        document.getElementById('bookModal').style.display = 'none';
    }
</script>

</body>
</html>