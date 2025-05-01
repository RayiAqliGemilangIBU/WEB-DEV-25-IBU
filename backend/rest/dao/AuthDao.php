<?php
require_once 'BaseDao.php';

class AuthDao extends BaseDao {

    protected $table = 'user'; // Table untuk data pengguna
    protected $idField = 'user_id';

    // Mengecek kredensial pengguna
    public function checkCredentials($email, $password) {
        // Query untuk mengecek email dan password pengguna
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE email = ? AND password = ?");
        $stmt->execute([$email, $password]);
        return $stmt->fetch(); // Mengembalikan data pengguna jika ditemukan
    }

    // Menyimpan token untuk pengguna
    public function storeToken($userId, $token) {
        // Query untuk menyimpan token ke database
        $stmt = $this->conn->prepare("INSERT INTO user_tokens (user_id, token) VALUES (?, ?)");
        return $stmt->execute([$userId, $token]);
    }

    // Mengambil token berdasarkan user_id
    public function getToken($userId) {
        // Query untuk mengambil token berdasarkan user_id
        $stmt = $this->conn->prepare("SELECT token FROM user_tokens WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn(); // Mengambil token
    }

    // Validasi token yang diberikan
    public function validateToken($token) {
        // Query untuk memeriksa apakah token valid
        $stmt = $this->conn->prepare("SELECT user_id FROM user_tokens WHERE token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch(); // Mengembalikan user_id jika token valid
    }
}
