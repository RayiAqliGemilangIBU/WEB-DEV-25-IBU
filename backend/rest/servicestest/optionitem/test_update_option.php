<?php
require_once __DIR__ . '/../../services/OptionItemService.php';

$service = new OptionItemService();

$optionId = 1; // ID yang valid
$updatedData = [
    'option_text' => 'Updated Option A',
    'is_correct' => 1
];

$result = $service->updateOption($optionId, $updatedData);
print_r($result);
