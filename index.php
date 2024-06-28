<?php
session_start();
require_once 'config_db.php';
require 'vendor/autoload.php';

\Sentry\init([
    'dsn' => 'https://3cbd8d10cef46bbfebaca74f8f298058@o4507428775133184.ingest.us.sentry.io/4507429260296192',
    'traces_sample_rate' => 1.0,
    'profiles_sample_rate' => 1.0,
]);

// Periksa apakah pengguna sudah login
$loggedIn = isset($_SESSION['user_id']);

// Pastikan tidak ada session yang aktif
if (!$loggedIn) {
    header('Location: login.php');
    exit();
}

// Fungsi untuk menampilkan pesan kesalahan
function showError($message) {
    echo "<tr><td colspan='10' class='text-center text-danger'>Error: " . htmlspecialchars($message) . "</td></tr>";
}

function handleShutdown() {
    $error = error_get_last();
    if ($error !== null) {
        \Sentry\captureLastError();
        showError("Something went wrong. Please try again later.");
    }
}

function handleException($exception) {
    \Sentry\captureException($exception);
    showError($exception->getMessage());
}

set_exception_handler('handleException');
register_shutdown_function('handleShutdown');
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Toko Sepeda</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            background-image: url('images/background1.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        footer {
            background-color: rgba(92, 102, 214, 0.9);
            color: white;
            padding: 20px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Toko Sepeda Jovan dan Selvi</h1>
        <div class="text-center mt-3">
            <?php if ($loggedIn): ?>
                <p>Selamat datang di Jovan Store, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Login</a>
                <a href="register.php" class="btn btn-secondary">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <h1 class="text-center mt-5">List Daftar Sepeda Toko SJ</h1>
        <div class="row">
            <div class="d-flex justify-content-between mb-3">
                <form action="" method="get" class="d-flex">
                    <input class="form-control" placeholder="Cari Data" name="search" />
                    <select class="form-select mx-2" name="search_by">
                        <option value="">Search All</option>
                        <option value="nama_sepeda">Nama Sepeda</option>
                        <option value="merk">Merk</option>
                        <option value="tahun_produksi">Tahun Produksi</option>
                    </select>
                    <button type="submit" class="btn btn-success btn-sm">Cari</button>
                </form>
                <a href="insert.php"><button class="btn btn-success">Tambah Data</button></a>
            </div>
            <table class="table table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Sepeda</th>
                        <th>Merk</th>
                        <th>Tahun Produksi</th>
                        <th>Tipe</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th>Tgl. Buat</th>
                        <th colspan="2">Pilihan</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                date_default_timezone_set('Asia/Jakarta');
                ini_set('display_errors', '1');
                ini_set('display_startup_errors', '1');
                error_reporting(E_ALL);

                try {
                    $db = new ConfigDB();
                    $conn = $db->connect();

                    $conditional = [];
                    if (isset($_GET['search'])) {
                        $search = $_GET['search'];
                        $search_by = $_GET['search_by'];
                        if ($search_by == 'nama_sepeda') {
                            $conditional['AND nama_sepeda LIKE'] = "%$search%";
                        } else if ($search_by == 'tahun_produksi') {
                            $conditional['AND tahun_produksi LIKE'] = "%$search%";
                        } else if ($search_by == 'merk') {
                            $conditional['AND merk LIKE'] = "%$search%";
                        }
                    } else if (isset($_GET['delete'])) {
                        $query = "UPDATE sepeda SET deleted_at = NOW() WHERE id_sepeda = " . $_GET['delete'];
                        $conn->query($query);
                    }

                    $query = "SELECT s.id_sepeda, s.nama_sepeda, s.merk, s.tahun_produksi, t.nama_tipe, 
                    k.nama_kategori, s.stok, s.harga, s.created_at
                    FROM sepeda s 
                    LEFT JOIN tipe t ON s.id_tipe = t.id_tipe 
                    LEFT JOIN kategori k ON s.id_kategori = k.id_kategori
                    WHERE s.deleted_at IS NULL";

                    if (!empty($conditional)) {
                        foreach ($conditional as $key => $value) {
                            $query .= " $key '$value'";
                        }
                    }

                    $result = $conn->query($query);
                    if (!$result) {
                        throw new Exception("Query failed: " . $conn->error);
                    }

                    $totalRows = $result->num_rows;

                    if ($totalRows > 0) {
                        foreach ($result as $key => $row) {
                            echo "<tr>";
                            echo "<td>" . ($key + 1) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_sepeda']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['merk']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['tahun_produksi']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_tipe']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama_kategori']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['stok']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['harga']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                            echo "<td><a class='btn btn-sm btn-info' href='update.php?id=" . htmlspecialchars($row['id_sepeda']) . "'>Update</a></td>";
                            echo "<td><a class='btn btn-sm btn-danger delete-button' href='index.php?delete=" . htmlspecialchars($row['id_sepeda']) . "'>Delete</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10' class='text-center'>No Data</td></tr>";
                    }
                } catch (Exception $e) {
                    \Sentry\captureException($e);
                    showError($e->getMessage());
                }

                $db->close();
                ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer class="text-center mt-5">Tugas Project-Praktisi Jovan Cahya Radita dan Selvi Oktaviani Membuat Fungsi CRUD, login, logout, register dan data transaksi dibagian update. </footer>
    <script>
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const confirmed = confirm('Apakah Anda yakin ingin menghapus data ini?');
                if (confirmed) {
                    window.location.href = this.href;
                }
            });
        });
    </script>
</body>
</html>
