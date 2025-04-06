<?php
require_once __DIR__ . '/../../dao/MaterialDao.php';

$materialDao = new MaterialDao();
$id = 1; // Sesuaikan ID materi yang ingin dicek
$material = $materialDao->getMaterialById($id);

echo "Detail materi dengan ID $id:\n";
print_r($material);
