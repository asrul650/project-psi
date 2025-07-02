<?php
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header("Location: login.php?error=unauthorized");
    exit();
}
require_once '../includes/db_connect.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $type = $_POST['type'];
    $image_path = trim($_POST['image_path']);
    $attr = trim($_POST['attr']);
    $desc = trim($_POST['description']);
    if ($name && $type) {
        $stmt = $conn->prepare("INSERT INTO items (name, type, image_path, attr, description) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $name, $type, $image_path, $attr, $desc);
        if ($stmt->execute()) {
            header('Location: manage_items.php?msg=added');
            exit();
        } else {
            $msg = 'Gagal menambah item.';
        }
    } else {
        $msg = 'Nama dan tipe item wajib diisi!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-box { max-width: 480px; margin: 40px auto; background: #232b4a; border-radius: 12px; padding: 32px 28px; color: #fff; }
        .form-box h2 { margin-bottom: 24px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display:block; margin-bottom:6px; font-weight:600; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:8px 10px; border-radius:6px; border:1px solid #444; background:#181c23; color:#fff; }
        .form-group textarea { min-height: 60px; }
        .btn { padding:8px 24px; border-radius:6px; border:none; background:#00bfff; color:#fff; font-weight:bold; cursor:pointer; }
        .btn-cancel { background:#e53935; margin-left:10px; }
        .msg { color:#ffb300; margin-bottom:12px; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Tambah Item</h2>
        <?php if ($msg): ?><div class="msg"><?php echo $msg; ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Nama Item</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Tipe Item</label>
                <select name="type" required>
                    <option value="">-- Pilih Tipe --</option>
                    <option value="Attack">Attack</option>
                    <option value="Magic">Magic</option>
                    <option value="Defense">Defense</option>
                    <option value="Movement">Movement</option>
                    <option value="Jungle">Jungle</option>
                    <option value="Roaming">Roaming</option>
                </select>
            </div>
            <div class="form-group">
                <label>Path Gambar</label>
                <input type="text" name="image_path" placeholder="../images/ITEM/Attack/1. Malefic Gun/Malefic_Gun.webp">
            </div>
            <div class="form-group">
                <label>Atribut (HTML diperbolehkan)</label>
                <input type="text" name="attr">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description"></textarea>
            </div>
            <button type="submit" class="btn">Tambah</button>
            <a href="manage_items.php" class="btn btn-cancel">Batal</a>
        </form>
    </div>
</body>
</html> 