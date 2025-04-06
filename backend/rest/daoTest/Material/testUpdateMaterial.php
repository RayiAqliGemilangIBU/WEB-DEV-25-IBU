<?php
require_once __DIR__ . '/../../dao/MaterialDao.php';

$materialDao = new MaterialDao();

$id = 1; // ID materi yang ingin diubah
$data = [
    'title' => 'Materi Kimia Dasar', //Materi Kimia Revisi
    'description' => 'Pengenalan tentang atom dan molekuu' //Materi telah diperbarui
];

$success = $materialDao->updateMaterial($data, $id);
echo $success ? "Materi berhasil diperbarui\n" : "Gagal memperbarui materi\n";
