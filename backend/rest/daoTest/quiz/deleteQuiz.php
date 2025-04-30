<?php
require_once __DIR__ . '/../../dao/QuizDao.php';

$quizDao = new QuizDao();
$deleted = $quizDao->deleteQuiz(1);
echo $deleted ? "Quiz deleted" : "Quiz deletion failed";
