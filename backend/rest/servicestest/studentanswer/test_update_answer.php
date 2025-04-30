<?php
require_once __DIR__ . '/../../services/StudentAnswerService.php';
//Tujuan: Memperbarui jawaban tertentu berdasarkan answer_id.
echo "=== Testing Update Student Answer ===\n";

$service = new StudentAnswerService();

try {
    $answerId = 3; // ID jawaban yang ingin diperbarui
    $updatedData = [
        'selected_option_id' => 2 // Ganti dengan opsi baru yang ingin diuji
    ];

    echo "\n1. Updating answer with ID = $answerId...\n";
    $result = $service->updateAnswer($updatedData, $answerId);

    echo $result ? "Update successful.\n" : "Update failed.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
