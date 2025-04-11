<?php
include 'koneksi.php';

$error_username = $error_email = $error_password = $error = "";
$username = $email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($username)) {
        $error_username = "Username harus diisi!";
    }
    if (empty($email)) {
        $error_email = "Email harus diisi!";
    } elseif (!strpos($email, '@')) {
        $error_email = "Email harus valid dan mengandung '@'!";
    }
    if (empty($password)) {
        $error_password = "Password harus diisi!";
    } elseif (strlen($password) < 6) {
        $error_password = "Password minimal 6 karakter!";
    }

    if (empty($error_username) && empty($error_email) && empty($error_password)) {
        $check_sql = "SELECT user_id FROM users WHERE username = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error_username = "Username sudah digunakan!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Pendaftaran gagal!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #00fff0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #00BFFF;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 300px;
        }
        .input-container {
            display: flex;
            align-items: center;
            margin: 10px 0;
            gap: 10px;
        }

        .input-container i {
            color: gray;
            font-size: 18px;
            margin-left: 5px;
        }

        .input-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .input-wrapper input {
            padding: 10px;
            padding-left: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            width: 100%;
        }

        .error {
            color: red;
            font-size: 12px;
            margin-top: 2px;
            text-align: left;
            padding-left: 2px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        a {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-user-plus"></i> Register</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="input-container">
            <i class="fas fa-user"></i>
            <div class="input-wrapper">
                <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>" autocomplete="new-username" required>
                <small class="error"><?php echo $error_username; ?></small>
            </div>
        </div>

        <div class="input-container">
            <i class="fas fa-envelope"></i>
            <div class="input-wrapper">
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($email ?? '') ?>" required>
                <small class="error"><?php echo $error_email; ?></small>
            </div>
        </div>

        <div class="input-container">
            <i class="fas fa-lock"></i>
            <div class="input-wrapper">
                <input type="password" name="password" placeholder="Password" required>
                <small class="error"><?php echo $error_password; ?></small>
            </div>
        </div>

        <button type="submit">Daftar</button>
    </form>
    <p>Sudah punya akun? <a href="login.php">Login</a></p>
</div>
</body>
</html>
