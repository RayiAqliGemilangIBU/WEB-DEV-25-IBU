<?php
require_once __DIR__ . '/../../dao/MaterialDao.php';

$materialDao = new MaterialDao();

$data = [
    'title' => 'Materi Kimia Dasar',
    'description' => 'Pengenalan tentang atom dan molekul',
    'created_at' => date('Y-m-d H:i:s')
];

$insertedId = $materialDao->insertMaterial($data);
echo "Berhasil menambahkan materi dengan ID: $insertedId\n";
