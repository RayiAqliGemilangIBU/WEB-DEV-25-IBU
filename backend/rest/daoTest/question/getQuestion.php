<?php
require_once __DIR__ . '/../../dao/QuestionDao.php';

$questionDao = new QuestionDao();
$question = $questionDao->getQuestionById(1);
print_r($question);

$all = $questionDao->getAllQuestions();
print_r($all);
