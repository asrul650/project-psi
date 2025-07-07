<?php
session_start();
if (!isset($_SESSION['user_id_admin']) || !isset($_SESSION['role_admin']) || $_SESSION['role_admin'] !== 'admin') {
    header('Location: login.php?error=unauthorized');
    exit();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Official Build</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>body{background:#f5f6fa;color:#23283a;text-align:center;padding:80px;font-size:1.3rem;}</style>
</head>
<body>
    <h1>Coming soon: Add Official Build</h1>
    <p>Fitur ini akan segera tersedia.</p>
    <a href="manage_builds.php" style="color:#ffe600;font-weight:700;">&larr; Back to Manage Builds</a>
</body>
</html> 