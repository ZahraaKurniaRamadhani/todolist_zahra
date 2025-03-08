<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$title = $description = $due_date = $priority = $status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $user_id = $_SESSION['user_id'];

    if (empty($title) || empty($due_date)) {
        echo "<script>alert('Judul dan Tanggal Jatuh Tempo harus diisi!');</script>";
    } else {
        if (!empty($_POST['sub_tasks'])) {
            foreach ($_POST['sub_tasks'] as $sub_task) {
                if (empty(trim($sub_task))) {
                    die("Semua subtugas harus diisi! <a href='tambah_tugas.php'>Kembali</a>");
                }
            }
        }

        if (!$conn) {
            die("Koneksi database gagal!");
        }
        $query = "INSERT INTO tasks (user_id, task, description, due_date, priority, status) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isssss", $user_id, $title, $description, $due_date, $priority, $status);

        if ($stmt->execute()) {
            $task_id = $stmt->insert_id;

            if (!empty($_POST['sub_tasks'])) {
                foreach ($_POST['sub_tasks'] as $sub_task) {
                    if (!empty($sub_task)) {
                        $sub_query = "INSERT INTO subtasks (task_id, sub_task) VALUES (?, ?)";
                        $sub_stmt = $conn->prepare($sub_query);
                        $sub_stmt->bind_param("is", $task_id, $sub_task);
                        $sub_stmt->execute();
                    }
                }
            }
            header("Location: daftar_tugas.php");
            exit();
        } else {
            echo "<script>alert('Gagal menambahkan tugas!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Tambah Tugas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #00fff0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #00BFFF ;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #007bff;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        textarea {
            height: 100px;
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
            text-align: center;
            width: 100%;
        }

        .btn-kembali:hover {
            background-color: #218838;
        }

        .subtasks {
            margin-top: 10px;
        }

        .subtasks input {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Tambah Tugas</h2>
    <form action="" method="POST">
        <input type="text" name="title" placeholder="Judul Tugas" required>
        <textarea name="description" placeholder="Deskripsi Tugas" required></textarea>
        <input type="date" name="due_date" min="<?= date('Y-m-d') ?>" required>
        <select name="priority">
            <option value="Mendesak">Mendesak</option>
            <option value="Biasa">Biasa</option>
            <option value="Tidak Mendesak">Tidak Mendesak</option>
        </select>
        <select name="status">
            <option value="Sedang Dibuat">Sedang Dibuat</option>
            <option value="Belum Dikerjakan">Belum Dikerjakan</option>
            <option value="Selesai">Selesai</option>
        </select>
        <button type="submit">Tambah Tugas</button>
        <div class="subtasks" id="subtasks">
    <input type="text" name="sub_tasks[]" placeholder="Sub-tugas" required>
</div>
<button type="submit">Tambah Tugas</button>

    </form>
    <a href="index.php" class="btn-kembali">Kembali</a>
</div>

</body>
</html>
