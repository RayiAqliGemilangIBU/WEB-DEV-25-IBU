<?php
require_once __DIR__ . '/../../dao/QuestionDao.php';

$questionDao = new QuestionDao();
$updated = $questionDao->updateQuestion(['header' => 'Updated question header'], 1);
echo $updated ? "Question updated successfully" : "Update failed";
