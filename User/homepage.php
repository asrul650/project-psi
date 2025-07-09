<?php
session_start();
if (!isset($_SESSION['user_id_user'])) {
    header('Location: ../includes/auth.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobile Legends Guide - Home User</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/herocard.css">
    <!--<link rel="stylesheet" href="../css/homepage.css">-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    body {
        overflow-x: hidden;
    }
    .spotlight-card {
        box-sizing: border-box;
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        background: #232b4a;
        min-height: 420px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        width: 370px;
    }
    .spotlight-img-wrap {
        position: relative;
        width: 100%;
        height: 100%;
    }
    .spotlight-card img {
        width: 100%;
        height: 370px;
        object-fit: cover;
        display: block;
    }
    .spotlight-info {
        position: absolute;
        bottom: 0;
        left: 0; right: 0;
        background: linear-gradient(0deg, rgba(20,24,40,0.92) 80%, rgba(20,24,40,0.2) 100%);
        color: #fff;
        padding: 28px 22px 22px 22px;
    }
    .role-tag {
        display: inline-block;
        background: #45a2ff;
        color: #fff;
        font-size: 0.95em;
        border-radius: 6px;
        padding: 2px 10px;
        margin-bottom: 8px;
    }
    .btn-detail {
        display: inline-block;
        margin-top: 12px;
        background: #ff9f45;
        color: #232b4a;
        font-weight: bold;
        border-radius: 8px;
        padding: 8px 18px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-detail:hover {
        background: #ff4545;
        color: #fff;
    }
    .hero-spotlight h3 {
        text-align: center;
        color: #e6d6ff;
        font-size: 2.5rem;
        font-family: 'Oswald', sans-serif;
        margin-top: 32px;
        margin-bottom: 18px;
        letter-spacing: 1px;
        text-shadow: 0 2px 12px rgba(162,89,247,0.12);
    }
    .spotlight-grid {
        display: flex;
        gap: 32px;
        justify-content: center;
        flex-wrap: wrap;
        margin: 32px 0 0 0;
        max-width: 100vw;
        padding: 0 16px;
        box-sizing: border-box;
    }
    @media (max-width: 1100px) {
        .spotlight-grid {
            gap: 18px;
        }
        .spotlight-card {
            width: 95vw;
            max-width: 370px;
        }
    }
    @media (max-width: 700px) {
        .spotlight-grid {
            flex-direction: column;
            align-items: center;
        }
        .spotlight-card {
            width: 98vw;
            max-width: 370px;
        }
        .spotlight-card img {
            height: 220px;
        }
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
                <li><a href="Item.php">Item</a></li>
                <li><a href="build.php">Build</a></li>
                <li><a href="tier_hero.php">Tier Hero</a></li>
                <li><a href="../includes/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="hero-banner">
        <div class="hero-content">
            <h2>Halo, <?php echo htmlspecialchars($_SESSION['username_user']); ?>!</h2>
            <p>Selamat datang kembali di portal Mobile Legends Guide.</p>
            <a href="#" class="btn-explore">Lihat Profil Akun</a>
        </div>
    </section>

    <main class="landing-page">
        <section class="features">
            <h3>Fitur Unggulan</h3>
            <div class="feature-grid">
                <div class="feature-item">
                    <h4>Tier List Terupdate</h4>
                    <p>Lihat ranking hero terbaru berdasarkan meta dan statistik.</p>
                </div>
                <div class="feature-item">
                    <h4>Build & Guide Lengkap</h4>
                    <p>Rekomendasi build item, emblem, dan strategi terbaik untuk setiap hero.</p>
                </div>
                <div class="feature-item">
                    <h4>Counter Hero</h4>
                    <p>Cari tahu cara meng-counter hero lawan dengan mudah dan efektif.</p>
                </div>
                <div class="feature-item">
                    <h4>Chatbot AI</h4>
                    <p>Dapatkan bantuan, tanya tips, atau konsultasi strategi dengan chatbot AI 24 jam!</p>
                </div>
            </div>
        </section>
    </main>

    <section class="hero-spotlight">
        <h3>Pahlawan Unggulan</h3>
        <div class="spotlight-grid">
            <!--  <div class="spotlight-card">
                <div class="spotlight-img-wrap">
                    <img src="../images/HERO/Fighter/Kalea/kalea.png" alt="Kalea">
                    <div class="spotlight-info">
                        <h4>Kalea</h4>
                        <span class="role-tag">Support/Fighter</span>
                        <p>Support/Fighter yang sedang naik daun di meta terbaru! Memiliki kemampuan heal dan crowd control yang kuat.</p>
                        <a href="detailhero.php?id=1" class="btn-detail">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="spotlight-card">
                <div class="spotlight-img-wrap">
                    <img src="../images/HERO/Mage/Lunox/lunoxx.png" alt="Lunox">
                    <div class="spotlight-info">
                        <h4>Lunox</h4>
                        <span class="role-tag">Mage</span>
                        <p>Mage spesialis burst damage dan fleksibilitas mode cahaya/gelap, sangat efektif di mid lane.</p>
                        <a href="detailhero.php?id=2" class="btn-detail">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <div class="spotlight-card">
                <div class="spotlight-img-wrap">
                    <img src="../images/HERO/Support/Diggie/Diggie.png" alt="Diggie">
                    <div class="spotlight-info">
                        <h4>Diggie</h4>
                        <span class="role-tag">Support</span>
                        <p>Support spesialis anti crowd control, sangat efektif melindungi tim dari efek stun dan lock musuh.</p>
                        <a href="detailhero.php?id=3" class="btn-detail">Lihat Detail</a>
                    </div>
                </div>
            </div>  -->

            <div class="hero-card">
            <img src="../images/HERO/Fighter/Kalea/kalea.png" alt="Kalea" class="hero-image">
            <div class="hero-overlay">
                <div class="hero-header">
                    <h2 class="hero-name">Kalea</h2>
                    <span class="hero-role">Support / Fighter</span>
                </div>
                <p class="hero-description">
                   Support/Fighter yang sedang naik daun di meta terbaru! Memiliki kemampuan heal dan crowd control yang kuat.
                </p>
                <a href="detailhero.php?id=1" class="hero-detail-link">
                    Lihat detail hero >>
                </a>
            </div>
        </div>
        <div class="hero-card">
            <img src="../images/HERO/Mage/Lunox/lunoxx.png" alt="Lunox" class="hero-image">
            <div class="hero-overlay">
                <div class="hero-header">
                    <h2 class="hero-name">Lunox</h2>
                    <span class="hero-role">Mage</span>
                </div>
                <p class="hero-description">
                Mage spesialis burst damage dan fleksibilitas mode cahaya/gelap, sangat efektif di mid lane.
                </p>
                <a href="detailhero.php?id=2" class="hero-detail-link">
                    Lihat detail hero >>
                </a>
            </div>
        </div>
        <div class="hero-card">
            <img src="../images/HERO/Support/Diggie/Diggie.png" alt="Diggie" class="hero-image">
            <div class="hero-overlay">
                <div class="hero-header">
                    <h2 class="hero-name">Diggie</h2>
                    <span class="hero-role">Support</span>
                </div>
                <p class="hero-description">
                   Support spesialis anti crowd control, sangat efektif melindungi tim dari efek stun dan lock musuh.
                </p>
                <a href="detailhero.php?id=3" class="hero-detail-link">
                    Lihat detail hero >>
                </a>
            </div>
        </div>

        </div>
    </section>

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