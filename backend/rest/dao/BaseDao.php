<?php
require_once __DIR__ . '/../config/config.php';

class BaseDao {
    protected $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function insert($table, $data) {
        $fields = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_map(fn($f) => ":$f", array_keys($data)));
        $stmt = $this->pdo->prepare("INSERT INTO $table ($fields) VALUES ($placeholders)");
        $stmt->execute($data);
        return $this->pdo->lastInsertId();
    }

    public function findAll($table) {
        $stmt = $this->pdo->query("SELECT * FROM $table");
        return $stmt->fetchAll();
    }

    public function findById($table, $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM $table WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function delete($table, $id) {
        $stmt = $this->pdo->prepare("DELETE FROM $table WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function update($table, $id, $data) {
        $fields = implode(", ", array_map(fn($key) => "$key = :$key", array_keys($data)));
        $data['id'] = $id;
        $stmt = $this->pdo->prepare("UPDATE $table SET $fields WHERE id = :id");
        return $stmt->execute($data);
    }
}
