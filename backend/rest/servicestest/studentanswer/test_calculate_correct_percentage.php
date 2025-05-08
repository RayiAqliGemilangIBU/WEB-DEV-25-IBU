<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';

echo "=== Testing Calculate Correct Answer Percentage ===\n";

$service = new StudentAnswerService();

// Ganti dengan user_id yang valid
$user_id = 1;

echo "Calculating correct answer percentage for student ID = $user_id...\n";
$percentage = $service->calculateCorrectAnswerPercentage($user_id);
echo "Correct percentage: $percentage%\n";
