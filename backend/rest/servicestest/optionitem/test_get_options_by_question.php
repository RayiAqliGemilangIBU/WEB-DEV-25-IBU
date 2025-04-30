<?php
require_once __DIR__ . '/../../services/OptionItemService.php';

$service = new OptionItemService();

$questionId = 1;
$options = $service->getOptionsByQuestionId($questionId);
print_r($options);
