<?php
require_once __DIR__ . '/../../services/OptionItemService.php';

$service = new OptionItemService();

echo "Creating option...\n";
$data = [
    'question_id' => 1, // pastikan question_id ini ada
    'option_text' => 'Option A',
    'is_correct' => 0
];

$result = $service->createOption($data);
print_r($result);
