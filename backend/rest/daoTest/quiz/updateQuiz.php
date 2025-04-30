<?php
require_once __DIR__ . '/../../dao/QuizDao.php';

$quizDao = new QuizDao();
$updated = $quizDao->updateQuiz(['title' => 'Updated Quiz Title'], 1);
echo $updated ? "Quiz updated successfully" : "Quiz update failed";

