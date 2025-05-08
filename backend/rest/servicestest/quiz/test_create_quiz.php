<?php
require_once __DIR__ . '/../../services/QuizService.php';

$service = new QuizService();

try {
    $quiz = $service->createQuiz([
        'title' => 'Quiz Kimia Dasar',
        'material_id' => 1
    ]);
    print_r($quiz);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
