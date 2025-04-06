<?php
require_once __DIR__ . '/../dao/StudentDao.php';

$studentDao = new StudentDao();

// ID student yang ingin dihapus
$studentId = 2;

// Menghapus student
if ($studentDao->deleteStudent($studentId)) {
    echo "Student dengan ID $studentId berhasil dihapus.";
} else {
    echo "Gagal menghapus student.";
}
?>
