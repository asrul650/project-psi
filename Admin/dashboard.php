<?php
session_start();

// Cek apakah admin sudah login. Jika tidak, redirect ke halaman login.
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

// Data admin dari session
$admin_username = $_SESSION['username_admin'];

// Koneksi DB
require_once '../includes/db_connect.php';

// Query total heroes
$total_heroes = 0;
$res = $conn->query('SELECT COUNT(*) as total FROM heroes');
if ($res && $row = $res->fetch_assoc()) $total_heroes = $row['total'];

// Query total users
$total_users = 0;
$res = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='user'");
if ($res && $row = $res->fetch_assoc()) $total_users = $row['total'];

// Query pending builds (jika ada tabel builds, jika tidak tampilkan 0)
$total_pending_builds = 0;
if ($conn->query("SHOW TABLES LIKE 'builds'")->num_rows) {
    // Cek apakah kolom 'status' ada di tabel builds
    $col_check = $conn->query("SHOW COLUMNS FROM builds LIKE 'status'");
    if ($col_check && $col_check->num_rows) {
        $res = $conn->query("SELECT COUNT(*) as total FROM builds WHERE status='pending'");
        if ($res && $row = $res->fetch_assoc()) $total_pending_builds = $row['total'];
    } else {
        // Jika tidak ada kolom status, tampilkan 0
        $total_pending_builds = 0;
    }
}

// Query 5 build dengan like terbanyak
$top_builds = [];
$res = $conn->query("SELECT b.id, b.name, h.name AS hero_name, COUNT(bl.id) AS like_count FROM builds b JOIN heroes h ON b.hero_id = h.id LEFT JOIN build_likes bl ON b.id = bl.build_id GROUP BY b.id ORDER BY like_count DESC, b.created_at DESC LIMIT 5");
while ($row = $res && $res->fetch_assoc() ? $row = $res->fetch_assoc() : false) $top_builds[] = $row;

// Query 5 hero yang build-nya paling sering di-like
$top_heroes = [];
$res = $conn->query("SELECT h.id, h.name, COUNT(bl.id) AS total_likes FROM heroes h JOIN builds b ON h.id = b.hero_id LEFT JOIN build_likes bl ON b.id = bl.build_id GROUP BY h.id ORDER BY total_likes DESC, h.name ASC LIMIT 5");
while ($row = $res && $res->fetch_assoc() ? $row = $res->fetch_assoc() : false) $top_heroes[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="manage_heroes.php">Manage Hero Details</a></li>
                    <li><a href="manage_hero_tiers.php">Manage Hero Tiers</a></li>
                    <li><a href="manage_items.php">Manage Items</a></li>
                    <li><a href="manage_builds.php">Manage Builds</a></li>
                    <li><a href="manage_discussions.php">Manage Discussions</a></li>
                    <li><a href="manage_users.php">Manage Users</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="header-title">
                    <h1>Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($admin_username); ?>!</p>
                </div>
            </header>

            <div class="content-body">
                <div class="card-container">
                    <!-- Statistik Card -->
                    <div class="stat-card">
                        <h4>Total Heroes</h4>
                        <p class="stat-number"><?php echo $total_heroes; ?></p>
                        <a href="#" class="card-link">View Heroes</a>
                    </div>

                    <div class="stat-card">
                        <h4>Total Users</h4>
                        <p class="stat-number"><?php echo $total_users; ?></p>
                        <a href="#" class="card-link">View Users</a>
                    </div>

                    <div class="stat-card">
                        <h4>Pending Builds</h4>
                        <p class="stat-number"><?php echo $total_pending_builds; ?></p>
                        <a href="manage_builds.php" class="card-link">View Builds</a>
                    </div>
                </div>

                <div class="card-container" style="margin-top:32px;">
                    <div class="stat-card" style="flex:2;min-width:320px;">
                        <h4>Top 5 Builds by Likes</h4>
                        <table style="width:100%;font-size:0.98em;">
                            <tr><th>Build Name</th><th>Hero</th><th>Likes</th></tr>
                            <?php foreach ($top_builds as $b): ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['name']) ?></td>
                                    <td><?= htmlspecialchars($b['hero_name']) ?></td>
                                    <td><?= $b['like_count'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <div class="stat-card" style="flex:1;min-width:220px;">
                        <h4>Top 5 Heroes by Build Likes</h4>
                        <table style="width:100%;font-size:0.98em;">
                            <tr><th>Hero</th><th>Total Likes</th></tr>
                            <?php foreach ($top_heroes as $h): ?>
                                <tr>
                                    <td><?= htmlspecialchars($h['name']) ?></td>
                                    <td><?= $h['total_likes'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 