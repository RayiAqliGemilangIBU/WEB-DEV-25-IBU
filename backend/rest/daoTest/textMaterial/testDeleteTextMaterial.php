<?php
require_once __DIR__ . '/../../dao/TextMaterialDao.php';

$dao = new TextMaterialDao();
$result = $dao->deleteTextMaterial(4); // Ubah ID sesuai kebutuhan (text_id)
echo $result ? "Delete sukses\n" : "Delete gagal\n";
