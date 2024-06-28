<?php
date_default_timezone_set('Asia/Jakarta');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once 'config_db.php';

$db = new ConfigDB();
$conn = $db->connect();

if (isset($_GET['id'])) {
    $id_sepeda = $_GET['id'];
    $deleted_at = date('Y-m-d H:i:s');

    // Query untuk menghapus data (soft delete dengan mengisi kolom deleted_at)
    $query = "UPDATE sepeda SET deleted_at = '$deleted_at' WHERE id_sepeda = '$id_sepeda'";

    if ($conn->query($query) === TRUE) {
        $message = "Data berhasil dihapus";
        $alert_type = "success";
    } else {
        $message = "Error: " . $query . "<br>" . $conn->error;
        $alert_type = "danger";
    }

    $conn->close();
    header("Location: index.php?message=$message&alert_type=$alert_type");
    exit;
} else {
    header("Location: index.php?message=ID tidak ditemukan&alert_type=danger");
    exit;
}
