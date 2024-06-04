<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Bicycle Data</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php
        date_default_timezone_set('Asia/Jakarta');
        require_once 'config_db.php';

        $db = new ConfigDB();
        $conn = $db->connect();

        $id_sepeda = $_GET['id'];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nama = $_POST['nama_sepeda'];
            $merk = $_POST['merk'];
            $tahunProduksi = $_POST['tahun_produksi'];
            $tipe = $_POST['id_tipe'];
            $kategori = $_POST['id_kategori'];
            $stok = $_POST['stok'];
            $harga = $_POST['harga'];

            $data_sepeda = [
                'nama_sepeda' => $nama,
                'merk' => $merk,
                'tahun_produksi' => $tahunProduksi,
                'id_tipe' => $tipe,
                'id_kategori' => $kategori,
                'stok' => $stok,
                'harga' => $harga,
            ];

            $conn->begin_transaction();

            $query = $db->update('sepeda', $data_sepeda, $id_sepeda);

            if ($query) {
                $conn->commit();
                echo "<div class='alert alert-success mt-3' role='alert'>Data berhasil diperbaharui</div>";
            } else {
                $conn->rollback();
                echo "<div class='alert alert-danger mt-3' role='alert'>Error: " . $conn->error . "</div>";
            }
            $result = $db->select("sepeda", ['AND id_sepeda=' => $id_sepeda]);
        } else {
            $result = $db->select("sepeda", ['AND id_sepeda=' => $id_sepeda]);
        }
        $sepeda = $result[0];
    ?>
    <div class="container">
        <h1 class="text-center mt-5">Ubah data sepeda</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="nameInput">Nama Sepeda</label>
                <input type="text" class="form-control" id="nameInput" name="nama_sepeda" placeholder="Masukkan nama sepeda" required value="<?php echo $sepeda['nama_sepeda'] ?>">
            </div>
            <div class="form-group">
                <label for="merkInput">Merk</label>
                <input type="text" class="form-control" id="merkInput" name="merk" placeholder="Masukkan merk" required value="<?php echo $sepeda['merk'] ?>">
            </div>
            <div class="form-group">
                <label for="tahunInput">Tahun Produksi</label>
                <input type="number" class="form-control" id="tahunInput" name="tahun_produksi" placeholder="Masukkan tahun produksi" required value="<?php echo $sepeda['tahun_produksi'] ?>">
            </div>
            <div class="form-group">
                <label for="namaTipe">Nama Tipe</label>
                <?php
                    $namaTipe = $conn->query("SELECT id_tipe, nama_tipe FROM tipe");
                    echo "<select class='form-control' id='namaTipe' name='id_tipe' required>";
                    echo "<option value=''>Pilih Tipe</option>"; 
                    while ($row = $namaTipe->fetch_assoc()) {
                        $selected = ($sepeda['id_tipe'] == $row['id_tipe']) ? 'selected' : '';
                        echo "<option value='{$row['id_tipe']}' $selected>{$row['nama_tipe']}</option>";
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
                        $selected = ($sepeda['id_kategori'] == $row['id_kategori']) ? 'selected' : '';
                        echo "<option value='{$row['id_kategori']}'$selected>{$row['nama_kategori']}</option>";
                    }
                    echo "</select>";
                ?>
            </div>
            <div class="form-group">
                <label for="stokInput">Stok</label>
                <input type="number" class="form-control" id="stokInput" name="stok" placeholder="Masukkan jumlah stok" required value="<?php echo $sepeda['stok'] ?>">
            </div>
            <div class="form-group">
                <label for="hargaInput">Harga</label>
                <input type="number" class="form-control" id="hargaInput" name="harga" placeholder="Masukkan harga" required value="<?php echo $sepeda['harga'] ?>">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="index.php" class="btn btn-info">Kembali</a>
        </form>

        <?php
            $conn->close();
        ?>
    </div>
</body>
</html>
