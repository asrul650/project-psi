<?php
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}
require_once '../includes/db_connect.php';

// Ambil data user dari database
$sql = "SELECT * FROM users ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .user-table { width:100%; border-collapse:collapse; margin-top:30px; }
        .user-table th, .user-table td { border:1px solid #333; padding:8px 12px; text-align:center; }
        .user-table th { background:#232b4a; color:#fff; }
        .btn { padding:6px 16px; border-radius:6px; border:none; cursor:pointer; font-weight:bold; }
        .btn-add { background:#00bfff; color:#fff; margin-bottom:18px; }
        .btn-edit { background:#ffc107; color:#232b4a; }
        .btn-delete { background:#e53935; color:#fff; }
        .role-badge { padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold; }
        .role-admin { background:#ff6b6b; color:#fff; }
        .role-user { background:#4ecdc4; color:#fff; }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'admin_sidebar.php'; ?>
        <main class="main-content">
            <?php $page_title = 'Manage Users'; include 'admin_header.php'; ?>
            <div class="content-body">
                <a href="add_user.php" class="btn btn-add"><i class="fas fa-plus"></i> Add User</a>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><span class="role-badge role-<?= $row['role'] ?>"><?= ucfirst($row['role']) ?></span></td>
                                <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this user?')"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No users found in the database.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html> 