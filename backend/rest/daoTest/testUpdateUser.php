<?php
require_once __DIR__ . '/../dao/UserDao.php';

$userDao = new UserDao();

// Data yang ingin diupdate
$data = [
    'name' => 'felix',  // Misal: ganti username
    'email' => 'felix@example.com',  // Misal: ganti email
    'password' => 'new_secure_password',
    'role' => 'admin'
];

// ID user yang ingin diupdate
$userId = 1; // Misalnya user dengan ID 1

// Mengupdate user
$updated = $userDao->updateUser($data, $userId);

if ($updated) {
    echo "User berhasil diupdate!";
} else {
    echo "Gagal mengupdate user.";
}
?>
