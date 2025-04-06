<?php
require_once __DIR__ . '/../dao/UserDao.php';

$userDao = new UserDao();

// INSERT user baru
$newUserId = $userDao->insert('User', [
    'name' => 'nabil',
    'email' => 'nabil@example.com',
    'password' => password_hash('123456', PASSWORD_DEFAULT),
    'role' => 'student'
]);


echo "User berhasil dibuat dengan ID: $newUserId\n";

// AMBIL semua user
$users = $userDao->findAll('User');
foreach ($users as $user) {
    echo "ID: {$user['user_id']} - Nama: {$user['name']} - Email: {$user['email']} - Role: {$user['role']}\n";
}
