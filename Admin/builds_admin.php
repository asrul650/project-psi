<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id_user']) || ($_SESSION['role_user'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}

// Ambil daftar hero
$heroes = [];
$res = $conn->query('SELECT id, name FROM heroes ORDER BY name');
while ($row = $res->fetch_assoc()) {
    $heroes[] = $row;
}

// Filter hero
$filter_hero = isset($_GET['hero_id']) ? intval($_GET['hero_id']) : 0;
$where = $filter_hero ? 'WHERE b.hero_id = ' . $filter_hero : '';

// Ambil semua build
$sql = "SELECT b.*, h.name AS hero_name, u.username FROM builds b JOIN heroes h ON b.hero_id = h.id JOIN users u ON b.user_id = u.id $where ORDER BY b.created_at DESC";
$builds = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Builds</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body { background: #181c24; color: #fff; }
        .container { max-width: 1100px; margin: 40px auto; background: #23283a; border-radius: 16px; padding: 32px; box-shadow: 0 4px 24px #ffe60022; }
        h1 { color: #ffe600; margin-bottom: 24px; }
        .filter-bar { margin-bottom: 24px; }
        .filter-bar select { padding: 8px 16px; border-radius: 8px; border: 1px solid #ffe600; background: #181c24; color: #ffe600; }
        table { width: 100%; border-collapse: collapse; margin-top: 18px; }
        th, td { padding: 10px 8px; border-bottom: 1px solid #333; text-align: left; }
        th { color: #ffe600; }
        tr.official { background: #232b4a; }
        .btn { padding: 6px 16px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; }
        .btn-official { background: #ffe600; color: #23283a; }
        .btn-unofficial { background: #3a4151; color: #fff; }
        .btn-delete { background: #ff4757; color: #fff; }
        .btn-add { background: #ffe600; color: #23283a; margin-bottom: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Builds (Admin)</h1>
        <a href="add_build.php" class="btn btn-add">+ Add Official Build</a>
        <form method="get" class="filter-bar">
            <label for="hero_id">Filter by Hero:</label>
            <select name="hero_id" id="hero_id" onchange="this.form.submit()">
                <option value="0">All Heroes</option>
                <?php foreach ($heroes as $hero): ?>
                    <option value="<?= $hero['id'] ?>"<?= $filter_hero == $hero['id'] ? ' selected' : '' ?>><?= htmlspecialchars($hero['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
        <table>
            <tr>
                <th>ID</th>
                <th>Hero</th>
                <th>Name</th>
                <th>Description</th>
                <th>User</th>
                <th>Official</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            <?php while ($build = $builds->fetch_assoc()): ?>
                <tr class="<?= $build['is_official'] ? 'official' : '' ?>">
                    <td><?= $build['id'] ?></td>
                    <td><?= htmlspecialchars($build['hero_name']) ?></td>
                    <td><?= htmlspecialchars($build['name']) ?></td>
                    <td><?= htmlspecialchars($build['description']) ?></td>
                    <td><?= htmlspecialchars($build['username']) ?></td>
                    <td><?= $build['is_official'] ? 'Yes' : 'No' ?></td>
                    <td><?= $build['created_at'] ?></td>
                    <td>
                        <?php if ($build['is_official']): ?>
                            <form method="post" action="set_official.php" style="display:inline;">
                                <input type="hidden" name="build_id" value="<?= $build['id'] ?>">
                                <input type="hidden" name="official" value="0">
                                <button class="btn btn-unofficial" type="submit">Unset Official</button>
                            </form>
                        <?php else: ?>
                            <form method="post" action="set_official.php" style="display:inline;">
                                <input type="hidden" name="build_id" value="<?= $build['id'] ?>">
                                <input type="hidden" name="official" value="1">
                                <button class="btn btn-official" type="submit">Set Official</button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="delete_build.php" style="display:inline;" onsubmit="return confirm('Delete this build?');">
                            <input type="hidden" name="build_id" value="<?= $build['id'] ?>">
                            <button class="btn btn-delete" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html> 