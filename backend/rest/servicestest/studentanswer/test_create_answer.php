<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';

echo "=== Testing Create Answer ===\n";

$service = new StudentAnswerService();


try {
    echo "\n1. Creating answer...\n";
    $data = [
        'user_id' => 1,
        'question_id' => 2,
        'selected_option_id' => 1
    ];
    $created = $service->createAnswer($data);
    print_r($created);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
