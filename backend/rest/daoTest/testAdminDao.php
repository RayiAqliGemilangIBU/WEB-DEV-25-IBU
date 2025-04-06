<?php
require_once __DIR__ . '/../dao/AdminDao.php';

$adminDao = new AdminDao();

$userId = 1; // ganti dengan ID user bertipe admin

$newAdminId = $adminDao->insert('Admin', [
    'user_id' => $userId
]);

echo "Admin berhasil dibuat dengan ID: $newAdminId\n";

// AMBIL semua admin
$admins = $adminDao->findAll('Admin');
foreach ($admins as $admin) {
    echo "Admin ID: {$admin['admin_id']}, user_id: {$admin['user_id']}\n";
}
