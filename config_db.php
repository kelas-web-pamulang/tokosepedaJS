<?php

class ConfigDB
{
    private $host = 'localhost';
    private $db_name = 'db_sepeda'; // Updated to reflect the bicycle shop database
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect()
    {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        return $this->conn;
    }

    public function close() {
        $this->conn->close();
    }

    public function select($table, $where = [])
    {
        $query = "SELECT id_sepeda, nama_sepeda, merk, tahun_produksi, id_tipe, id_kategori, stok, harga, created_at FROM $table WHERE deleted_at IS NULL";

        foreach ($where as $key => $value) {
            $query .= " $key '$value'";
        }

        $result = $this->conn->query($query);

        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function update($table, $data, $id)
    {
        $updated_at = date('Y-m-d H:i:s');
        $query = "UPDATE $table SET ";
        foreach ($data as $key => $value) {
            $query .= "$key = '$value', ";
        }
        $query .= "updated_at = '$updated_at' WHERE id_sepeda='$id'";

        return $this->conn->query($query);
    }
}
