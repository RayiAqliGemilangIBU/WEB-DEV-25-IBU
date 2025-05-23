<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthService {

    private $userDao;

    public function __construct() {
        $this->userDao = new UserDao();
    }

    public function login($email, $password) {
        $user = $this->userDao->getUserByEmail($email);

        if (!$user) {
            return ['error' => 'User not found'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['error' => 'Invalid password'];
        }

        $payload = [
            'sub' => $user['user_id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + (60 * 60 * 24) // expires in 1 day
        ];

        $jwt = JWT::encode($payload, Database::JWT_SECRET(), 'HS256');

        return [
            'token' => $jwt,
            'user' => [
                'user_id' => $user['user_id'],
                'email' => $user['email'],
                'name' => $user['name'],
                'role' => $user['role']
            ]
        ];
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
?>
