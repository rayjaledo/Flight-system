<?php 
session_start();
if (!isset($_SESSION['user'])) { header("Location: login.php"); exit(); }
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlightEase - Modern Booking</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        :root { --primary: #0062E3; --secondary: #FFD200; --bg: #f1f5f9; --dark: #0f172a; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--dark); }
        
        .navbar { background: white; padding: 15px 10%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .logo { 
    font-size: 26px; 
    font-weight: 800; 
    text-decoration: none; 
    color: #00253c; /* Dark Blue text */
    display: flex;
    align-items: center;
    gap: 8px; /* Space tali sa text ug airplane */
    font-family: 'Inter', sans-serif;
}

.logo i {
    font-size: 18px;
    color: #0062E3; /* Blue ang airplane */
    transform: none; /* Siguroha nga straight ra siya */
    display: inline-block;
    /* Kon gusto nimo nga mas taas gamay ang airplane, mahimo nimong butangan og:
    margin-top: -2px; */
}

.logo:hover {
    opacity: 0.8; /* Mo-fade gamay kon itudlo ang mouse */
}

.logo i {
    font-size: 20px;
    transform: rotate(45deg); /* Para murag nag-take off ang airplane */
    margin-top: -2px;
}
        .logout-link { color: #ef4444; text-decoration: none; font-weight: 700; background: #fef2f2; padding: 8px 15px; border-radius: 20px; }

        .hero { 
    /* 1. ILISI ANG URL SA UBOS PARA SA BAG-ONG IMAGE */
    background: linear-gradient(135deg, rgba(0,98,227,0.7), rgba(0,98,227,0.4)), 
                url('https://wallpaperaccess.com/full/254367.png'); 
    
    /* 2. PADAK-AN ANG HEIGHT (Himoang 500px o 600px depende sa imong gusto) */
    height: 500px; 
    
    display: flex; 
    flex-direction: column; 
    justify-content: center; 
    align-items: center; 
    color: white; 
    text-align: center; 
    background-size: cover;
    background-position: center; /* Aron siguradong sa tunga ang focus sa image */
}

        .search-container { max-width: 1250px; width: 95%; background: white; margin: -50px auto 50px; padding: 25px; border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
        
        /* Trip Type Toggle */
        .trip-type-container { display: flex; gap: 20px; margin-bottom: 20px; }
        .trip-type-label { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; cursor: pointer; color: #64748b; }
        .trip-type-label input { accent-color: var(--primary); width: 18px; height: 18px; }
        .trip-type-label.active { color: var(--primary); }

        .search-grid { display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; position: relative; }
        .input-group { flex: 1; min-width: 180px; display: flex; flex-direction: column; position: relative; }
        .input-group label { font-size: 11px; font-weight: 700; color: #64748b; margin-bottom: 5px; text-transform: uppercase; }
        
        .field-box { 
            padding: 12px 12px 12px 40px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; font-weight: 600; 
            background: #fff; width: 100%; cursor: pointer; display: flex; align-items: center; height: 45px; box-sizing: border-box; outline: none;
        }
        .field-box:focus { border-color: var(--primary); }
        .field-box i { position: absolute; left: 15px; color: #94a3b8; }
        
        /* Swap Icon */
        .swap-container { display: flex; align-items: center; justify-content: center; height: 45px; padding-top: 15px; }
        .swap-btn { 
            width: 35px; height: 35px; border-radius: 50%; border: 1px solid #ddd; background: white; 
            display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; z-index: 5;
        }
        .swap-btn:hover { background: #f1f5f9; border-color: var(--primary); color: var(--primary); }

        /* Auto-suggest Dropdown */
        .suggest-results {
            display: none; position: absolute; top: 100%; left: 0; width: 100%;
            background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            z-index: 3000; border: 1px solid #eee; margin-top: 5px; max-height: 250px; overflow-y: auto;
        }
        .suggest-item { padding: 12px 15px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid #f8fafc; }
        .suggest-item:hover { background: #f1f5f9; color: var(--primary); }
        .suggest-item i { color: #94a3b8; font-size: 12px; }

        /* Travelers Dropdown */
        .dropdown-content {
            display: none; position: absolute; top: 100%; right: 0; width: 320px;
            background: white; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 20px; z-index: 2000; border: 1px solid #eee; margin-top: 10px;
        }
        .traveler-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .qty-controls { display: flex; align-items: center; gap: 12px; }
        .qty-btn { width: 30px; height: 30px; border-radius: 50%; border: 1px solid #ddd; background: #fff; cursor: pointer; font-weight: bold; }
        
        .cabin-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 10px; }
        .cabin-btn { padding: 8px; border: 1px solid #ddd; border-radius: 6px; background: white; font-size: 12px; cursor: pointer; text-align: center; }
        .cabin-btn.active { border-color: var(--primary); color: var(--primary); background: #eff6ff; font-weight: bold; }

        .search-btn { background: var(--secondary); border: none; height: 45px; width: 45px; border-radius: 8px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.3s; }
        .search-btn:hover { background: #e6bd00; transform: scale(1.05); }
        .show { display: block !important; }
    </style>
</head>
<body>

<div class="navbar">
    <a href="home.php" class="logo">
    Cheapflights <i class="fa-solid fa-plane"></i>
</a>
    <div class="nav-links">
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
            <label class="trip-type-label active" id="roundLabel">
                <input type="radio" name="trip_type" value="round" checked onclick="updateTripType('round')"> Round-trip
            </label>
            <label class="trip-type-label" id="oneLabel">
                <input type="radio" name="trip_type" value="one-way" onclick="updateTripType('one')"> One-way
            </label>
        </div>

        <div class="search-grid">
            <div class="input-group">
                <label>From</label>
                <div class="field-box" style="background:#f1f5f9; cursor:not-allowed;">
                    <i class="fa-solid fa-location-dot"></i> Cebu (CEB)
                </div>
                <input type="hidden" name="origin" value="Cebu">
            </div>

            <div class="swap-container">
                <div class="swap-btn"><i class="fa-solid fa-right-left"></i></div>
            </div>

            <div class="input-group">
                <label>To</label>
                <i class="fa-solid fa-plane-arrival" style="left:15px; top:32px; position:absolute; z-index:10;"></i>
                <input type="text" id="destInput" name="destination" class="field-box" placeholder="Where to?" required 
                       onfocus="showAllDestinations()" onkeyup="filterDestinations()">
                <div id="destSuggest" class="suggest-results"></div>
            </div>

            <div class="input-group">
                <label id="dateLabel">Departure - Return</label>
                <i class="fa-solid fa-calendar-days" style="left:15px; top:32px; position:absolute; z-index:10;"></i>
                <input type="text" id="flightDates" name="dates" class="field-box" placeholder="Dates" required>
            </div>

            <div class="input-group">
                <label>Travelers</label>
                <div class="field-box" id="travelerTrigger">
                    <i class="fa-solid fa-user"></i> <span id="summaryText">1 adult, Economy</span>
                </div>
                <div class="dropdown-content" id="travelerMenu">
                    <div class="traveler-row">
                        <span>Adults <small>18+</small></span>
                        <div class="qty-controls">
                            <button type="button" class="qty-btn" onclick="updateQty('adults', -1)">-</button>
                            <span id="adultsQty">1</span>
                            <button type="button" class="qty-btn" onclick="updateQty('adults', 1)">+</button>
                        </div>
                    </div>
                    <div class="traveler-row">
                        <span>Children <small>2-11</small></span>
                        <div class="qty-controls">
                            <button type="button" class="qty-btn" onclick="updateQty('children', -1)">-</button>
                            <span id="childrenQty">0</span>
                            <button type="button" class="qty-btn" onclick="updateQty('children', 1)">+</button>
                        </div>
                    </div>
                    <div style="font-size: 11px; font-weight: 700; color: #64748b; margin-top: 15px;">CABIN CLASS</div>
                    <div class="cabin-grid">
                        <button type="button" class="cabin-btn active" onclick="setClass('Economy', this)">Economy</button>
                        <button type="button" class="cabin-btn" onclick="setClass('Business', this)">Business</button>
                        <button type="button" class="cabin-btn" onclick="setClass('First Class', this)">First Class</button>
                    </div>
                </div>
            </div>

            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass" style="color:black; font-size:18px;"></i>
            </button>
        </div>
        
        <input type="hidden" name="adults" id="adultsInput" value="1">
        <input type="hidden" name="class" id="classInput" value="Economy">
    </form>
</div>

<script>
    // IMONG MGA ORIGINAL DESTINATIONS
    const destinations = [
        "Manila (MNL)", "Davao (DVO)", "Boracay (MPH)", "Siargao (IAO)", 
        "Palawan (PPS)", "Bohol (TAG)", "Iloilo (ILO)", "Bacolod (BCD)", 
        "Tacloban (TAC)", "Zamboanga (ZAM)"
    ];

    const destInput = document.getElementById('destInput');
    const destSuggest = document.getElementById('destSuggest');

    function showAllDestinations() {
        renderList(destinations);
        destSuggest.style.display = 'block';
    }

    function filterDestinations() {
        const val = destInput.value.toLowerCase();
        const filtered = destinations.filter(d => d.toLowerCase().includes(val));
        renderList(filtered);
        destSuggest.style.display = filtered.length > 0 ? 'block' : 'none';
    }

    function renderList(list) {
        destSuggest.innerHTML = '';
        list.forEach(d => {
            const div = document.createElement('div');
            div.className = 'suggest-item';
            div.innerHTML = `<i class="fa-solid fa-plane"></i> ${d}`;
            div.onclick = () => {
                destInput.value = d;
                destSuggest.style.display = 'none';
            };
            destSuggest.appendChild(div);
        });
    }

    let calendar = flatpickr("#flightDates", {
        mode: "range", minDate: "today", showMonths: 2, altInput: true, altFormat: "M j"
    });

    function updateTripType(type) {
        document.getElementById('roundLabel').classList.toggle('active', type === 'round');
        document.getElementById('oneLabel').classList.toggle('active', type === 'one');
        document.getElementById('dateLabel').innerText = type === 'round' ? "Departure - Return" : "Departure";
        calendar.set("mode", type === 'round' ? "range" : "single");
    }

    const trigger = document.getElementById('travelerTrigger');
    const menu = document.getElementById('travelerMenu');
    trigger.onclick = (e) => { e.stopPropagation(); menu.classList.toggle('show'); };
    
    window.onclick = (e) => { 
        if(!menu.contains(e.target) && e.target !== trigger) menu.classList.remove('show'); 
        if(!destInput.contains(e.target) && !destSuggest.contains(e.target)) destSuggest.style.display = 'none';
    };

    const counts = { adults: 1, children: 0 };
    function updateQty(type, change) {
        if (type === 'adults' && counts[type] + change < 1) return;
        if (counts[type] + change < 0) return;
        counts[type] += change;
        document.getElementById(type + 'Qty').innerText = counts[type];
        updateSummary();
    }
    function setClass(name, btn) {
        document.getElementById('classInput').value = name;
        document.querySelectorAll('.cabin-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        updateSummary();
    }
    function updateSummary() {
        let total = counts.adults + counts.children;
        let cabin = document.getElementById('classInput').value;
        document.getElementById('summaryText').innerText = `${total} adult${total > 1 ? 's' : ''}, ${cabin}`;
        document.getElementById('adultsInput').value = total;
    }
</script>
</body>
</html>