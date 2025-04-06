<?php
require_once __DIR__ . '/../../dao/TextMaterialDao.php';

$dao = new TextMaterialDao();
$newTextMaterial = [
    'material_id' => 1,
    'title' => 'Pendahuluan Kimia',
    'content' => 'Ini adalah konten pengantar kimia...',
    'image_path' => 'images/kimia1.jpg'
];

$id = $dao->insertTextMaterial($newTextMaterial);
echo "Inserted TextMaterial with ID: $id\n";
