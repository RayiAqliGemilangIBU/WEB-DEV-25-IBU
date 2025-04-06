<?php
require_once __DIR__ . '/../../dao/MaterialDao.php';

$materialDao = new MaterialDao();

$id = 2; // ID materi yang ingin dihapus
$success = $materialDao->deleteMaterial($id);
echo $success ? "Materi berhasil dihapus\n" : "Gagal menghapus materi\n";
