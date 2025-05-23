<?php
require_once __DIR__ . '/../services/AuthService.php';

Flight::route('POST /auth/login', function() {
    $data = Flight::request()->data->getData();

    if (!isset($data['email']) || !isset($data['password'])) {
        Flight::json(['error' => 'Email and password are required'], 400);
        return;
    }

    $authService = new AuthService();
    $result = $authService->login($data['email'], $data['password']);

    if (isset($result['error'])) {
        Flight::json($result, 401);
    } else {
        Flight::json($result, 200);
    }
});
