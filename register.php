<?php
session_start();
require_once 'config_db.php';

// Pastikan tidak ada session yang aktif
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Proses registrasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validasi email harus menggunakan @gmail.com
    if (strpos($email, '@gmail.com') === false) {
        echo "<div class='alert alert-danger'>Alamat email harus menggunakan domain @gmail.com</div>";
    } else {
        // Proses registrasi sesuai kebutuhan Anda
        try {
            $db = new ConfigDB();
            $conn = $db->connect();

            $username = $_POST['username'];

            // Periksa apakah username sudah digunakan
            $check_stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $existing_user = $check_stmt->get_result()->fetch_assoc();

            if ($existing_user) {
                echo "<div class='alert alert-danger'>Username sudah digunakan. Silakan gunakan username lain.</div>";
            } else {
                $insert_stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                $insert_stmt->bind_param("sss", $username, $email, $password);

                if ($insert_stmt->execute()) {
                    $_SESSION['user_id'] = $insert_stmt->insert_id; // Simpan ID pengguna baru ke dalam session
                    $_SESSION['username'] = $username; // Simpan username ke dalam session
                    header('Location: index.php'); // Redirect ke halaman utama
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Registrasi gagal: " . $insert_stmt->error . "</div>";
                }

                $insert_stmt->close();
            }

            $check_stmt->close();
            $conn->close();
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Registrasi gagal: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Register</div>
                    <div class="card-body">
                        <form action="register.php" method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
