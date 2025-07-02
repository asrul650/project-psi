<?php
require_once __DIR__ . '/includes/db_connect.php';

$baseDirs = [
    'Attack' => __DIR__ . '/images/ITEM/Attack/',
    // Tambahkan kategori lain di sini jika ingin
    // 'Magic' => __DIR__ . '/images/ITEM/Magic/',
    // 'Defense' => __DIR__ . '/images/ITEM/Defense/',
];

$total = 0;
foreach ($baseDirs as $category => $baseDir) {
    $folders = array_filter(glob($baseDir . '*'), 'is_dir');
    foreach ($folders as $folderPath) {
        $folderName = basename($folderPath);
        // Nama item dari folder (hilangkan nomor urut jika ada)
        $name = preg_replace('/^\d+\.\s*/', '', $folderName);
        // Cari file gambar (webp)
        $imgFile = glob($folderPath . '/*.webp');
        $imgPath = count($imgFile) ? str_replace(__DIR__ . '/', '', $imgFile[0]) : null;
        if ($imgPath) {
            // Update image_path di database
            $stmt = $conn->prepare('UPDATE items SET image_path=? WHERE name=? AND category=?');
            $stmt->bind_param('sss', $imgPath, $name, $category);
            $stmt->execute();
            $stmt->close();
            echo "Update $name ($category): $imgPath<br>";
            $total++;
        }
    }
}
echo "<br>Update selesai. Total: $total item."; 