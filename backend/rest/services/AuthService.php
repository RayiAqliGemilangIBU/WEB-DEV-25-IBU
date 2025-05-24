<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../dao/AuthDao.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
class AuthService {
    private $authDao;

    public function __construct() {
        $this->authDao = new AuthDao();
    }

    public function login($email, $password) {
        $user = $this->authDao->getUserByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        unset($user['password']); // Jangan kirim password

        $payload = [
            'sub' => $user['user_id'],
            'role' => $user['role'],
            'email' => $user['email'],
            'exp' => time() + (60 * 30) // Token 5 menit
        ];

        $jwt = JWT::encode($payload, Database::JWT_SECRET(), 'HS256');

        return ['token' => $jwt, 'user' => $user];
    }

    
    public function decodeToken($jwt) {
        try {
            $decoded = JWT::decode($jwt, new Key(Database::JWT_SECRET(), 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            return ['error' => 'Invalid token'];
        }
    }


}
