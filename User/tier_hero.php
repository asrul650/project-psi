<?php
require_once '../includes/db_connect.php';

// Ambil semua hero dari database
$sql = "SELECT id, name, role, lane, tier, image_path FROM heroes ORDER BY name ASC";
$result = $conn->query($sql);

$heroes_by_tier = [
    'SS' => [], 'S' => [], 'A' => [], 'B' => [], 'C' => [], 'D' => []
];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        if (array_key_exists($row['tier'], $heroes_by_tier)) {
            $heroes_by_tier[$row['tier']][] = $row;
        }
    }
}

// Urutan tier untuk ditampilkan
$tier_order = ['SS', 'S', 'A', 'B', 'C', 'D'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hero Tier List - Mobile Legends Guide</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .tier-list-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background: #10141a;
            border-radius: 8px;
        }
        .tier-section {
            margin-bottom: 40px;
            background: #181c24;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            border-left: 5px solid;
        }
        .tier-SS { border-color: #ff4545; }
        .tier-S { border-color: #ff9f45; }
        .tier-A { border-color: #45a2ff; }
        .tier-B { border-color: #2ed573; }
        .tier-C { border-color: #a0a0a0; }
        .tier-D { border-color: #6a6a6a; }

        .tier-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            cursor: pointer;
            user-select: none;
        }
        .tier-header .fa-chevron-down {
            margin-left: auto;
            font-size: 1.3em;
            transition: transform 0.2s;
        }
        .tier-header.collapsed .fa-chevron-down {
            transform: rotate(-90deg);
        }
        .tier-badge {
            font-size: 2.5rem;
            font-weight: bold;
            color: #fff;
            padding: 5px 20px;
            border-radius: 8px;
            min-width: 80px;
            text-align: center;
        }
        .tier-badge-SS { background-color: #ff4545; }
        .tier-badge-S { background-color: #ff9f45; }
        .tier-badge-A { background-color: #45a2ff; }
        .tier-badge-B { background-color: #2ed573; }
        .tier-badge-C { background-color: #a0a0a0; }
        .tier-badge-D { background-color: #6a6a6a; }

        .tier-description { color: #bfc8e2; font-size: 1rem; }
        .hero-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 20px;
            transition: max-height 0.3s, opacity 0.3s;
            overflow: hidden;
        }
        .hero-grid.collapsed {
            max-height: 0;
            opacity: 0;
            pointer-events: none;
            padding: 0;
            margin: 0;
        }
        .hero-tier-card {
            background: #232b4a;
            border-radius: 8px;
            overflow: hidden;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: white;
            cursor: pointer;
            pointer-events: auto;
        }
        .hero-tier-card * {
            pointer-events: none;
        }
        .hero-tier-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }
        .hero-tier-card img {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }
        .hero-tier-card .hero-name {
            padding: 10px 5px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header>
        <h1>Mobile Legends Guide</h1>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-toggle-label"><span></span></label>
        <nav>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="hero.php">Hero</a></li>
                <li><a href="Item.php">Item</a></li>
                <li><a href="#">Build</a></li>
                <li class="active"><a href="tier_hero.php">Tier Hero</a></li>
                <li><a href="../includes/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main style="background:#10141a;min-height:100vh;">
        <div class="tier-list-container">
            <h2 style="color:white;text-align:center;margin-bottom:30px;">Hero Tier List</h2>
            <?php foreach ($tier_order as $tier): ?>
                <?php if (!empty($heroes_by_tier[$tier])): ?>
                    <section class="tier-section tier-<?php echo $tier; ?>">
                        <div class="tier-header" onclick="toggleTierGrid(this)">
                            <span class="tier-badge tier-badge-<?php echo $tier; ?>"><?php echo $tier; ?></span>
                            <p class="tier-description">Heroes in this tier have a very high impact and are often picked or banned.</p>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="hero-grid">
                            <?php foreach ($heroes_by_tier[$tier] as $hero): ?>
                                <a href="detailhero.php?id=<?= $hero['id'] ?>" class="hero-tier-card">
                                    <img src="../<?= htmlspecialchars($hero['image_path']) ?>" alt="<?= htmlspecialchars($hero['name']) ?>">
                                    <div class="hero-name"><?= htmlspecialchars($hero['name']) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-section about">
                <h4>Tentang Kami</h4>
                <p>Mobile Legends Guide adalah sumber daya lengkap untuk pemain Mobile Legends, menyediakan informasi hero, build item optimal, panduan emblem, counter, synergy, dan tier list terbaru untuk membantu Anda meningkatkan performa di Land of Dawn.</p>
            </div>
            <div class="footer-section contact">
                <h4>Kontak</h4>
                <p><i class="fas fa-envelope"></i> info@mlguide.com</p>
                <p><i class="fas fa-phone"></i> +62 812 3456 7890</p>
                <p><i class="fas fa-map-marker-alt"></i> Land of Dawn, Indonesia</p>
            </div>
            <div class="footer-section social">
                <h4>Ikuti Kami</h4>
                <div class="social-icons">
                    <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Mobile Legends Guide. All rights reserved.</p>
        </div>
    </footer>

    <script>
    function toggleTierGrid(header) {
        const grid = header.nextElementSibling;
        header.classList.toggle('collapsed');
        grid.classList.toggle('collapsed');
    }
    </script>
    <script src="../js/script.js"></script>
</body>
</html> 