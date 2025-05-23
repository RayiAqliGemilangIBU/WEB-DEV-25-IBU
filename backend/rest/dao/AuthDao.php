<?php
require_once __DIR__ . '/BaseDao.php';

require_once __DIR__ . '/../config/config.php'; // Sesuaikan jika path config berbeda

class AuthDao {
    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function getUserByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function verifyPassword($inputPassword, $hashedPassword) {
        return password_verify($inputPassword, $hashedPassword);
    }

    public function login($email, $password) {
        $user = $this->getUserByEmail($email);

        if (!$user) {
            return null; // Email tidak ditemukan
        }

        if (!$this->verifyPassword($password, $user['password'])) {
            return null; // Password salah
        }

        return $user; // Login berhasil
    }
}
?>