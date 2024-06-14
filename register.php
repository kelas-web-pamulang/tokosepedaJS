<?php
session_start();
require_once 'config_db.php';

$db = new ConfigDB();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Periksa apakah username sudah digunakan
    $check_stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $existing_user = $check_stmt->get_result()->fetch_assoc();

    if ($existing_user) {
        echo "<div class='alert alert-danger'>Username sudah digunakan. Silakan gunakan username lain.</div>";
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $insert_stmt->bind_param("ss", $username, $password);

        if ($insert_stmt->execute()) {
            echo "<div class='alert alert-success'>Registrasi berhasil.</div>";
        } else {
            echo "<div class='alert alert-danger'>Registrasi gagal: " . $insert_stmt->error . "</div>";
        }

        $insert_stmt->close();
    }

    $check_stmt->close();
}

$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Register</h1>
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>
</html>
