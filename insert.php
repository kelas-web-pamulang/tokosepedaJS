<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tambah Data Sepeda</title>
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
    <div class="container mt-5">
        <h1 class="text-center mb-5">Tambah Data Sepeda</h1>
        <form action="" method="post" class="needs-validation" novalidate>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nameInput" class="form-label">Nama Sepeda</label>
                    <input type="text" class="form-control" id="nameInput" name="nama_sepeda" placeholder="Masukkan nama sepeda" required>
                    <div class="invalid-feedback">
                        Silakan masukkan nama sepeda.
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="merkInput" class="form-label">Merk</label>
                    <input type="text" class="form-control" id="merkInput" name="merk" placeholder="Masukkan merk sepeda" required>
                    <div class="invalid-feedback">
                        Silakan masukkan merk sepeda.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tahunInput" class="form-label">Tahun Produksi</label>
                    <input type="number" class="form-control" id="tahunInput" name="tahun_produksi" placeholder="Masukkan tahun produksi" required>
                    <div class="invalid-feedback">
                        Silakan masukkan tahun produksi.
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="namaTipe" class="form-label">Nama Tipe</label>
                    <?php
                        $namaTipe = $conn->query("SELECT id_tipe, nama_tipe FROM tipe");
                        echo "<select class='form-control' id='namaTipe' name='id_tipe' required>";
                        echo "<option value=''>Pilih Tipe</option>";
                        while ($row = $namaTipe->fetch_assoc()) {
                            echo "<option value='{$row['id_tipe']}'>{$row['nama_tipe']}</option>";
                        }
                        echo "</select>";
                    ?>
                    <div class="invalid-feedback">
                        Silakan pilih tipe sepeda.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="namaKategori" class="form-label">Pilihan Kategori</label>
                    <?php
                        $namaKategori = $conn->query("SELECT id_kategori, nama_kategori FROM kategori");
                        echo "<select class='form-control' id='namaKategori' name='id_kategori' required>";
                        echo "<option value=''>Pilih Kategori</option>";
                        while ($row = $namaKategori->fetch_assoc()) {
                            echo "<option value='{$row['id_kategori']}'>{$row['nama_kategori']}</option>";
                        }
                        echo "</select>";
                    ?>
                    <div class="invalid-feedback">
                        Silakan pilih kategori sepeda.
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="stokInput" class="form-label">Stok</label>
                    <input type="number" class="form-control" id="stokInput" name="stok" placeholder="Masukkan jumlah stok" required>
                    <div class="invalid-feedback">
                        Silakan masukkan jumlah stok.
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="hargaInput" class="form-label">Harga</label>
                    <input type="number" class="form-control" id="hargaInput" name="harga" placeholder="Masukkan harga" required>
                    <div class="invalid-feedback">
                        Silakan masukkan harga.
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="index.php" class="btn btn-info">Kembali</a>
            </div>
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
                    echo "<div class='alert alert-success mt-3' role='alert'>Data berhasil ditambahkan</div>";
                } else {
                    echo "<div class='alert alert-danger mt-3' role='alert'>Error: " . $query . "<br>" . $conn->error . "</div>";
                }
            }
            $conn->close();
        ?>
    </div>

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function () {
            'use strict';
            window.addEventListener('load', function () {
                // Fetch all the forms we want to apply custom Bootstrap validation styles to
                var forms = document.getElementsByClassName('needs-validation');
                // Loop over them and prevent submission
                var validation = Array.prototype.filter.call(forms, function (form) {
                    form.addEventListener('submit', function (event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>
