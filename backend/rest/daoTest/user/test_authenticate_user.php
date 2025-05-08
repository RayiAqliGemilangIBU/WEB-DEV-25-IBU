<?php
require_once __DIR__ . '/../../dao/UserDao.php';

echo "=== Testing User Authentication ===\n";

$userDao = new UserDao();

// Data login untuk diuji
$email = 'smith@admin.com'; // Ganti dengan email yang valid di database
$password = 'adminpass';   // Ganti dengan password yang sesuai (plaintext)

echo "1. Attempting login for email = $email...\n";

$result = $userDao->authenticate($email, $password);

if ($result) {
    echo "Login successful!\n";
    print_r($result);
} else {
    echo "Login failed. Invalid email or password.\n";
}
