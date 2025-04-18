<?php
require_once 'BaseDao.php';

class UserDao extends BaseDao {

    public function insert($table, $data) {
        // Menyusun query insert dinamis
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";

        // Persiapkan statement dan bind parameters
        $stmt = $this->conn->prepare($sql);

        // Bind data
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Eksekusi dan cek
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Kembalikan ID terakhir yang dimasukkan
        } else {
            return false; // Jika gagal
        }
    }

        // Fungsi untuk update data user
    public function updateUser($data, $id) {
        // Menentukan nama tabel dan kolom ID
        $table = 'user'; // Nama tabel
        $idField = 'user_id'; // Kolom ID
            
        // Memanggil fungsi update dari BaseDao
        return $this->update($table, $data, $idField, $id);
    }
}

