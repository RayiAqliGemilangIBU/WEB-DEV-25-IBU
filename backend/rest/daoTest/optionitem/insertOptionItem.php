<?php
require_once __DIR__ . '/../../dao/OptionItemDao.php';

$optionDao = new OptionItemDao();

// Contoh insert options untuk soal di atas
$options = [
    ['question_id' => 1, 'option_text' => 'Positive', 'is_correct' => 0],
    ['question_id' => 1, 'option_text' => 'Negative', 'is_correct' => 1],
    ['question_id' => 1, 'option_text' => 'Neutral', 'is_correct' => 0],
    ['question_id' => 1, 'option_text' => 'No charge', 'is_correct' => 0],
];

foreach ($options as $opt) {
    $optionId = $optionDao->insertOptionItem($opt);
    echo "Inserted Option ID: " . $optionId . PHP_EOL;
}
