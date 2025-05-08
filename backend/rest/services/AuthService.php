<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../util/JwtHelper.php';
require_once __DIR__ . '/../config/config.php';

use Firebase\JWT\JWT;

class AuthService {
    private $userDao;
    private $jwtHelper;

    public function __construct() {
        $this->userDao = new UserDao(Database::connect());
        $this->jwtHelper = new JwtHelper();
    }

    // Fungsi untuk login
    public function authenticateUser($email, $password) {
        // Cek apakah email sudah terdaftar
        $user = $this->userDao->getUserByEmail($email);
        if (!$user) {
            return ["status" => false, "message" => "Email atau password salah"];
        }

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Jika password valid, buat JWT
            $jwt = $this->jwtHelper->generateJwt($user);
            return ["status" => true, "token" => $jwt];
        } else {
            return ["status" => false, "message" => "Email atau password salah"];
        }
    }

    // Fungsi untuk register
    public function registerUser($email, $password, $name, $role) {
        // Hash password sebelum disimpan
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $data = [
            'email' => $email,
            'password' => $hashedPassword,
            'name' => $name,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Periksa apakah email sudah terdaftar
        $existingUser = $this->userDao->getUserByEmail($email);
        if ($existingUser) {
            return ["status" => false, "message" => "Email sudah terdaftar"];
        }

        // Insert data user baru
        $this->userDao->insert('user', $data);
        return ["status" => true, "message" => "User berhasil didaftarkan"];
    }

    // Fungsi untuk memverifikasi JWT dan memberikan akses berdasarkan role
    public function verifyAccess($jwt, $requiredRole) {
        $userData = $this->jwtHelper->validateJwt($jwt);
        
        if (!$userData) {
            return ["status" => false, "message" => "Token tidak valid"];
        }

        if ($userData['role'] !== $requiredRole && $requiredRole !== 'Any') {
            return ["status" => false, "message" => "Akses ditolak"];
        }

        return ["status" => true, "data" => $userData];
    }

    public function logoutUser($token){
    // Belum ada mekanisme blacklist token, jadi kita anggap logout berhasil
    return [
        'status' => true,
        'message' => 'User logged out successfully'
    ];
}

}
