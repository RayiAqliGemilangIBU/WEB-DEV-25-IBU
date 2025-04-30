<?php
require_once __DIR__ . '/../../dao/QuizDao.php';

$quizDao = new QuizDao();
$quiz = $quizDao->getQuizById(1);
print_r($quiz);

$all = $quizDao->getAllQuizzes();
print_r($all);
