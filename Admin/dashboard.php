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
    $res = $conn->query("SELECT COUNT(*) as total FROM builds WHERE status='pending'");
    if ($res && $row = $res->fetch_assoc()) $total_pending_builds = $row['total'];
}
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
                    <li><a href="#">Manage Counter Picks</a></li>
                    <li><a href="#">Manage Items</a></li>
                    <li><a href="#">Manage Builds</a></li>
                    <li><a href="#">Manage Discussions</a></li>
                    <li><a href="#">Manage Users</a></li>
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
                        <a href="#" class="card-link">View Builds</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 