<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config_db.php';

$db = new ConfigDB();
$conn = $db->connect();

if (isset($_GET['id'])) {
    $id_sepeda = $_GET['id'];

    // Mulai transaksi
    $conn->begin_transaction();

    // Query untuk menghapus data sepeda
    $query = $db->delete('sepeda', $id_sepeda);

    if ($query) {
        // Commit transaksi jika berhasil
        $conn->commit();
        echo "<div class='alert alert-success mt-3' role='alert'>Data berhasil dihapus</div>";
    } else {
        // Rollback transaksi jika gagal
        $conn->rollback();
        echo "<div class='alert alert-danger mt-3' role='alert'>Error: " . $conn->error . "</div>";
    }
} else {
    echo "<div class='alert alert-danger mt-3' role='alert'>ID tidak ditemukan</div>";
}

$conn->close();
header("Location: index.php");
exit;
?>
