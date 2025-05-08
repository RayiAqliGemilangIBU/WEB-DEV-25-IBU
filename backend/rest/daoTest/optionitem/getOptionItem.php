<?php
require_once __DIR__ . '/../../dao/OptionItemDao.php';

$optionDao = new OptionItemDao();
$option = $optionDao->getOptionItemById(1);
print_r($option);

$all = $optionDao->getAllOptionItems();
print_r($all);
