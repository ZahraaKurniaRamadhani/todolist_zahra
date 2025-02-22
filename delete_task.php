<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    $task_id = $_POST['task_id'];
    $user_id = $_SESSION['user_id'];

    $query = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Konfirmasi Hapus Tugas</title>
</head>
<body>
    <h2>Konfirmasi Hapus Tugas</h2>
    <p>Apakah Anda yakin ingin menghapus tugas ini: <strong><?= htmlspecialchars($task['task']) ?></strong>?</p>
    <form action="delete_task.php?id=<?= $task['id'] ?>" method="POST">
        <button type="submit" style="background-color: red; color: white;">Hapus Tugas</button>
        <a href="index.php"><button type="button">Batal</button></a>
    </form>
</body>
</html>
