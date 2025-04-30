<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';

echo "=== Testing Get Detailed Answers By User ===\n";

$service = new StudentAnswerService();

// Ganti dengan user_id yang valid
$user_id = 2;

echo "Fetching detailed answers for student with ID = $user_id...\n";
$details = $service->getDetailedAnswersByUser($user_id);
print_r($details);
