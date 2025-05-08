<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';

echo "=== Testing Get Answer By ID ===\n";

$service = new StudentAnswerService();

try {
    $answerId = 3; // Ganti dengan ID yang valid di database Anda
    echo "\n1. Fetching answer with ID = $answerId...\n";
    $answer = $service->getAnswerById($answerId);
    
    if ($answer) {
        print_r($answer);
    } else {
        echo "No answer found with ID = $answerId.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
