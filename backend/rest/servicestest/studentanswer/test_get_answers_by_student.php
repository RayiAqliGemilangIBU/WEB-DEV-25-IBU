<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';

echo "=== Testing Get Answers by Student ID ===\n";

$service = new StudentAnswerService();

// Ganti dengan user_id yang valid
$user_id = 2;

echo "Fetching answers for student with ID = $user_id...\n";
$answers = $service->getAnswersByStudentId($user_id);
print_r($answers);
