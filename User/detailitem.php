<?php
session_start();
if (!isset($_SESSION['user_id_user'])) {
    header('Location: ../includes/auth.php');
    exit();
}
require_once '../includes/db_connect.php';

// Ambil id item dari GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$item = null;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT * FROM items WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $item = $result->fetch_assoc();
    }
    $stmt->close();
}
if (!$item) {
    echo '<p style="color:red">Item tidak ditemukan.</p>';
    exit();
}
// Ambil komponen resep
$components = [];
$stmt2 = $conn->prepare('SELECT i.id, i.name, i.image_path FROM item_recipes ir JOIN items i ON ir.component_item_id=i.id WHERE ir.item_id=?');
$stmt2->bind_param('i', $id);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2) {
    while ($row = $res2->fetch_assoc()) $components[] = $row;
}
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Item - Mobile Legends Guide</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .item-detail-section {
            background: #181c23;
            border-radius: 10px;
            margin: 30px auto 0 auto;
            padding: 30px 30px 40px 30px;
            max-width: 1100px;
            box-shadow: 0 0 0 2px #00bfff;
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }
        .item-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #222;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.5);
        }
        .item-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .item-info {
            flex: 1;
        }
        .item-info h2 {
            color: #ffe600;
            font-size: 2.3rem;
            margin-bottom: 10px;
        }
        .item-recipe {
            margin-top: 30px;
        }
        .item-recipe-title {
            color: #00bfff;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .item-recipe-list {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
        }
        .item-recipe-list .recipe-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #232b4a;
            border-radius: 8px;
            padding: 10px 14px;
        }
        .item-recipe-list .recipe-item img {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            margin-bottom: 6px;
        }
        .item-recipe-list .recipe-item span {
            color: #fff;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>Mobile Legends Guide</h1>
        <input type="checkbox" id="nav-toggle" class="nav-toggle">
        <label for="nav-toggle" class="nav-toggle-label">
            <span></span>
        </label>
        <nav>
            <ul>
                <li><a href="homepage.php">Home</a></li>
                <li><a href="hero.php">Hero</a></li>
                <li><a href="Item.php" class="active">Item</a></li>
                <li><a href="#">Build</a></li>
                <li><a href="tier_hero.php">Tier Hero</a></li>
                <li><a href="../includes/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <main>
    <div class="item-detail-section">
        <div class="item-image">
            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
        </div>
        <div class="item-info">
            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
            <div style="color:#fff; margin-bottom:12px; white-space:pre-line;">
                <?php echo nl2br(htmlspecialchars($item['description'])); ?>
            </div>
            <div class="item-recipe">
                <div class="item-recipe-title">Resep Item</div>
                <div class="item-recipe-list">
                    <?php if (empty($components)): ?>
                        <span style="color:#ffb300;">Tidak ada komponen resep.</span>
                    <?php else: ?>
                        <?php foreach ($components as $c): ?>
                            <div class="recipe-item">
                                <img src="<?php echo htmlspecialchars($c['image_path'] ?: '../images/wallpaper.jpg'); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>">
                                <span><?php echo htmlspecialchars($c['name']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
    <script src="../js/script.js"></script>
</body>
</html> 