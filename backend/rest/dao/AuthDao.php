<?php
require_once __DIR__ . '/BaseDao.php';

require_once __DIR__ . '/../config/config.php'; // Sesuaikan jika path config berbeda


class AuthDao {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
}