<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Insert Data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<?php
        date_default_timezone_set('Asia/Jakarta');
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);

        require_once 'config_db.php';

        $db = new ConfigDB();
        $conn = $db->connect();
    ?>
    <div class="container">
        <h1 class="text-center mt-5">Tambah Data Sepeda</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="nameInput">Nama Sepeda</label>
                <input type="text" class="form-control" id="nameInput" name="nama_sepeda" placeholder="Masukkan nama sepeda" required>
            </div>
            <div class="form-group">
                <label for="merkInput">Merk</label>
                <input type="text" class="form-control" id="merkInput" name="merk" placeholder="Masukkan merk sepeda" required>
            </div>
            <div class="form-group">
                <label for="tahunInput">Tahun Produksi</label>
                <input type="number" class="form-control" id="tahunInput" name="tahun_produksi" placeholder="Masukkan tahun produksi" required>
            </div>
            <div class="form-group">
                <label for="namaTipe">Nama Tipe</label>
                <?php
                    $namaTipe = $conn->query("SELECT id_tipe, nama_tipe FROM tipe");
                    echo "<select class='form-control' id='namaTipe' name='id_tipe' required>";
                    echo "<option value=''>Pilih Tipe</option>";
                    while ($row = $namaTipe->fetch_assoc()) {
                        echo "<option value='{$row['id_tipe']}'>{$row['nama_tipe']}</option>";
                    }
                    echo "</select>";
                ?>
            </div>
            <div class="form-group">
                <label for="namaKategori">Pilihan Kategori</label>
                <?php
                    $namaKategori = $conn->query("SELECT id_kategori, nama_kategori FROM kategori");
                    echo "<select class='form-control' id='namaKategori' name='id_kategori' required>";
                    echo "<option value=''>Pilih Kategori</option>";
                    while ($row = $namaKategori->fetch_assoc()) {
                        echo "<option value='{$row['id_kategori']}'>{$row['nama_kategori']}</option>";
                    }
                    echo "</select>";
                ?>
            </div>
            <div class="form-group">
                <label for="stokInput">Stok</label>
                <input type="number" class="form-control" id="stokInput" name="stok" placeholder="Masukkan jumlah stok" required>
            </div>
            <div class="form-group">
                <label for="hargaInput">Harga</label>
                <input type="number" class="form-control" id="hargaInput" name="harga" placeholder="Masukkan harga" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="index.php" class="btn btn-info">Kembali</a>
        </form>

        <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $nama = $_POST['nama_sepeda'];
                $merk = $_POST['merk'];
                $tahunProduksi = $_POST['tahun_produksi'];
                $tipe = $_POST['id_tipe'];
                $kategori = $_POST['id_kategori'];
                $stok = $_POST['stok'];
                $harga = $_POST['harga'];
                $createdAt = date('Y-m-d H:i:s');

                $query = "INSERT INTO sepeda (nama_sepeda, merk, tahun_produksi, id_tipe, id_kategori, stok, harga, created_at) 
                         VALUES ('$nama', '$merk', '$tahunProduksi','$tipe','$kategori','$stok', '$harga', '$createdAt')";

                if ($conn->query($query) === TRUE) {
                    echo "<div class='alert alert-success mt-3' role='alert'>Data inserted successfully</div>";
                } else {
                    echo "<div class='alert alert-danger mt-3' role='alert'>Error: " . $query . "<br>" . $conn->error . "</div>";
                }
            }
            $conn->close();
        ?>
    </div>
</body>
</html>
