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

// Fungsi untuk mengambil urutan linear komponen resep (dari komponen dasar ke item jadi)
function getRecipeLinear($conn, $item_id) {
    $stmt = $conn->prepare('SELECT i.id, i.name, i.image_path FROM item_recipes ir JOIN items i ON ir.component_item_id=i.id WHERE ir.item_id=?');
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $components = [];
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            // Cek apakah komponen ini punya komponen lagi (rekursif)
            $sub = getRecipeLinear($conn, $row['id']);
            if ($sub) {
                foreach ($sub as $s) $components[] = $s;
            } else {
                $components[] = $row;
            }
        }
    }
    $stmt->close();
    return $components;
}
$recipe_linear = getRecipeLinear($conn, $id);

// Data tips & kecocokan item (contoh untuk Malefic Gun)
$item_tips = [
    'Malefic Gun' => [
        'tips' => 'Paling cocok diperlengkapi oleh Marksman untuk meningkatkan Jangkauan Serangan mereka untuk kiting. Efektif melawan lawan dengan armor tinggi karena pasif Armor Buster. Hanya signifikan untuk hero dengan serangan dasar jarak jauh.',
        'desc' => 'Malefic Gun merupakan item terbaik bagi Marksman untuk meningkatkan Jangkauan Serangan Dasar mereka untuk kiting sekaligus menghasilkan damage tinggi terhadap musuhnya. Item ini hanya cocok untuk marksman karena hanya akan memberikan efek yang signifikan jika digunakan oleh mereka. Namun ada beberapa hero seperti Aulus & Freya dapat menggunakannya karena Jangkauan Serangan Dasarnya yang jauh. Ia juga memberikan kecepatan serangan dan kecepatan gerak, yang merupakan apa yang dibutuhkan seorang Marksman. Ia juga efektif melawan lawan dengan pertahanan fisik tinggi karena pasif "Armor Buster" yang memberikan persentase penetrasi fisik.',
        'synergy' => [
            ['name' => 'Wind of Nature', 'img' => '../images/ITEM/Attack/17. Wind of Nature/Wind_of_Nature.webp'],
            ['name' => 'Rose Gold Meteor', 'img' => '../images/ITEM/Attack/4. Rose Gold Meteor/Rose_Gold_Meteor.webp'],
        ],
        'counter' => [
            ['name' => 'Dominance Ice', 'img' => '../images/ITEM/Defense/6. Dominance Ice/Dominance_Ice.webp'],
        ],
        'heroes' => [
            ['name' => 'Layla', 'img' => '../images/HERO/Marksman/Layla/Layla.png'],
            ['name' => 'Miya', 'img' => '../images/HERO/Marksman/Miya/Miya.png'],
            ['name' => 'Hanabi', 'img' => '../images/HERO/Marksman/Hanabi/Hanabi.png'],
            ['name' => 'Aulus', 'img' => '../images/HERO/Fighter/Aulus/Aulus.png'],
            ['name' => 'Freya', 'img' => '../images/HERO/Fighter/Freya/Freya.png'],
        ],
        'note' => 'Jika Malefic Roar dibeli, JANGAN membeli Malefic Gun. Pasif "Armor Buster" tidak dapat ditumpuk dan efeknya tidak signifikan jika dikombinasikan.'
    ],
    // Tambahkan item lain di sini
];
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
            padding: 28px 12px 18px 12px;
            min-width: 220px;
            max-width: 320px;
            margin-left: auto;
            box-shadow: 0 0 0 2px #232b4a;
        }
        .tree-level {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-bottom: 16px;
            position: relative;
        }
        .tree-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #232b4a;
            border-radius: 50%;
            padding: 5px;
            width: 60px;
            height: 60px;
            box-shadow: 0 2px 8px #0003;
            position: relative;
            margin: 0 5px;
            transition: box-shadow 0.2s, border 0.2s;
            border: 2px solid #232b4a;
        }
        .tree-item:hover {
            box-shadow: 0 4px 12px #00bfff44;
            border: 2px solid #00bfff;
        }
        .tree-item img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-bottom: 0;
            display: block;
        }
        .tree-item span { display: none; }
        .tree-connector {
            display: none;
        }
        .item-recipe-title {
            color: #ffe600;
            font-size: 1.3rem;
            margin-bottom: 18px;
            text-align: center;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
        }
        @media (max-width: 600px) {
            .item-recipe-tree { max-width: 98vw; min-width: 0; padding: 16px 2vw; }
            .tree-item { width: 44px; height: 44px; }
            .tree-item img { width: 34px; height: 34px; }
            .tree-level { gap: 8px; margin-bottom: 10px; }
        }
        .item-recipe-horizontal {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 18px;
            margin: 32px 0 0 0;
            padding: 18px 0 0 0;
            border-top: 1.5px solid #232b4a;
            overflow-x: auto;
            scrollbar-width: thin;
            scrollbar-color: #232b4a #181c23;
            flex-wrap: nowrap;
        }
        .recipe-node {
            background: #232b4a;
            border-radius: 50%;
            width: 54px;
            height: 54px;
            min-width: 54px;
            max-width: 54px;
            min-height: 54px;
            max-height: 54px;
            aspect-ratio: 1/1;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px #0003;
            border: 2px solid #232b4a;
            position: relative;
            flex-shrink: 0;
        }
        .recipe-node img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: block;
        }
        .recipe-arrow {
            font-size: 1.8rem;
            color: #00bfff;
            margin: 0 2px;
            user-select: none;
        }
        @media (max-width: 600px) {
            .item-recipe-horizontal { gap: 8px; padding: 10px 0 0 0; }
            .recipe-node { width: 36px; height: 36px; min-width: 36px; max-width: 36px; min-height: 36px; max-height: 36px; aspect-ratio: 1/1; }
            .recipe-node img { width: 24px; height: 24px; }
            .recipe-arrow { font-size: 1.1rem; }
        }
        .item-tips-panel {
            flex:1.2;
            background: #232b4a;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.13), 0 0 0 2px #00bfff33;
            border: 1.5px solid #232b4a;
            padding: 32px 28px 24px 28px;
            margin-left: 36px;
            min-width: 320px;
            max-width: 420px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }
        .tips-title {
            color: #ffe600;
            font-size: 1.35rem;
            font-family: 'Oswald', sans-serif;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
            text-align: left;
            border-left: 4px solid #00bfff;
            padding-left: 12px;
        }
        .tips-short {
            color: #b0d0ff;
            font-size: 1.08rem;
            font-style: italic;
            margin-bottom: 8px;
            padding-left: 8px;
        }
        .tips-desc {
            color: #e0e6f7;
            font-size: 1.01rem;
            margin-bottom: 12px;
            padding-left: 8px;
        }
        .tips-section {
            color: #00bfff;
            font-size: 1.08rem;
            margin-top: 12px;
            margin-bottom: 2px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 7px;
        }
        .tips-section i {
            color: #ffe600;
            font-size: 1.1em;
            margin-right: 2px;
        }
        .tips-hero-list, .tips-item-list {
            display: flex;
            gap: 12px;
            margin-top: 8px;
            flex-wrap: wrap;
        }
        .tips-hero, .tips-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
        }
        .tips-hero img, .tips-item img {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #181c23;
            box-shadow: 0 2px 8px #00bfff22;
            border: 2px solid #232b4a;
            transition: box-shadow 0.2s, border 0.2s;
        }
        .tips-hero img:hover, .tips-item img:hover {
            box-shadow: 0 4px 16px #00bfff66;
            border: 2px solid #00bfff;
        }
        .tips-hero-label, .tips-item-label {
            color: #b0d0ff;
            font-size: 0.92rem;
            margin-top: 2px;
            text-align: center;
            max-width: 60px;
            word-break: break-word;
        }
        .tips-note {
            color: #232b4a;
            font-size: 0.99rem;
            margin-top: 14px;
            background: #ffe600;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 600;
            box-shadow: 0 2px 8px #ffe60033;
        }
        @media (max-width: 900px) {
            .item-detail-section { flex-direction: column; gap: 24px; }
            .item-tips-panel { margin-left: 0; max-width: 98vw; min-width: 0; }
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
    <div class="item-detail-section" style="display:flex;gap:40px;align-items:flex-start;">
        <div style="flex:1;max-width:420px;">
            <div class="item-image">
                <img src="<?php echo htmlspecialchars($img ?: '../images/wallpaper.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
            </div>
            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
            <div class="item-attr"><?php echo isset($item['attr']) ? htmlspecialchars($item['attr']) : ''; ?></div>
            <div class="item-desc"><?php echo nl2br(htmlspecialchars($item['description'])); ?></div>
            <?php if (!empty($recipe_linear)): ?>
            <div class="item-recipe-horizontal">
                <?php foreach ($recipe_linear as $i => $c):
                    $imgc = $c['image_path'] ?? '';
                    if ($imgc && strpos($imgc, '../') !== 0) $imgc = '../' . $imgc;
                ?>
                    <div class="recipe-node"><img src="<?php echo htmlspecialchars($imgc ?: '../images/wallpaper.jpg'); ?>" alt=""></div>
                    <span class="recipe-arrow">&#8594;</span>
                <?php endforeach; ?>
                <div class="recipe-node"><img src="<?php echo htmlspecialchars($img ?: '../images/wallpaper.jpg'); ?>" alt=""></div>
            </div>
            <?php endif; ?>
        </div>
        <div class="item-tips-panel">
            <?php if (isset($item_tips[$item['name']])): $tips = $item_tips[$item['name']]; ?>
            <div class="tips-title">Tips & Kecocokan</div>
            <div class="tips-short">"<?php echo htmlspecialchars($tips['tips']); ?>"</div>
            <div class="tips-desc"><?php echo htmlspecialchars($tips['desc']); ?></div>
            <div class="tips-section"><i class="fas fa-user-astronaut"></i>Hero yang Cocok:</div>
            <div class="tips-hero-list">
                <?php foreach ($tips['heroes'] as $h): ?>
                <div class="tips-hero"><img src="<?php echo htmlspecialchars($h['img']); ?>" alt="<?php echo htmlspecialchars($h['name']); ?>" title="<?php echo htmlspecialchars($h['name']); ?>"><div class="tips-hero-label"><?php echo htmlspecialchars($h['name']); ?></div></div>
                <?php endforeach; ?>
            </div>
            <div class="tips-section"><i class="fas fa-link"></i>Item Sinergi:</div>
            <div class="tips-item-list">
                <?php foreach ($tips['synergy'] as $s): ?>
                <div class="tips-item"><img src="<?php echo htmlspecialchars($s['img']); ?>" alt="<?php echo htmlspecialchars($s['name']); ?>" title="<?php echo htmlspecialchars($s['name']); ?>"><div class="tips-item-label"><?php echo htmlspecialchars($s['name']); ?></div></div>
                <?php endforeach; ?>
            </div>
            <div class="tips-section"><i class="fas fa-shield-alt"></i>Item Counter:</div>
            <div class="tips-item-list">
                <?php foreach ($tips['counter'] as $c): ?>
                <div class="tips-item"><img src="<?php echo htmlspecialchars($c['img']); ?>" alt="<?php echo htmlspecialchars($c['name']); ?>" title="<?php echo htmlspecialchars($c['name']); ?>"><div class="tips-item-label"><?php echo htmlspecialchars($c['name']); ?></div></div>
                <?php endforeach; ?>
            </div>
            <div class="tips-note"><b>Catatan:</b> <?php echo htmlspecialchars($tips['note']); ?></div>
            <?php endif; ?>
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