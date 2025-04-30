<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';

echo "=== Testing Get Answers By Option ID ===\n";

$service = new StudentAnswerService();

try {
    $optionId = 1; // Ganti dengan ID opsi yang ingin diperiksa
    echo "\n1. Fetching answers with selected_option_id = $optionId...\n";
    $answers = $service->getAnswersByOptionId($optionId);
    
    if ($answers) {
        print_r($answers);
    } else {
        echo "No answers found for selected_option_id = $optionId.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
