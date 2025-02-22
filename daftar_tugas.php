<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    if (isset($_POST['delete'])) {
        $task_id = $_POST['task_id'];
        $delete_subtask_query = "DELETE FROM subtasks WHERE task_id = ?";
        $stmt = $conn->prepare($delete_subtask_query);
        $stmt->bind_param("i", $task_id);
        $stmt->execute();

        $delete_query = "DELETE FROM tasks WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("ii", $task_id, $user_id);
        $stmt->execute();
    } elseif (isset($_POST['status'])) {
        $task_id = $_POST['task_id'];
        $status = $_POST['status'] === 'on' ? 'Selesai' : 'Belum Dikerjakan';
        $update_query = "UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sii", $status, $task_id, $user_id);
        $stmt->execute();
    }
    header("Location: daftar_tugas.php");
    exit();
}

$query = "SELECT t.*, s.sub_task 
          FROM tasks t 
          LEFT JOIN subtasks s ON t.id = s.task_id 
          WHERE t.user_id = ? 
          ORDER BY 
            CASE 
              WHEN t.status = 'Belum Dikerjakan' THEN 1 
              WHEN t.status = 'Sedang Dikerjakan' THEN 2 
              WHEN t.status = 'Selesai' THEN 3 
            END, t.due_date ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks_result = $stmt->get_result();

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #00fff0;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #007bff;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
        }
        .header h1 {
            font-size: 24px;
        }
        .completed {
            text-decoration: line-through;
            color: gray;
        }
        .logout-btn {
            background: #ff4d4d;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .logout-btn:hover {
            background: #cc0000;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .task {
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ff9999;
        }
        .task.selesai {
            background: #66b3ff;
        }
        .task h3 {
            margin: 0;
            font-size: 18px;
        }
        .task p {
            margin: 5px 0;
        }
        .task-actions {
            display: flex;
            gap: 10px;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas</title>
</head>
<body>
<div class="header">
    <h1>Daftar Tugas</h1>
    <form method="POST">
        <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </form>
</div>
<div class="container">
    <?php while ($task = $tasks_result->fetch_assoc()): ?>
        <div class="task <?php echo ($task['status'] == 'Selesai') ? 'selesai' : ''; ?>">
            <div>
                <h3 class="<?php echo ($task['status'] == 'Selesai') ? 'completed' : ''; ?>">
                    <?php echo htmlspecialchars($task['task']); ?>
                </h3>

                <p class="<?php echo ($task['status'] == 'Selesai') ? 'completed' : ''; ?>">
                    <strong>Sub-tugas:</strong> <?php echo htmlspecialchars($task['sub_task']); ?>
                </p>

                <p class="<?php echo ($task['status'] == 'Selesai') ? 'completed' : ''; ?>">
                    <?php echo htmlspecialchars($task['description']); ?>
                </p>
                <p class="<?php echo ($task['status'] == 'Selesai') ? 'completed' : ''; ?>">
                    <strong>Tenggat:</strong> <?php echo htmlspecialchars($task['due_date']); ?>
                </p>
                <p class="<?php echo ($task['status'] == 'Selesai') ? 'completed' : ''; ?>">
                    <strong>Prioritas:</strong> <?php echo htmlspecialchars($task['priority']); ?>
                </p>
                <p class="<?php echo ($task['status'] == 'Selesai') ? 'completed' : ''; ?>">
                    <strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?>
                </p>
            </div>
            <div class="task-actions">
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <input type="checkbox" name="status" onchange="this.form.submit()" <?php echo ($task['status'] == 'Selesai') ? 'checked disabled' : ''; ?>>
                </form>
                <?php if ($task['status'] != 'Selesai'): ?>
                <form action="edit_task.php" method="GET" style="display:inline;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>
                </form>
                <?php endif; ?>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                    <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Hapus tugas ini?');"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
