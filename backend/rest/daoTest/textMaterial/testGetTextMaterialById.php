d<?php
require_once __DIR__ . '/../../dao/TextMaterialDao.php';

$dao = new TextMaterialDao();
$material = $dao->getTextMaterialById(1); // Ubah ID jika perlu
print_r($material);
