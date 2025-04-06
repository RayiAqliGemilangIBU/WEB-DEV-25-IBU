<?php
require_once __DIR__ . '/../config/config.php';

class BaseDao {
    protected $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_map(fn($col) => ":$col", array_keys($data)));
        $stmt = $this->conn->prepare("INSERT INTO $table ($columns) VALUES ($placeholders)");
        $stmt->execute($data);
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
        $fields = implode(", ", array_map(fn($col) => "$col = :$col", array_keys($data)));
        $stmt = $this->conn->prepare("UPDATE $table SET $fields WHERE $idField = :id");
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($table, $idField, $id) {
        $stmt = $this->conn->prepare("DELETE FROM $table WHERE $idField = ?");
        return $stmt->execute([$id]);
    }
}
