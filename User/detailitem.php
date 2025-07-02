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

$img = $item['image_path'] ?? '';
if ($img && strpos($img, '../') !== 0) $img = '../' . $img;

// Fungsi rekursif untuk mengambil tree resep
function getRecipeTree($conn, $item_id) {
    $stmt = $conn->prepare('SELECT i.id, i.name, i.image_path FROM item_recipes ir JOIN items i ON ir.component_item_id=i.id WHERE ir.item_id=?');
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $tree = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $row['components'] = getRecipeTree($conn, $row['id']);
            $tree[] = $row;
        }
    }
    $stmt->close();
    return $tree;
}
$recipe_tree = getRecipeTree($conn, $id);
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
            margin-bottom: 18px;
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
        .item-attr {
            color: #fff;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .item-desc {
            color: #fff;
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .item-recipe-tree {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #181c23;
            border-radius: 16px;
            padding: 24px 18px 18px 18px;
            min-width: 320px;
            max-width: 350px;
            margin-left: auto;
            box-shadow: 0 0 0 2px #232b4a;
        }
        .tree-level {
            display: flex;
            justify-content: center;
            gap: 18px;
            margin-bottom: 18px;
            position: relative;
        }
        .tree-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #232b4a;
            border-radius: 50%;
            padding: 10px;
            width: 90px;
            height: 90px;
            box-shadow: 0 2px 8px #0003;
            position: relative;
            margin: 0 10px;
        }
        .tree-item img {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            margin-bottom: 4px;
            display: block;
        }
        .tree-item span {
            color: #fff;
            font-size: 0.98rem;
            text-align: center;
            margin-top: 2px;
            word-break: break-word;
            max-width: 80px;
            white-space: normal;
            line-height: 1.1;
            display: block;
        }
        .tree-connector {
            width: 2px;
            height: 18px;
            background: #00bfff;
            margin: 0 auto;
        }
        .item-recipe-title {
            color: #ffe600;
            font-size: 1.3rem;
            margin-bottom: 18px;
            text-align: center;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
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
        <div style="flex:1;max-width:420px;">
            <div class="item-image">
                <img src="<?php echo htmlspecialchars($img ?: '../images/wallpaper.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
            </div>
            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
            <div class="item-attr"><?php echo isset($item['attr']) ? htmlspecialchars($item['attr']) : ''; ?></div>
            <div class="item-desc"><?php echo nl2br(htmlspecialchars($item['description'])); ?></div>
        </div>
        <div class="item-recipe-tree">
            <div class="item-recipe-title">RECIPE</div>
            <?php
            // Fungsi bantu untuk render tree bertingkat
            function renderRecipeTree($item, $level = 0) {
                $img = $item['image_path'] ?? '';
                if ($img && strpos($img, '../') !== 0) $img = '../' . $img;
                echo '<div class="tree-level">';
                echo '<div class="tree-item">';
                echo '<img src="' . htmlspecialchars($img ?: '../images/wallpaper.jpg') . '" alt="' . htmlspecialchars($item['name']) . '"><span>' . htmlspecialchars($item['name']) . '</span>';
                echo '</div>';
                echo '</div>';
                if (!empty($item['components'])) {
                    echo '<div class="tree-connector"></div>';
                    echo '<div class="tree-level">';
                    foreach ($item['components'] as $c) {
                        renderRecipeTree($c, $level + 1);
                    }
                    echo '</div>';
                }
            }
            // Render tree mulai dari item utama
            $main_item = [
                'name' => $item['name'],
                'image_path' => $item['image_path'],
                'components' => $recipe_tree
            ];
            renderRecipeTree($main_item);
            ?>
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