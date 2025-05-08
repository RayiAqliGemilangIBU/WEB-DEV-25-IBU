<?php
require_once __DIR__ . '/../../dao/UserDao.php';

echo "=== Testing User Registration ===\n";

$userDao = new UserDao();
$hashedPassword = password_hash("testpass123", PASSWORD_BCRYPT);

$userData = [
    "name" => "Test User",
    "email" => "testuser2@example.com",
    "password" => $hashedPassword,
    "role" => "Student",
    "created_at" => date("Y-m-d H:i:s")
];

$result = $userDao->insert("user", $userData);

if ($result) {
    echo "User registered successfully with ID: $result\n";
} else {
    echo "Failed to register user.\n";
}
//php backend/rest/daoTest/user/test_register_user.php
