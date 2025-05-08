<?php
require_once __DIR__ . '/../../dao/QuestionDao.php';

$questionDao = new QuestionDao();

// Contoh insert question
$questionId = $questionDao->insertQuestion([
    'quiz_id' => 1, // pastikan ID ini ada di DB
    'header' => 'What is the charge of an electron?',
    'explanation' => 'Electrons are negatively charged subatomic particles.'
]);

echo "Inserted Question ID: " . $questionId . PHP_EOL;
