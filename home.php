<?php 
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Function para sa picture base sa destination gamit imong links
function getCityImage($city) {
    $city = strtolower($city);
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
    return 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=600&q=80';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlightEase - Booking</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        :root { --primary: #0062E3; --secondary: #FFD200; --bg: #f1f5f9; --dark: #0f172a; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--dark); }
        
        .navbar { background: white; padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .logo { font-size: 26px; font-weight: 800; text-decoration: none; color: #00253c; display: flex; align-items: center; gap: 10px; }
        .logout-link { color: #ef4444; text-decoration: none; font-weight: 700; background: #fef2f2; padding: 8px 15px; border-radius: 20px; }

        /* GIPABILIN ANG BACKGROUND */
        .hero { 
            background: linear-gradient(135deg, rgba(0,98,227,0.7), rgba(0,98,227,0.4)), url('https://wallpaperaccess.com/full/254367.png'); 
            height: 450px; display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; text-align: center; background-size: cover; background-position: center;
        }

        .search-container { max-width: 1250px; width: 95%; background: white; margin: -50px auto 50px; padding: 25px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.12); position: relative; z-index: 1000; }
        .trip-type-container { display: flex; gap: 20px; margin-bottom: 20px; }
        .trip-type-label { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; cursor: pointer; color: #64748b; }
        .trip-type-label.active { color: var(--primary); }

        .search-grid { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; position: relative; }
        
        /* ICONS POSITIONING - GIBALIK SA SULOD */
        .input-group { flex: 1; min-width: 180px; display: flex; flex-direction: column; position: relative; }
        .input-group label { font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 5px; text-transform: uppercase; }
        
        .field-box { 
            padding: 12px 12px 12px 40px; /* Space para sa icon sa left */
            border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-weight: 600; 
            background: #fff; width: 100%; cursor: pointer; display: flex; 
            align-items: center; height: 45px; box-sizing: border-box; outline: none; 
        }

        /* I-adjust ang icons para naa sa sulod sa box */
        .input-group i { 
            position: absolute; 
            left: 15px; 
            top: 32px; /* I-align base sa height sa box human sa label */
            color: #4b5563; 
            z-index: 10;
            pointer-events: none;
        }

        /* Travelers Dropdown */
        .dropdown-content { display: none; position: absolute; top: 100%; right: 0; width: 350px; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); padding: 20px; z-index: 2001; border: 1px solid #eee; margin-top: 10px; }
        .traveler-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .qty-controls { display: flex; align-items: center; gap: 15px; }
        .qty-btn { width: 32px; height: 32px; border-radius: 50%; border: 1px solid #0062E3; background: white; color: #0062E3; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .cabin-select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-weight: 600; margin-top: 10px; }

        .search-btn { background: var(--secondary); border: none; height: 45px; width: 45px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .show { display: block !important; }

        /* Recommendations */
        .recommendations-section { max-width: 1250px; margin: 0 auto 80px; padding: 0 25px; }
        .rec-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .rec-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: 0.3s; cursor: pointer; border: 1px solid #efefef; text-decoration: none; color: inherit; }
        .rec-card:hover { transform: translateY(-5px); }
        .rec-img { width: 100%; height: 160px; object-fit: cover; }
        .rec-body { padding: 15px; }
        .rec-dest { font-size: 18px; margin: 5px 0; color: #333; font-weight: 700; display: flex; align-items: center; gap: 8px; }

        .suggest-results { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 3000; border: 1px solid #eee; margin-top: 5px; max-height: 250px; overflow-y: auto; }
        .suggest-item { padding: 12px 15px; cursor: pointer; font-size: 14px; border-bottom: 1px solid #f8fafc; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="home.php" class="logo">Cheapflights <i class="fa-solid fa-plane"></i></a>
    <div>
        <a href="my_bookings.php" style="margin-right:20px; text-decoration:none; color:#64748b; font-weight:600;">My Bookings</a>
        <a href="logout.php" class="logout-link">Logout</a>
    </div>
</div>

<div class="hero">
    <h1>Ready for your next adventure?</h1>
    <p>Fly from Cebu to your favorite destinations.</p>
</div>

<div class="search-container">
    <form action="results.php" method="GET" autocomplete="off">
        <div class="trip-type-container">
            <label class="trip-type-label active" id="roundLabel"><input type="radio" name="trip_type" value="round" checked onclick="updateTripType('round')"> Round-trip</label>
            <label class="trip-type-label" id="oneLabel"><input type="radio" name="trip_type" value="one-way" onclick="updateTripType('one')"> One-way</label>
        </div>

        <div class="search-grid">
            <div class="input-group">
                <label>FROM</label>
                <i class="fa-solid fa-location-dot"></i>
                <div class="field-box" style="background:#f1f5f9; cursor:not-allowed;">Cebu (CEB)</div>
                <input type="hidden" name="origin" value="Cebu">
            </div>

            <div class="input-group">
                <label>TO</label>
                <i class="fa-solid fa-plane-arrival"></i>
                <input type="text" id="destInput" name="destination" class="field-box" placeholder="Where to?" required onfocus="showAll()" onkeyup="filterDest()">
                <div id="destSuggest" class="suggest-results"></div>
            </div>

            <div class="input-group">
                <label id="dateLabel">DEPARTURE - RETURN</label>
                <i class="fa-solid fa-calendar-days"></i>
                <input type="text" id="flightDates" name="dates" class="field-box" placeholder="Dates" required>
            </div>

            <div class="input-group">
                <label>TRAVELERS</label>
                <i class="fa-solid fa-user"></i>
                <div class="field-box" id="travelerTrigger"><span id="summaryText">1 adult, Economy</span></div>
                
                <div class="dropdown-content" id="travelerMenu">
                    <p style="font-size: 12px; color: #666; margin-bottom: 15px;">Please select travelers</p>
                    <div class="traveler-row">
                        <div><span>Adults</span><br><small>12+ years</small></div>
                        <div class="qty-controls">
                            <button type="button" class="qty-btn" onclick="updateQty('adults', -1)">-</button>
                            <span id="adultsQty">1</span>
                            <button type="button" class="qty-btn" onclick="updateQty('adults', 1)">+</button>
                        </div>
                    </div>
                    <div class="traveler-row">
                        <div><span>Children</span><br><small>2-11 years</small></div>
                        <div class="qty-controls">
                            <button type="button" class="qty-btn" onclick="updateQty('children', -1)">-</button>
                            <span id="childrenQty">0</span>
                            <button type="button" class="qty-btn" onclick="updateQty('children', 1)">+</button>
                        </div>
                    </div>
                    <select class="cabin-select" id="cabinClass" name="class" onchange="updateSummary()">
                        <option value="Economy">Economy</option>
                        <option value="Business">Business</option>
                        <option value="First class">First class</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
        <input type="hidden" name="adults" id="adultsInput" value="1">
    </form>
</div>

<div class="recommendations-section">
    <h2 style="font-size: 20px; font-weight: 700; margin-bottom: 25px;">Exclusive Flight Recommendations</h2>
    <div class="rec-grid">
        <?php
        $recs = [
            ['from' => 'Cebu', 'to' => 'Manila', 'price' => '1,137'], 
            ['from' => 'Cebu', 'to' => 'Davao', 'price' => '1,060'], 
            ['from' => 'Cebu', 'to' => 'Boracay', 'price' => '1,522'],
            ['from' => 'Cebu', 'to' => 'Iloilo', 'price' => '1,111'], 
            ['from' => 'Cebu', 'to' => 'Siargao', 'price' => '3,200'],
            ['from' => 'Cebu', 'to' => 'Palawan', 'price' => '1,890'],
            ['from' => 'Cebu', 'to' => 'Bacolod', 'price' => '1,137']
        ];
        foreach ($recs as $r): ?>
            <a href="results.php?origin=<?php echo urlencode($r['from']); ?>&destination=<?php echo urlencode($r['to']); ?>&trip_type=round" class="rec-card">
                <img src="<?php echo getCityImage($r['to']); ?>" class="rec-img">
                <div class="rec-body">
                    <p style="font-size: 12px; color: #888; margin: 0;"><?php echo $r['from']; ?></p>
                    <div class="rec-dest"><i class="fa-solid fa-plane" style="font-size: 14px; color: #ccc;"></i> <?php echo $r['to']; ?></div>
                    <p style="font-size: 13px; color: #666; margin: 0;">Start from <strong>₱<?php echo $r['price']; ?></strong></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
    const destinations = ["Manila (MNL)", "Davao (DVO)", "Boracay (MPH)", "Siargao (IAO)", "Palawan (PPS)", "Bohol (TAG)", "Iloilo (ILO)", "Bacolod (BCD)"];
    
    let calendar = flatpickr("#flightDates", { mode: "range", minDate: "today", altInput: true, altFormat: "M j" });

    function updateTripType(type) {
        document.getElementById('roundLabel').classList.toggle('active', type === 'round');
        document.getElementById('oneLabel').classList.toggle('active', type === 'one');
        document.getElementById('dateLabel').innerText = type === 'round' ? "DEPARTURE - RETURN" : "DEPARTURE";
        calendar.set("mode", type === 'round' ? "range" : "single");
    }

    const dInput = document.getElementById('destInput');
    const dSug = document.getElementById('destSuggest');
    function showAll() { render(destinations); dSug.style.display = 'block'; }
    function filterDest() {
        const val = dInput.value.toLowerCase();
        const filtered = destinations.filter(d => d.toLowerCase().includes(val));
        render(filtered);
        dSug.style.display = filtered.length > 0 ? 'block' : 'none';
    }
    function render(list) {
        dSug.innerHTML = '';
        list.forEach(d => {
            const div = document.createElement('div');
            div.className = 'suggest-item'; div.innerText = d;
            div.onclick = () => { dInput.value = d; dSug.style.display = 'none'; };
            dSug.appendChild(div);
        });
    }

    const trigger = document.getElementById('travelerTrigger');
    const menu = document.getElementById('travelerMenu');
    trigger.onclick = (e) => { e.stopPropagation(); menu.classList.toggle('show'); };
    window.onclick = (e) => { 
        if(!menu.contains(e.target) && e.target !== trigger) menu.classList.remove('show'); 
        if(!dInput.contains(e.target)) dSug.style.display = 'none';
    };

    const counts = { adults: 1, children: 0 };
    function updateQty(type, change) {
        if (type === 'adults' && counts[type] + change < 1) return;
        if (counts[type] + change < 0) return;
        counts[type] += change;
        document.getElementById(type + 'Qty').innerText = counts[type];
        updateSummary();
    }
    function updateSummary() {
        let total = counts.adults + counts.children;
        let cabin = document.getElementById('cabinClass').value;
        document.getElementById('summaryText').innerText = `${total} traveler${total>1?'s':''}, ${cabin}`;
        document.getElementById('adultsInput').value = total;
    }
</script>
</body>
</html>