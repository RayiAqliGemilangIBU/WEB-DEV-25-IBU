<?php
require_once __DIR__ . '/../dao/StudentDao.php';

$studentDao = new StudentDao();

// Mendapatkan semua student
$students = $studentDao->getAllStudents();

foreach ($students as $student) {
    echo "Student ID: " . $student['student_id'] . " | User ID: " . $student['user_id'] . "<br>";
}
?>
