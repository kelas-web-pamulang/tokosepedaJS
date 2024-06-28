<?php
session_start();
require_once 'config_db.php';

$db = new ConfigDB();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi domain email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@gmail\.com$/', $email)) {
        echo "<div class='alert alert-danger'>Email harus menggunakan domain @gmail.com.</div>";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header('Location: index.php');
            exit();
        } else {
            echo "<div class='alert alert-danger'>Email atau password salah.</div>";
        }

        $stmt->close();
    }
}

$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Login</h1>
        <form action="login.php" method="post" id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Masukkan email @gmail.com" pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$" required>
                <div class="invalid-feedback">
                    Email harus menggunakan domain @gmail.com.
                </div>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

    <script>
        // JavaScript untuk validasi email domain
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            var emailField = document.getElementById('email');
            var emailPattern = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
            if (!emailPattern.test(emailField.value)) {
                emailField.classList.add('is-invalid');
                event.preventDefault();
            } else {
                emailField.classList.remove('is-invalid');
            }
        });
    </script>
</body>
</html>
