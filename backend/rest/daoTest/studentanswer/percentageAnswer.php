<?php
require_once __DIR__ . '/../../dao/StudentAnswerDao.php';

$dao = new StudentAnswerDao();

// Menghitung persentase jawaban benar untuk user dengan ID 2
$user_id = 2;
$percentage = $dao->calculateCorrectAnswerPercentage($user_id);
echo "Persentase jawaban benar untuk user ID $user_id: $percentage%\n";
