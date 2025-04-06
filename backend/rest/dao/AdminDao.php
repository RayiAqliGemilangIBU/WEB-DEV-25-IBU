<?php
require_once 'BaseDao.php';

class AdminDao extends BaseDao {

    public function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";

        $stmt = $this->conn->prepare($sql);

        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); 
        } else {
            return false;
        }
    }
}