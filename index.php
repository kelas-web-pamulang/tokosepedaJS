<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Toko Sepeda SJ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        footer {
            background-color: rgba(92, 102, 214, 1.5);
            color: white;
            padding: 20px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">List Daftar Sepeda Toko SJ</h1>
        <div class="row">
            <div class="d-flex justify-content-between">
                <form action="" method="get" class="d-flex">
                    <input class="form-control" placeholder="Cari Data" name="search"/>
                    <select name="search_by">
                        <option value="">Search All</option>
                        <option value="nama_sepeda">Nama Sepeda</option>
                        <option value="merk">Merk</option>
                        <option value="tahun_produksi">Tahun Produksi</option>
                    </select>
                    <button type="submit" class="btn btn-success btn-sm">Cari</button>
                </form>
                <a href="insert.php" class="ml-auto mb-2"><button class="btn btn-success">Tambah Data</button></a>
            </div>
            <table class="table">
                <thead>
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
                <?php
                date_default_timezone_set('Asia/Jakarta');
                ini_set('display_errors', '1');
                ini_set('display_startup_errors', '1');
                error_reporting(E_ALL);

                require_once 'config_db.php';

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
                    }  else if ($search_by == 'merk') {
                        $conditional['AND merk LIKE'] = "%$search%";
                    }
                } else if (isset($_GET['delete'])) {
                    $query = $db->update('sepeda',[
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
                foreach ($conditional as $key => $value) {
                $query .= " $key '$value'";
                    }
                }

                $result = $conn->query($query);
                $totalRows = $result->num_rows;

                if ($totalRows > 0) {
                    foreach ($result as $key => $row) {
                        echo "<tr>";
                        echo "<td>".($key + 1)."</td>";
                        echo "<td>".$row['nama_sepeda']."</td>";
                        echo "<td>".$row['merk']."</td>";
                        echo "<td>".$row['tahun_produksi']."</td>";
                        echo "<td>".$row['nama_tipe']."</td>";
                        echo "<td>".$row['nama_kategori']."</td>";
                        echo "<td>".$row['stok']."</td>";
                        echo "<td>".$row['harga']."</td>";
                        echo "<td>".$row['created_at']."</td>";
                        echo "<td><a class='btn btn-sm btn-info' href='update.php?id=$row[id_sepeda]'>Update</a></td>";
                        echo "<td><a class='btn btn-sm btn-danger delete-button' href='index.php?delete=$row[id_sepeda]'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No Data</td></tr>";
                }

                $db->close();
                ?>
            </table>
        </div>
    </div>
    <footer class="text-center mt-5">Tugas Project-Praktisi Mengajar dibuat oleh Alfa Azriansah Yasin & Dery Saputra</footer>
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
