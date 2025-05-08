<?php
require_once __DIR__ . '/../../dao/OptionItemDao.php';

$optionDao = new OptionItemDao();
$deleted = $optionDao->deleteOptionItem(1);
echo $deleted ? "Option deleted" : "Deletion failed";
