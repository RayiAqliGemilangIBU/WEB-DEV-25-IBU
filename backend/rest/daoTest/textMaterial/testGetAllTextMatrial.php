<?php
require_once __DIR__ . '/../../dao/TextMaterialDao.php';

$dao = new TextMaterialDao();
$materials = $dao->getAllTextMaterials();
print_r($materials);
