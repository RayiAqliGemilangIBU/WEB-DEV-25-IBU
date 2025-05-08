<?php
require_once __DIR__ . '/../../dao/QuestionDao.php';

$questionDao = new QuestionDao();
$deleted = $questionDao->deleteQuestion(1);
echo $deleted ? "Question deleted" : "Deletion failed";
