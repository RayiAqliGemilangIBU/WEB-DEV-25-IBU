<?php
require_once __DIR__ . '/../../services/AuthService.php';

$authService = new AuthService();

// Test login
$email = 'ucok@test.com';
$password = 'ucok'; // Ensure this matches the stored password (hashed) in DB

// Attempt to login
$response = $authService->authenticateUser($email, $password); //

if ($response['status']) {
    echo "Login successful!\n";
    echo "JWT: " . $response['token'] . "\n";
} else {
    echo "Login failed: " . $response['message'] . "\n";
}
