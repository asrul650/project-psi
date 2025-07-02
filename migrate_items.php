<?php
// Script migrasi item dari folder ke database
require_once __DIR__ . '/includes/db_connect.php';

$baseDir = __DIR__ . '/images/ITEM/Attack/';
$category = 'Attack';
$inserted = 0;
$failed = 0;

$folders = array_filter(glob($baseDir . '*'), 'is_dir');
foreach ($folders as $folderPath) {
    $folderName = basename($folderPath);
    // Cari file gambar (webp)
    $imgFile = glob($folderPath . '/*.webp');
    $imgPath = count($imgFile) ? str_replace(__DIR__ . '/', '', $imgFile[0]) : null;
    // Cari file deskripsi (txt)
    $descFile = glob($folderPath . '/*.txt');
    $desc = '';
    if (count($descFile)) {
        $desc = file_get_contents($descFile[0]);
    }
    // Nama item dari folder (hilangkan nomor urut jika ada)
    $name = preg_replace('/^\d+\.\s*/', '', $folderName);
    // Cek duplikat
    $stmt = $conn->prepare('SELECT id FROM items WHERE name=? AND category=?');
    $stmt->bind_param('ss', $name, $category);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        // Insert ke database
        $stmt2 = $conn->prepare('INSERT INTO items (name, image_path, description, category) VALUES (?, ?, ?, ?)');
        $stmt2->bind_param('ssss', $name, $imgPath, $desc, $category);
        if ($stmt2->execute()) {
            $inserted++;
        } else {
            $failed++;
            echo "Gagal insert: $name<br>";
        }
        $stmt2->close();
    }
    $stmt->close();
}
echo "Migrasi selesai. Berhasil: $inserted, Gagal: $failed"; 