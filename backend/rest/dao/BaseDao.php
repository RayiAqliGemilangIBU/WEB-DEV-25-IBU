<?php
require_once __DIR__ . '/../config/config.php';

class BaseDao {
    protected $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

 public function insert($table, $data) {
    // Escape kolom menggunakan backtick untuk menghindari konflik dengan keyword SQL
    $columns = implode(", ", array_map(fn($col) => "`$col`", array_keys($data)));
    $placeholders = ":" . implode(", :", array_keys($data));

    // Menyiapkan query insert
    $sql = "INSERT INTO `$table` ($columns) VALUES ($placeholders)";
    $stmt = $this->conn->prepare($sql);



    // Menjalankan query
    $stmt->execute($data);

    // Mengembalikan ID terakhir yang dimasukkan
    return $this->conn->lastInsertId();
}

    public function findAll($table) {
        $stmt = $this->conn->query("SELECT * FROM $table");
        return $stmt->fetchAll();
    }

    public function findById($table, $idField, $id) {
        $stmt = $this->conn->prepare("SELECT * FROM $table WHERE $idField = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($table, $data, $idField, $id) {
        // // Menyusun kolom dan nilai untuk query UPDATE
        // $fields = implode(", ", array_map(fn($col) => "$col = :$col", array_keys($data)));
    
        // // Menyusun query UPDATE
        // $stmt = $this->conn->prepare("UPDATE $table SET $fields WHERE $idField = :id");
        
        // // Menambahkan ID ke dalam data untuk binding
        // $data['id'] = $id;
        
        // // Menjalankan query update
        // return $stmt->execute($data);
        $fields = implode(", ", array_map(fn($col) => "$col = :$col", array_keys($data)));
        $stmt = $this->conn->prepare("UPDATE $table SET $fields WHERE $idField = :id");
        $data['id'] = $id; // Pastikan 'id' adalah nama kolom yang benar
        return $stmt->execute($data);
    }
    

    public function delete($table, $idField, $id) {
        $stmt = $this->conn->prepare("DELETE FROM $table WHERE $idField = ?");
        return $stmt->execute([$id]);
    }
}
