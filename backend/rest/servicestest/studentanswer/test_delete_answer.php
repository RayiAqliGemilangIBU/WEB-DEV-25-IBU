<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';

echo "=== Testing Delete Student Answer ===\n";

$service = new StudentAnswerService();

try {
    $answerId = 3; // ID jawaban yang ingin dihapus

    echo "\n1. Deleting answer with ID = $answerId...\n";
    $result = $service->deleteAnswer($answerId);

    echo $result ? "Deletion successful.\n" : "Deletion failed.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
