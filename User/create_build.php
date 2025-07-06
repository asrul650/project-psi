<?php
require_once '../includes/db_connect.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id_user'])) {
    echo json_encode(['success'=>false, 'error'=>'Not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$hero_id = isset($data['hero_id']) ? intval($data['hero_id']) : 0;
$name = trim($data['name'] ?? '');
$desc = trim($data['description'] ?? '');
$items = $data['items'] ?? [];
$user_id = $_SESSION['user_id_user'];

if ($hero_id <= 0 || !$name || count($items) == 0) {
    echo json_encode(['success'=>false, 'error'=>'Invalid data']);
    exit();
}

// Insert build
$stmt = $conn->prepare('INSERT INTO builds (hero_id, user_id, name, description, is_official, created_at) VALUES (?, ?, ?, ?, 0, NOW())');
$stmt->bind_param('iiss', $hero_id, $user_id, $name, $desc);
if (!$stmt->execute()) {
    echo json_encode(['success'=>false, 'error'=>'Failed to create build']);
    exit();
}
$build_id = $stmt->insert_id;
// Insert items
$item_stmt = $conn->prepare('INSERT INTO build_items (build_id, item_id) VALUES (?, ?)');
foreach ($items as $item_id) {
    $item_stmt->bind_param('ii', $build_id, $item_id);
    $item_stmt->execute();
}
echo json_encode(['success'=>true, 'build_id'=>$build_id]); 