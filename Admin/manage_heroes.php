<?php
session_start();
require_once '../includes/db_connect.php';

// Cek otorisasi admin
if (!isset($_SESSION['user_id_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}

// Ambil semua data hero dari database, diurutkan berdasarkan nama
$sql = "SELECT * FROM heroes ORDER BY name ASC";
$result = $conn->query($sql);

$admin_username = $_SESSION['username_admin'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Heroes - Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li class="active"><a href="manage_heroes.php">Manage Hero Details</a></li>
                    <li><a href="manage_hero_tiers.php">Manage Hero Tiers</a></li>
                    <li><a href="manage_items.php">Manage Items</a></li>
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
                    <h1>Manage Heroes</h1>
                    <p>Add, edit, or delete hero data.</p>
                </div>
                <div class="header-actions">
                    <a href="add_hero.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Hero</a>
                </div>
            </header>

            <div class="content-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Lane</th>
                                <th>Tier</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($hero = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($hero['id']); ?></td>
                                        <td>
                                            <?php if (!empty($hero['image_path'])): ?>
                                                <img src="../<?php echo htmlspecialchars($hero['image_path']); ?>" alt="<?php echo htmlspecialchars($hero['name']); ?>" class="table-img">
                                            <?php else: ?>
                                                <img src="../assets/images/default_hero.png" alt="Default" class="table-img">
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($hero['name']); ?></td>
                                        <td><?php echo htmlspecialchars($hero['role']); ?></td>
                                        <td><?php echo htmlspecialchars($hero['lane']); ?></td>
                                        <td><span class="tier-badge tier-<?php echo strtolower(htmlspecialchars($hero['tier'])); ?>"><?php echo htmlspecialchars($hero['tier']); ?></span></td>
                                        <td>
                                            <a href="edit_hero.php?id=<?php echo $hero['id']; ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i></a>
                                            <a href="delete_hero.php?id=<?php echo $hero['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this hero?');"><i class="fas fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No heroes found in the database.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 