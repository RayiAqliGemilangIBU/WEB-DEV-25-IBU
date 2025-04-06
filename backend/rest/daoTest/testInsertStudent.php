<?php
require_once __DIR__ . '/../dao/StudentDao.php';

$studentDao = new StudentDao();

// Data student yang ingin ditambahkan
$data = [
    'user_id' => 3  // ID dari tabel User
];

// Menambahkan student
$studentId = $studentDao->insertStudent($data);

echo "Student berhasil ditambahkan dengan ID: $studentId";
?>
