<?php
session_start();
require_once 'config_db.php';
require 'vendor/autoload.php';

\Sentry\init(['dsn' => 'https://examplePublicKey@o0.ingest.sentry.io/0']);

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

// Proses form untuk menambah stok
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_sepeda']) && isset($_POST['jumlah'])) {
        // Lakukan koneksi ke database
        $db = new ConfigDB();
        $conn = $db->connect();

        // Validasi input
        $id_sepeda = $_POST['id_sepeda'];
        $jumlah = intval($_POST['jumlah']);

        if (empty($id_sepeda) || $jumlah <= 0) {
            throw new Exception('Data yang dikirim tidak valid.');
        }

        // Query untuk menambah stok
        $query = "UPDATE sepeda SET stok = stok + ? WHERE id_sepeda = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $jumlah, $id_sepeda);

        if (!$stmt->execute()) {
            throw new Exception('Gagal menambah stok: ' . $conn->error);
        }

        // Pesan sukses
        echo "<div class='alert alert-success'>Stok berhasil ditambahkan.</div>";

        // Tutup koneksi database
        $db->close();
    }
} catch (Exception $e) {
    \Sentry\captureException($e);
    showError($e->getMessage());
}

// Query untuk menampilkan data sepeda
try {
    $db = new ConfigDB();
    $conn = $db->connect();

    $conditional = [];
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $search_by = $_GET['search_by'];
        if ($search_by == 'nama_sepeda') {
            $conditional[] = "nama_sepeda LIKE '%$search%'";
        } else if ($search_by == 'tahun_produksi') {
            $conditional[] = "tahun_produksi LIKE '%$search%'";
        } else if ($search_by == 'merk') {
            $conditional[] = "merk LIKE '%$search%'";
        }
    } else if (isset($_GET['delete'])) {
        $query = $db->update('sepeda', [
            'deleted_at' => date('Y-m-d H:i:s')
        ], $_GET['delete']);
    }

    $query = "SELECT s.id_sepeda, s.nama_sepeda, s.merk, s.tahun_produksi, t.nama_tipe, 
              k.nama_kategori, s.stok, s.harga, s.created_at
              FROM sepeda s 
              LEFT JOIN tipe t ON s.id_tipe = t.id_tipe 
              LEFT JOIN kategori k ON s.id_kategori = k.id_kategori
              WHERE s.deleted_at IS NULL";

    if (!empty($conditional)) {
        $query .= " AND " . implode(" AND ", $conditional);
    }

    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Query failed: " . $conn->error);
    }

    $totalRows = $result->num_rows;

    // Sisipkan HTML untuk tampilan tabel
    $tableHTML = '';
    if ($totalRows > 0) {
        $tableHTML .= "<table class='table table-striped table-hover'>
                        <thead class='thead-dark'>
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
                                <th colspan='2'>Pilihan</th>
                            </tr>
                        </thead>
                        <tbody>";

        foreach ($result as $key => $row) {
            $tableHTML .= "<tr>
                                <td>" . ($key + 1) . "</td>
                                <td>" . htmlspecialchars($row['nama_sepeda']) . "</td>
                                <td>" . htmlspecialchars($row['merk']) . "</td>
                                <td>" . htmlspecialchars($row['tahun_produksi']) . "</td>
                                <td>" . htmlspecialchars($row['nama_tipe']) . "</td>
                                <td>" . htmlspecialchars($row['nama_kategori']) . "</td>
                                <td>" . htmlspecialchars($row['stok']) . "</td>
                                <td>" . htmlspecialchars($row['harga']) . "</td>
                                <td>" . htmlspecialchars($row['created_at']) . "</td>
                                <td>
                                    <a class='btn btn-sm btn-info' href='update.php?id=" . htmlspecialchars($row['id_sepeda']) . "'>Update</a>
                                    <form action='index.php' method='post' class='d-inline'>
                                        <input type='hidden' name='id_sepeda' value='" . htmlspecialchars($row['id_sepeda']) . "'>
                                        <label for='jumlah_" . $row['id_sepeda'] . "'>Tambahkan Jumlah Stok:</label>
                                        <input type='number' id='jumlah_" . $row['id_sepeda'] . "' name='jumlah' min='1' required>
                                        <button type='submit' class='btn btn-sm btn-success'>Tambah Stok</button>
                                    </form>
                                    <a class='btn btn-sm btn-danger delete-button' href='index.php?delete=" . htmlspecialchars($row['id_sepeda']) . "'>Delete</a>
                                </td>
                            </tr>";
        }

        $tableHTML .= "</tbody></table>";
    } else {
        $tableHTML .= "<p class='text-center'>No Data</p>";
    }

    // Tutup koneksi database
    $db->close();

} catch (Exception $e) {
    \Sentry\captureException($e);
    showError($e->getMessage());
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Sepeda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
            <a href="insert.php" class="btn btn-success">Tambah Data</a>
        </div>

        <?php echo $tableHTML; ?>

    </div>

    <footer class="text-center mt-5">
        Tugas Project-Praktisi Jovan Cahya Radita dan Selvi Oktaviani Membuat Fungsi CRUD, login, logout, register dan data transaksi dibagian update.
    </footer>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
      
