<?php

require_once __DIR__ . '/../../services/QuestionService.php';

$questionService = new QuestionService();

// Data pertanyaan untuk uji coba
$questionData = [
    'quiz_id' => 1,
    'header' => 'What is the capital of France?',
    'explanation' => 'The capital of France is Paris.'
];

try {
    // Cek jumlah pertanyaan sebelum penambahan
    echo "Attempting to add question...\n";
    $questionService->createQuestion($questionData);
    echo "Question added successfully.\n";

    // Ambil dan tampilkan semua pertanyaan
    $allQuestions = $questionService->getAllQuestions();
    print_r($allQuestions);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
