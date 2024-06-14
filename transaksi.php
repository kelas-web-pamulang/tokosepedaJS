<?php
session_start();
require_once 'config_db.php';

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$db = new ConfigDB();
$conn = $db->connect();

// Mendapatkan id sepeda dari parameter URL
$id_sepeda = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Mendapatkan data sepeda dari database
$query = $conn->prepare("SELECT * FROM sepeda WHERE id_sepeda = ?");
$query->bind_param("i", $id_sepeda);
$query->execute();
$result = $query->get_result();
$sepeda = $result->fetch_assoc();

if (!$sepeda) {
    echo "Data sepeda tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mendapatkan data dari form
    $nama_pembeli = $_POST['nama_pembeli'];
    $jumlah = intval($_POST['jumlah']);

    // Mengurangi stok
    if ($jumlah > 0 && $jumlah <= $sepeda['stok']) {
        $new_stok = $sepeda['stok'] - $jumlah;

        // Memulai transaksi
        $conn->begin_transaction();

        try {
            // Menambah data transaksi
            $insert_transaksi = $conn->prepare("INSERT INTO transaksi (id_sepeda, nama_pembeli, jumlah, tanggal) VALUES (?, ?, ?, NOW())");
            $insert_transaksi->bind_param("isi", $id_sepeda, $nama_pembeli, $jumlah);
            $insert_transaksi->execute();

            // Mengurangi stok sepeda
            $update_stok = $conn->prepare("UPDATE sepeda SET stok = ? WHERE id_sepeda = ?");
            $update_stok->bind_param("ii", $new_stok, $id_sepeda);
            $update_stok->execute();

            // Komit transaksi
            $conn->commit();
            echo "Transaksi berhasil dan stok telah diperbarui.";
        } catch (Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            $conn->rollback();
            echo "Terjadi kesalahan: " . $e->getMessage();
        }
    } else {
        echo "Jumlah tidak valid.";
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Transaksi Sepeda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Transaksi Sepeda</h1>
        <form action="" method="post">
            <div class="form-group">
                <label>Nama Sepeda</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($sepeda['nama_sepeda']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Stok Saat Ini</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($sepeda['stok']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Nama Pembeli</label>
                <input type="text" name="nama_pembeli" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="jumlah" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</body>
</html>
