<?php
require_once __DIR__ . '/../../services/OptionItemService.php';

$service = new OptionItemService();

$optionId = 3; // ID yang valid
$result = $service->deleteOptionItem($optionId);
print_r($result);
