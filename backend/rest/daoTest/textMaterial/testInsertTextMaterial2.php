<?php
require_once __DIR__ . '/../../dao/TextMaterialDao.php';

$dao = new TextMaterialDao();
$newTextMaterial = [

    'title' => 'Pendahuluan Kimia 2',
    'content' => 'Ini adalah konten pengantar kimia... 2',
    'image_path' => 'images 2/kimia1.jpg'
];

$id = $dao->insertTextMaterial($newTextMaterial);
echo "Inserted TextMaterial with ID: $id\n";
