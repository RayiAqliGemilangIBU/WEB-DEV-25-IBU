<?php
require_once __DIR__ . '/../../dao/TextMaterialDao.php';

$dao = new TextMaterialDao();
$updatedTextMaterial = [
    'material_id' => 1,
    'title' => 'Pendahuluan Kimia Update ',
    'content' => 'Konten telah diubah',
    'image_path' => 'images/update.jpg'
];

$result = $dao->updateTextMaterial($updatedTextMaterial, 1); // ID text_id
echo $result ? "Update sukses\n" : "Update gagal\n";
