<?php
require_once '../includes/db_connect.php';
session_start();
if (!isset($_SESSION['user_id_user']) || ($_SESSION['role_user'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $build_id = intval($_POST['build_id'] ?? 0);
    if ($build_id > 0) {
        // Hapus build_items dan build_likes terkait
        $conn->query('DELETE FROM build_items WHERE build_id = ' . $build_id);
        $conn->query('DELETE FROM build_likes WHERE build_id = ' . $build_id);
        // Hapus build
        $conn->query('DELETE FROM builds WHERE id = ' . $build_id);
    }
}
header('Location: builds_admin.php');
exit(); 