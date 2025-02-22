<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['task_id'])) {
    header("Location: index.php");
    exit();
}

$task_id = $_GET['task_id'];
$user_id = $_SESSION['user_id'];

$title = $subtask_name = "";
$status = "Belum Dikerjakan";

$query = "SELECT task FROM tasks WHERE task_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Tugas tidak ditemukan!'); window.location.href = 'index.php';</script>";
    exit();
}

$task = $result->fetch_assoc();
$title = $task['task'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subtask_name = trim($_POST['subtask_name']);
    $status = $_POST['status'];

    if (empty($subtask_name)) {
        echo "<script>alert('Nama subtugas harus diisi!');</script>";
    } else {
        $query = "INSERT INTO subtasks (task_id, subtask_name, status) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iss", $task_id, $subtask_name, $status);

        if ($stmt->execute()) {
            echo "<script>alert('Subtugas berhasil ditambahkan!'); window.location.href = 'task_list.php';</script>";
            exit();
        } else {
            echo "<script>alert('Gagal menambahkan subtugas!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Subtugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            color: #007bff;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .btn-kembali {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn-kembali:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Tambah Subtugas untuk Tugas: <?php echo htmlspecialchars($title); ?></h2>
    <form action="" method="POST">
        <input type="text" name="subtask_name" placeholder="Nama Subtugas" required>
        <select name="status">
            <option value="Belum Dikerjakan">Belum Dikerjakan</option>
            <option value="Selesai">Selesai</option>
        </select>
        <button type="submit">Tambah Subtugas</button>
    </form>
    <a href="task_list.php" class="btn-kembali">Kembali ke Daftar Tugas</a>
</div>
</body>
</html>
