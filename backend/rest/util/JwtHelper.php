<?php
require_once __DIR__ . '/../vendor/autoload.php'; //
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtHelper {
    private $secretKey = 'UIIAUUIIA';

    public function generateJwt($userData) {
        $payload = [
            'sub' => $userData['user_id'],
            'email' => $userData['email'],
            'role' => $userData['role'],
            'iat' => time(),
            'exp' => time() + (10 * 365 * 24 * 60 * 60) 
        ];

        return JWT::encode($payload, $this->secretKey, ' ');
    }

    public function validateJwt($token) {
        try {
            return (array) JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (Exception $e) {
            return false;
        }
    }
}
