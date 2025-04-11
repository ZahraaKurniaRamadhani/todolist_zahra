<?php
session_start();
include 'koneksi.php';

$login_error = "";
$error_username = "";
$error_password = "";
$username = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username)) {
        $error_username = "Username harus diisi!";
    }
    if (empty($password)) {
        $error_password = "Password harus diisi!";
    }

    if (empty($error_username) && empty($error_password)) {
        $sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

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
    width: 100%;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    overflow: hidden;
    background: white;
}

.input-container i {
    min-width: 35px;
    text-align: center;
    color: gray;
    padding: 10px;
    background: #f1f1f1;
    border-right: 1px solid #ccc;
}

.input-container input {
    flex: 1;
    padding: 10px;
    border: none;
    outline: none;
    box-sizing: border-box;
}

        .error-message {
            color: red;
            font-size: 12px;
            text-align: left;
            margin-top: 5px;
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
    <h2><i class="fas fa-sign-in-alt"></i> Login</h2>
    
    <?php if (!empty($login_error)): ?>
        <p class="error-message"><?php echo $login_error; ?></p>
    <?php endif; ?>

    <form method="POST">
    <div class="input-container">
    <i class="fas fa-user"></i>
    <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>">
</div>
<?php if (!empty($error_username)): ?>
    <small class="error-message"><?php echo $error_username; ?></small>
<?php endif; ?>

<div class="input-container">
    <i class="fas fa-lock"></i>
    <input type="password" name="password" placeholder="Password">
</div>
<?php if (!empty($error_password)): ?>
    <small class="error-message"><?php echo $error_password; ?></small>
<?php endif; ?>

        <button type="submit">Masuk</button>
    </form>

    <p>Belum punya akun? <a href="register.php">Daftar</a></p>
</div>
</body>
</html>
