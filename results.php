<?php 
session_start();
include 'db.php'; 

// 1. Get Destination Data
$raw_dest = isset($_GET['destination']) ? mysqli_real_escape_string($conn, $_GET['destination']) : '';
$dest = trim(explode('(', $raw_dest)[0]);

// 2. Get All Traveler Types (Ayaw ni tangtanga kay importante sa headcount)
$adults   = isset($_GET['adults'])   ? (int)$_GET['adults']   : 1;
$students = isset($_GET['students']) ? (int)$_GET['students'] : 0;
$youths   = isset($_GET['youths'])   ? (int)$_GET['youths']   : 0;
$children = isset($_GET['children']) ? (int)$_GET['children'] : 0;
$toddlers = isset($_GET['toddlers']) ? (int)$_GET['toddlers'] : 0;
$infants  = isset($_GET['infants'])  ? (int)$_GET['infants']  : 0;

$total_travelers = $adults + $students + $youths + $children + $toddlers + $infants;
if($total_travelers < 1) { $adults = 1; $total_travelers = 1; }

$cabin = isset($_GET['cabin']) ? htmlspecialchars($_GET['cabin']) : 'Economy Class';

// 3. SQL Query
$query = "SELECT * FROM flights WHERE destination LIKE '%$dest%' OR origin LIKE '%$dest%' ORDER BY price ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flight Results | CheapFlight</title>
    <link rel="stylesheet" href="">
    <style>
        :root { --primary: #0062E3; --yellow: #FFD200; --bg: #f6f7f9; --text: #1a1d1f; --border: #e6e8ec; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); }

        .search-header { background: #fff; padding: 15px 5%; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 1000; }
        .nav-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; }
        .logo { font-size: 22px; font-weight: 800; text-decoration: none; color: #000; display: flex; align-items: center; gap: 8px; }
        
        .search-controls { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .input-pill { 
            background: #fff; border: 1px solid var(--border); padding: 10px 15px; border-radius: 8px; 
            display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 600;
            min-width: 250px;
        }
        .input-pill input { border: none; outline: none; font-weight: 600; width: 100%; }

        .main-container { display: grid; grid-template-columns: 320px 1fr; gap: 25px; padding: 25px 5%; max-width: 1400px; margin: 0 auto; }
        
        /* SIDEBAR */
        .sidebar-card { background: white; padding: 20px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 15px; }
        .advice-card { border-left: 4px solid #31ba7b; }
        .filter-title { font-size: 14px; font-weight: 800; margin-bottom: 15px; display: block; border-bottom: 1px solid var(--border); padding-bottom: 8px; }

        /* TRAVELER DROPDOWN */
        /* I-apply kini nga style sa duha ka elements */
/* Kini nga rules maghimo sa duha nga parehas og hitsura */
/* 1. I-apply ang parehong style sa duha */
.dropdown-trigger, .filter-select {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background-color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    box-sizing: border-box;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
    appearance: none; /* Magtangtang sa default system arrow */
    
    /* 2. Kini nga code mag-add sa PAREHONG arrow icon sa duha */
    background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='black' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 10px center;
    background-size: 16px;
}
        .dropdown-content { 
            box-sizing: border-box;
            width: 100%;
            display: none; background: #fff; border: 1px solid var(--border); border-radius: 8px; 
            padding: 15px; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .dropdown-content.show { display: block; }
        
        .qty-wrapper { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
        .qty-btns { display: flex; align-items: center; gap: 10px; }
        .q-btn { width: 28px; height: 28px; border-radius: 50%; border: 1px solid var(--border); background: #fff; cursor: pointer; font-weight: bold; }

        /* STOPS FILTER */
        .stop-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 13px; cursor: pointer; }
        .stop-item input { margin-right: 10px; }

        .filter-select { width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); font-weight: 600; margin-bottom: 15px; }
        .filter-btn { width: 100%; background: var(--primary); color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer; }

        /* FLIGHT CARDS (COMPLETE DETAILS) */
        .flight-card { background: white; border-radius: 12px; margin-bottom: 15px; display: grid; grid-template-columns: 1fr 240px; border: 1px solid var(--border); overflow: hidden; }
        .route-details { padding: 25px; }
        .airline-info { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 700; color: #444; }
        
        .timeline-box { display: flex; justify-content: space-between; align-items: center; }
        .time-node b { font-size: 20px; display: block; color: #111; }
        .time-node span { font-size: 12px; color: #64748b; font-weight: 600; }
        
        .line-wrap { flex: 2; text-align: center; padding: 0 20px; position: relative; }
        .line-wrap .hr-line { height: 2px; background: #e2e8f0; margin: 8px 0; position: relative; }
        .line-wrap .hr-line::after { content: '\f579'; font-family: 'Font Awesome 6 Free'; font-weight: 900; position: absolute; right: -5px; top: -8px; color: #cbd5e1; background: white; padding: 0 5px; }

        .price-box { padding: 25px; border-left: 1px solid #f4f5f6; background: #fafbfc; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; }
        .price-box b { font-size: 24px; color: #1a1d1f; }
        .btn-select { background: var(--yellow); border: none; padding: 12px; width: 100%; border-radius: 8px; font-weight: 800; cursor: pointer; text-decoration: none; color: #000; display: block; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>

<div class="search-header">
    <div class="nav-row">
        <a href="home.php" class="logo">CheapFlight ✈</a>
    </div>
    <div style="font-size: 18px; font-weight: 800; color: #1a1d1f; margin-top: -10px; margin-bottom: 10px;">
        Flight Results: <?php echo htmlspecialchars($dest); ?>
    </div>
</div>
    
</div>

<div class="main-container">
    <div class="sidebar">
        <div class="sidebar-card advice-card">
            <b style="color:#31ba7b; font-size:13px; text-transform:uppercase;">Our Advice</b>
            <p style="font-size:12px; margin:8px 0;">Prices for <b><?php echo htmlspecialchars($dest); ?></b> are steady. Book now.</p>
        </div>

        <form action="results.php" method="GET">
            <input type="hidden" name="destination" value="<?php echo htmlspecialchars($raw_dest); ?>">

            <div class="sidebar-card">
                <b class="filter-title">Travelers & Cabin Class</b> 
                <div style="display: flex; gap: 10px; flex-direction: column;">
        
                    <label style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: -5px;">TRAVELERS</label>
                    <div class="dropdown-trigger" onclick="toggleDropdown()">
    <span id="trigger_text"><?php echo $total_travelers; ?> Traveler<?php echo $total_travelers > 1 ? 's' : ''; ?></span>
    </div>

                    <div class="dropdown-content" id="drop_content">
                        <?php
                        $configs = ['adults'=>'Adults','students'=>'Students','youths'=>'Youths','children'=>'Children','toddlers'=>'Toddlers','infants'=>'Infants'];
                        foreach($configs as $key => $label): $val = $$key;
                        ?>
                        <div class="qty-wrapper">
                            <span style="font-size:13px; font-weight:700;"><?php echo $label; ?></span>
                            <div class="qty-btns">
                                <button type="button" class="q-btn" onclick="updateQty('<?php echo $key; ?>', -1)">-</button>
                                <span id="qty_<?php echo $key; ?>" style="font-weight:800; min-width:15px; text-align:center;"><?php echo $val; ?></span>
                                <input type="hidden" name="<?php echo $key; ?>" id="in_<?php echo $key; ?>" value="<?php echo $val; ?>">
                                <button type="button" class="q-btn" onclick="updateQty('<?php echo $key; ?>', 1)">+</button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <label style="font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: -5px;">CABIN CLASS</label>
                    <select name="cabin" class="filter-select">
                        <option value="Economy Class" <?php echo ($cabin == 'Economy Class') ? 'selected' : ''; ?>>Economy (100%)</option>
                        <option value="Premium Economy" <?php echo ($cabin == 'Premium Economy') ? 'selected' : ''; ?>>Premium Economy (125%)</option>
                        <option value="Business Class" <?php echo ($cabin == 'Business Class') ? 'selected' : ''; ?>>Business Class (160%)</option>
                        <option value="First Class" <?php echo ($cabin == 'First Class') ? 'selected' : ''; ?>>First Class (250%)</option>
                    </select>
                    
                    <button type="submit" class="filter-btn">Apply Filters</button>
                </div>
            </div>

            <div class="sidebar-card">
                <b class="filter-title">Stops</b>
                <label class="stop-item">
                    <span><input type="checkbox" name="stops[]" value="0" checked> Nonstop</span>
                    <span style="color:#64748b; font-size:11px;">₱2,854</span>
                </label>
                <label class="stop-item">
                    <span><input type="checkbox" name="stops[]" value="1" checked> 1 stop <b>only</b></span>
                    <span style="color:#64748b; font-size:11px;">₱16,204</span>
                </label>
                <label class="stop-item">
                    <span><input type="checkbox" name="stops[]" value="2" checked> 2+ stops</span>
                </label>
            </div>
        </form>

        <div class="sidebar-card">
            <b style="font-size:13px;">Trip Details</b>
            <p style="font-size:12px; color:#64748b; margin-top:10px;">
                <i class="fa-solid fa-user-group"></i> <?php echo $total_travelers; ?> Pax<br>
                <i class="fa-solid fa-couch" style="margin-top:8px;"></i> <?php echo $cabin; ?>
            </p>
        </div>
    </div>

    <div class="results-list">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                $base_price = $row['price'];
                $multiplier = 1.0; 
                if ($cabin == "Premium Economy") $multiplier = 1.25; 
                elseif ($cabin == "Business Class") $multiplier = 1.60; 
                elseif ($cabin == "First Class") $multiplier = 2.50; 

                $total_price = ($base_price * $multiplier) * $total_travelers;
            ?>
                <div class="flight-card">
                    <div class="route-details">
                        <div class="airline-info">
                            <span><?php echo strtoupper($row['airline']); ?></span>
                            <span style="color:#64748b; font-weight:400;">| <?php echo $cabin; ?></span>
                        </div>
                        <div class="timeline-box">
                            <div class="time-node">
                                <b><?php echo date('H:i', strtotime($row['departure_time'])); ?></b>
                                <span><?php echo $row['origin']; ?></span>
                            </div>
                            <div class="line-wrap">
                                <span style="font-size:11px; font-weight:700; color: #31ba7b;">NONSTOP</span>
                                <div class="hr-line"></div>
                                <span style="font-size:11px; color:gray;"><?php echo date('M d, Y', strtotime($row['flight_date'])); ?></span>
                            </div>
                            <div class="time-node" style="text-align: right;">
                                <b><?php echo isset($row['arrival_time']) ? date('H:i', strtotime($row['arrival_time'])) : '--:--'; ?></b>
                                <span><?php echo $row['destination']; ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="price-box">
                        <b>₱<?php echo number_format($total_price, 2); ?></b>
                        <div style="font-size:11px; color:#64748b; margin-top:5px;">Total for <?php echo $total_travelers; ?> pax</div>
                        <a href="booking_form.php?id=<?php echo $row['id']; ?>&pax=<?php echo $total_travelers; ?>&cabin=<?php echo urlencode($cabin); ?>" class="btn-select">Select Flight</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="sidebar-card" style="text-align:center; padding:50px;">
                <h3>No flights found.</h3>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleDropdown() {
    document.getElementById('drop_content').classList.toggle('show');
}

function updateQty(type, change) {
    const qtySpan = document.getElementById('qty_' + type);
    const hiddenInput = document.getElementById('in_' + type);
    const triggerText = document.getElementById('trigger_text');
    
    let currentQty = parseInt(hiddenInput.value);
    let newQty = currentQty + change;

    if (type === 'adults' && newQty < 1) return;
    if (newQty < 0) return;

    let total = 0;
    ['adults','students','youths','children','toddlers','infants'].forEach(id => {
        total += parseInt(document.getElementById('in_' + id).value);
    });

    if (change > 0 && total >= 9) { alert("Maximum 9 travelers."); return; }

    qtySpan.innerText = newQty;
    hiddenInput.value = newQty;

    let finalTotal = 0;
    ['adults','students','youths','children','toddlers','infants'].forEach(id => {
        finalTotal += parseInt(document.getElementById('in_' + id).value);
    });
    triggerText.innerText = finalTotal + (finalTotal > 1 ? " Travelers" : " Traveler");
}
</script>
</body>
</html>