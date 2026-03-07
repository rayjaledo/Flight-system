<?php 
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Function para sa picture base sa destination
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
    <title>Cheapflights - Booking</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        :root { --primary: #0062E3; --secondary: #FFD200; --bg: #f1f5f9; --dark: #0f172a; --text-muted: #64748b; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--dark); line-height: 1.6; }
        
        .navbar { background: white; padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .logo { font-size: 26px; font-weight: 800; text-decoration: none; color: #00253c; display: flex; align-items: center; gap: 10px; }

        .hero { 
            background: linear-gradient(135deg, rgba(0,98,227,0.7), rgba(0,98,227,0.4)), url('https://wallpaperaccess.com/full/254367.png'); 
            height: 400px; display: flex; flex-direction: column; justify-content: center; align-items: center; color: white; text-align: center; background-size: cover; background-position: center;
        }

        .search-container { max-width: 1250px; width: 95%; background: white; margin: -50px auto 50px; padding: 25px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.12); position: relative; z-index: 1000; }
        .trip-type-container { display: flex; gap: 20px; margin-bottom: 20px; }
        .trip-type-label { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; cursor: pointer; color: var(--text-muted); }
        .trip-type-label.active { color: var(--primary); }

        .search-grid { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; position: relative; }
        .input-group { flex: 1; min-width: 180px; display: flex; flex-direction: column; position: relative; }
        .input-group label { font-size: 11px; font-weight: 700; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase; }
        
        .field-box { 
            padding: 12px 12px 12px 40px; 
            border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-weight: 600; 
            background: #fff; width: 100%; cursor: pointer; display: flex; 
            align-items: center; height: 45px; box-sizing: border-box; outline: none; 
        }

        .input-group i { position: absolute; left: 15px; top: 32px; color: #4b5563; z-index: 10; pointer-events: none; }

        .dropdown-content { display: none; position: absolute; top: 100%; right: 0; width: 320px; background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); padding: 20px; z-index: 2001; border: 1px solid #eee; margin-top: 10px; }
        .traveler-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #f8fafc; }
        .traveler-row:last-of-type { border-bottom: none; }
        .traveler-row span { font-weight: 700; font-size: 14px; color: #1e293b; }
        .traveler-row small { color: var(--text-muted); font-size: 11px; display: block; }
        
        .qty-controls { display: flex; align-items: center; gap: 12px; }
        .qty-btn { width: 32px; height: 32px; border-radius: 50%; border: 1px solid var(--primary); background: white; color: var(--primary); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 20px; transition: 0.2s; }
        .qty-btn:hover { background: var(--primary); color: white; }
        .qty-num { font-weight: 700; min-width: 20px; text-align: center; font-size: 15px; }
        
        .cabin-select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-weight: 600; margin-top: 10px; }

        .search-btn { background: var(--secondary); border: none; height: 45px; width: 45px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .show { display: block !important; }

        .recommendations-section { max-width: 1250px; margin: 0 auto 50px; padding: 0 25px; }
        .rec-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
        .rec-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: 0.3s; cursor: pointer; border: 1px solid #efefef; text-decoration: none; color: inherit; }
        .rec-card:hover { transform: translateY(-5px); }
        .rec-img { width: 100%; height: 160px; object-fit: cover; }
        .rec-body { padding: 15px; }
        .rec-dest { font-size: 18px; margin: 5px 0; color: #333; font-weight: 700; display: flex; align-items: center; gap: 8px; }

        .about-section { max-width: 900px; margin: 80px auto 100px; padding: 0 25px; text-align: center; }
        .about-section h2 { font-size: 28px; font-weight: 700; color: #1a1d1f; margin-bottom: 20px; }
        .about-section p { font-size: 16px; color: #4b5563; line-height: 1.8; margin-bottom: 40px; }
        .info-item h3 { font-size: 22px; font-weight: 700; color: #1a1d1f; margin-bottom: 20px; }
        .info-item ul { list-style: none; padding: 0; display: inline-block; text-align: left; }
        .info-item li { position: relative; padding-left: 25px; margin-bottom: 15px; font-size: 15px; color: #4b5563; }
        .info-item li::before { content: "✓"; position: absolute; left: 0; color: var(--primary); font-weight: bold; }

        .suggest-results { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); z-index: 3000; border: 1px solid #eee; margin-top: 5px; max-height: 250px; overflow-y: auto; }
        .suggest-item { padding: 12px 15px; cursor: pointer; font-size: 14px; border-bottom: 1px solid #f8fafc; }

        /* FOOTER STYLES */
        footer { background-color: #ffffff; padding: 30px 0; border-top: 1px solid #e2e8f0; text-align: center; width: 100%; margin-top: 50px; }
        footer p { color: #64748b; font-size: 14px; margin: 0; font-weight: 500; }
        .footer-links { margin-top: 10px; }
        .footer-links a { color: var(--primary); text-decoration: none; font-size: 13px; margin: 0 10px; transition: 0.2s; }
        .footer-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="home.php" class="logo">Cheapflights ✈</a>
    <div class="nav-links" style="display: flex; align-items: center; gap: 20px;">
        <a href="login.php" style="text-decoration: none; color: #0062E3; font-weight: 700;">Log in</a>
        <a href="signup.php" style="text-decoration: none; color: white; background: #0062E3; padding: 8px 20px; border-radius: 20px; font-weight: 700;">Sign up</a>
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
                <div class="field-box" id="travelerTrigger"><span id="summaryText">1 traveler, Economy</span></div>
                
                <div class="dropdown-content" id="travelerMenu">
                    <p style="font-size: 11px; color: #666; margin-bottom: 15px; font-weight: 700; text-transform: uppercase;">Select Travelers (Max 9)</p>
                    
                    <?php 
                    $pax_types = [
                        ['id'=>'adults', 'label'=>'Adults', 'sub'=>'18+ years', 'default'=>1],
                        ['id'=>'students', 'label'=>'Students', 'sub'=>'over 18', 'default'=>0],
                        ['id'=>'youths', 'label'=>'Youths', 'sub'=>'12-17 years', 'default'=>0],
                        ['id'=>'children', 'label'=>'Children', 'sub'=>'2-11 years', 'default'=>0],
                        ['id'=>'toddlers', 'label'=>'Toddlers', 'sub'=>'in own seat', 'default'=>0],
                        ['id'=>'infants', 'label'=>'Infants', 'sub'=>'on lap', 'default'=>0]
                    ];
                    foreach($pax_types as $p): ?>
                    <div class="traveler-row">
                        <div><span><?php echo $p['label']; ?></span><small><?php echo $p['sub']; ?></small></div>
                        <div class="qty-controls">
                            <button type="button" class="qty-btn" onclick="updateQty('<?php echo $p['id']; ?>', -1)">-</button>
                            <span class="qty-num" id="<?php echo $p['id']; ?>Qty"><?php echo $p['default']; ?></span>
                            <button type="button" class="qty-btn" onclick="updateQty('<?php echo $p['id']; ?>', 1)">+</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    
                    <select class="cabin-select" id="cabinClass" name="cabin" onchange="updateSummary()">
                        <option value="Economy Class">Economy</option>
                        <option value="Premium Economy">Premium Economy</option>
                        <option value="Business Class">Business</option>
                        <option value="First Class">First class</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </div>
        
        <input type="hidden" name="adults" id="adultsInput" value="1">
        <input type="hidden" name="students" id="studentsInput" value="0">
        <input type="hidden" name="youths" id="youthsInput" value="0">
        <input type="hidden" name="children" id="childrenInput" value="0">
        <input type="hidden" name="toddlers" id="toddlersInput" value="0">
        <input type="hidden" name="infants" id="infantsInput" value="0">
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

<div class="about-section">
    <h2>About Cheapflights</h2>
    <p>Cheapflights is your trusted partner for finding convenient and affordable travel options. Through our advanced search engine, we strive to make your planning simple and seamless—from flights. Our service is free for you because we are committed to providing the best value to every traveler. We are proud to be part of your travel stories!</p>

    <div class="info-item">
        <h3>How to get the most out of Cheapflights</h3>
        <ul>
            <li>Use our filtering tools to customize your results based on your budget, schedule, and travel comfort.</li>
            <li>Always check the total price to ensure transparency of all fees before making a purchase.</li>
            <li>Keep in mind that flight prices can change quickly, so it’s best to book immediately when you find a great deal.</li>
        </ul>
    </div>
</div>

<footer>
    <div class="footer-container">
        <p>&copy; 2026 Cheapflights. All rights reserved.</p>
        
    </div>
</footer>

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

    const counts = { adults: 1, students: 0, youths: 0, children: 0, toddlers: 0, infants: 0 };
    
    function updateQty(type, change) {
        let currentTotal = Object.values(counts).reduce((a, b) => a + b, 0);
        if (type === 'adults' && counts[type] + change < 1) return;
        if (counts[type] + change < 0) return;
        if (change > 0 && currentTotal >= 9) {
            alert("Maximum of 9 travelers only.");
            return;
        }
        counts[type] += change;
        document.getElementById(type + 'Qty').innerText = counts[type];
        document.getElementById(type + 'Input').value = counts[type];
        updateSummary();
    }

    function updateSummary() {
        let total = Object.values(counts).reduce((a, b) => a + b, 0);
        let cabinSelect = document.getElementById('cabinClass');
        let cabinText = cabinSelect.options[cabinSelect.selectedIndex].text;
        document.getElementById('summaryText').innerText = `${total} traveler${total > 1 ? 's' : ''}, ${cabinText}`;
    }
</script>
</body>
</html>