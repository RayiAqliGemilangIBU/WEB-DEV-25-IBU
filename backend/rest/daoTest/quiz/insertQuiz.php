<?php
require_once __DIR__ . '/../../dao/QuizDao.php';

$quizDao = new QuizDao();

// Contoh insert quiz
$quizId = $quizDao->insertQuiz([
    'material_id' => 1, // pastikan ID ini ada di DB
    'title' => 'Quiz: Atomic Structure'
]);

echo "Inserted Quiz ID: " . $quizId . PHP_EOL;
