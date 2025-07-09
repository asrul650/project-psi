<?php
include '../includes/db_connect.php';
include 'admin_header.php';
include 'admin_sidebar.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    if ($username && $password && $role) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $hash, $role);
        if ($stmt->execute()) {
            header('Location: manage_users.php');
            exit;
        } else {
            $error = 'Gagal menambah user.';
        }
    } else {
        $error = 'Semua field wajib diisi!';
    }
}
?>
<div class="admin-content">
    <h2>Tambah User Baru</h2>
    <?php if ($error): ?><div style="color:red;"> <?= $error ?> </div><?php endif; ?>
    <form method="post">
        <label>Username:</label><br>
        <input type="text" name="username" required><br>
        <label>Password:</label><br>
        <input type="password" name="password" required><br>
        <label>Role:</label><br>
        <select name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select><br><br>
        <button type="submit">Tambah</button>
        <a href="manage_users.php">Batal</a>
    </form>
</div>
<?php include 'admin_footer.php'; ?> 