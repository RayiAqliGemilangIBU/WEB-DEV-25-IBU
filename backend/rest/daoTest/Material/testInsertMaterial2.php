<?php
require_once __DIR__ . '/../../dao/MaterialDao.php';

$materialDao = new MaterialDao();

$data = [
    'title' => ' 2 Materi Kimia Dasar',
    'description' => ' 2 Pengenalan tentang atom dan molekul',
    'created_at' => date('Y-m-d H:i:s')
];

$insertedId = $materialDao->insertMaterial($data);
echo "Berhasil menambahkan materi dengan ID: $insertedId\n";
