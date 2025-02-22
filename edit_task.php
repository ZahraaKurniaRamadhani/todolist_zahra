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

$query = "SELECT * FROM tasks WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $task_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    die("Tugas tidak ditemukan!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $task_name = $_POST['task_name'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];

    $update_query = "UPDATE tasks SET task = ?, description = ?, due_date = ?, priority = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("ssssii", $task_name, $description, $due_date, $priority, $task_id, $user_id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tugas</title>
    <style>
        body {
            background-color: #00fff0;
            font-family: Arial, sans-serif;
        }
        </style>
</head>
<body>
<div class="container">
    <h2>Edit Tugas</h2>
    <form method="POST">
        <div class="mb-3">
            <label>Nama Tugas</label>
            <input type="text" name="task_name" class="form-control" value="<?php echo htmlspecialchars($task['task']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control"><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label>Tenggat Waktu</label>
            <input type="date" name="due_date" class="form-control" value="<?php echo htmlspecialchars($task['due_date']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Prioritas</label>
            <select name="priority" class="form-control">
                <option value="Mendesak" <?php echo ($task['priority'] == 'Mendesak') ? 'selected' : ''; ?>>Mendesak</option>
                <option value="Biasa" <?php echo ($task['priority'] == 'Biasa') ? 'selected' : ''; ?>>Biasa</option>
                <option value="Tidak Mendesak" <?php echo ($task['priority'] == 'Tidak Mendesak') ? 'selected' : ''; ?>>Tidak Mendesak</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
