<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$username = $user['username'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #00fff0;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
            max-width: 600px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h3>Selamat datang di To-Do List, <strong><?php echo htmlspecialchars($username); ?></strong></h3>
    <div class="d-grid gap-3 mt-4">
        <a href="add_task.php" class="btn btn-primary btn-lg"><i class="fas fa-plus"></i> Tambah Tugas</a>
        <a href="daftar_tugas.php" class="btn btn-secondary btn-lg"><i class="fas fa-list"></i> Lihat Daftar Tugas</a>
    </div>
</div>
</body>
</html>
