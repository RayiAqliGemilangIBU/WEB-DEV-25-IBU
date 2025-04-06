<?php
require_once '../dao/UserDao.php';
require_once '../dao/AdminDao.php';
require_once '../dao/StudentDao.php';

$userDao = new UserDao();
$adminDao = new AdminDao();
$studentDao = new StudentDao();

// Insert User
$userDao->insert([
    'username' => 'admin1',
    'email' => 'admin1@example.com',
    'password' => password_hash('password123', PASSWORD_DEFAULT),
    'full_name' => 'Administrator One',
    'role' => 'admin'
]);

// Get last inserted user_id
$lastId = $userDao->pdo->lastInsertId();

// Insert to Admin Table
$adminDao->insert($lastId);

// Cek isi User
$users = $userDao->findAll('User');
print_r($users);
