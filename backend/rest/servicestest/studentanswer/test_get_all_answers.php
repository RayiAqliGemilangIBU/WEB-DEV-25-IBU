<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';
// Mengambil semua data jawaban dari tabel student_answer.
echo "=== Testing Get All Student Answers ===\n";

$service = new StudentAnswerService();

try {
    echo "\n1. Fetching all student answers...\n";
    $answers = $service->getAllAnswers();

    if ($answers) {
        foreach ($answers as $a) {
            print_r($a);
        }
    } else {
        echo "No student answers found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
