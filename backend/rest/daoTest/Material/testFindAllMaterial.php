<?php
require_once __DIR__ . '/../../dao/MaterialDao.php';

$materialDao = new MaterialDao();
$materials = $materialDao->getAllMaterials();

echo "Daftar semua materi:\n";
print_r($materials);
