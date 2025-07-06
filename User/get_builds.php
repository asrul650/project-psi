<?php
require_once '../includes/db_connect.php';
session_start();
header('Content-Type: application/json');

$hero_id = isset($_GET['hero_id']) ? intval($_GET['hero_id']) : 0;
if ($hero_id <= 0) {
    echo json_encode(['success'=>false, 'error'=>'Invalid hero_id']);
    exit();
}

error_log('GET_BUILDS: Session = ' . print_r($_SESSION, true));

// Official builds (admin)
$official = [];
$stmt = $conn->prepare('SELECT b.id, b.name, b.description, b.created_at, u.username as author FROM builds b JOIN users u ON b.user_id = u.id WHERE b.hero_id = ? AND b.is_official = 1');
$stmt->bind_param('i', $hero_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $row['items'] = [];
    $item_stmt = $conn->prepare('SELECT item_id FROM build_items WHERE build_id = ?');
    $item_stmt->bind_param('i', $row['id']);
    $item_stmt->execute();
    $item_res = $item_stmt->get_result();
    while ($item = $item_res->fetch_assoc()) {
        $row['items'][] = intval($item['item_id']);
    }
    $official[] = $row;
}

// User builds
$user = [];
$stmt = $conn->prepare('SELECT b.id, b.name, b.description, b.created_at, u.username as author FROM builds b JOIN users u ON b.user_id = u.id WHERE b.hero_id = ? AND b.is_official = 0');
$stmt->bind_param('i', $hero_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $row['items'] = [];
    $item_stmt = $conn->prepare('SELECT item_id FROM build_items WHERE build_id = ?');
    $item_stmt->bind_param('i', $row['id']);
    $item_stmt->execute();
    $item_res = $item_stmt->get_result();
    while ($item = $item_res->fetch_assoc()) {
        $row['items'][] = intval($item['item_id']);
    }
    $user[] = $row;
}

echo json_encode(['success'=>true, 'official'=>$official, 'user'=>$user]); 