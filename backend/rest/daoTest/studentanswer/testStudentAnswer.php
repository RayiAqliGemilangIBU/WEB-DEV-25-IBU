<?php
require_once __DIR__ . '/../../dao/StudentAnswerDao.php';

$dao = new StudentAnswerDao();

// Insert
$data = [
    'user_id' => 2,
    'question_id' => 1,
    'selected_option_id' => 4
];
$insertedId = $dao->insertStudentAnswer($data);
echo "Inserted ID: $insertedId\n";

// Get detail by user
$answers = $dao->getDetailedAnswersByUser(2);
print_r($answers);
