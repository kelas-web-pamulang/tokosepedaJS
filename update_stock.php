<?php
session_start();
require_once 'config_db.php';
require 'vendor/autoload.php';

\Sentry\init(['dsn' => 'https://examplePublicKey@o0.ingest.sentry.io/0' ]);

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

try {
    // Validasi metode request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Metode request tidak valid.');
    }

    // Validasi input
    $id_sepeda = $_POST['id_sepeda'];
    $jumlah = intval($_POST['jumlah']);

    if (empty($id_sepeda) || $jumlah <= 0) {
        throw new Exception('Data yang dikirim tidak valid.');
    }

    // Lakukan koneksi ke database
    $db = new ConfigDB();
    $conn = $db->connect();

    // Query untuk menambah stok
    $query = "UPDATE sepeda SET stok = stok + ? WHERE id_sepeda = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $jumlah, $id_sepeda);

    if (!$stmt->execute()) {
        throw new Exception('Gagal menambah stok: ' . $conn->error);
    }

    echo "<tr><td colspan='10' class='text-center'>Stok berhasil ditambahkan.</td></tr>";

    // Tutup koneksi database
    $db->close();
} catch (Exception $e) {
    \Sentry\captureException($e);
    showError($e->getMessage());
}
?>
