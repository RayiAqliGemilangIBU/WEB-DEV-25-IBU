<?php
require_once __DIR__ . '/../../dao/OptionItemDao.php';

$optionDao = new OptionItemDao();
$updated = $optionDao->updateOptionItem(['option_text' => 'Updated Option Text'], 1);
echo $updated ? "Option updated successfully" : "Update failed";
