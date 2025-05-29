<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../config/config.php';

function requireAuth() {
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        Flight::halt(401, 'Authorization header missing');
    }

    $authHeader = $headers['Authorization'];

    if (strpos($authHeader, 'Bearer ') !== 0) {
        Flight::halt(401, 'Invalid Authorization format');
    }

    $jwt = substr($authHeader, 7);

    try {
        $decoded = JWT::decode($jwt, new Key(Database::JWT_SECRET(), 'HS256'));

        // Set in Flight after decode
        Flight::set('jwt_decoded', $decoded);
        Flight::set('user', (array)$decoded); 

    } catch (Exception $e) {
        Flight::halt(401, 'Invalid token: ' . $e->getMessage());
    }
}

function requireRole($requiredRole) {
    $user = Flight::get('user');
    if (!$user || !isset($user['role']) || $user['role'] !== $requiredRole) {
        Flight::halt(403, 'Forbidden: Insufficient permissions');
    }
}



Flight::map('middleware', 'requireAuth');
