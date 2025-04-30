<?php
require_once __DIR__ . '/../dao/MaterialDao.php';

class MaterialService {
    private $dao;

    public function __construct() {
        $this->dao = new MaterialDao();
    }

    public function createMaterial($title, $description) {
        // Validasi
        if (empty($title) || strlen(trim($title)) < 5) {
            throw new Exception("Title harus minimal 5 karakter.");
        }
        if (empty($description)) {
            throw new Exception("Deskripsi tidak boleh kosong.");
        }

        // Sanitasi
        $title = trim(strip_tags($title));
        $description = trim(strip_tags($description));

        // Cek duplikat
        $existing = $this->dao->getMaterialByTitle($title);
        if ($existing) {
            throw new Exception("Material dengan judul ini sudah ada.");
        }

        // Simpan
        return $this->dao->insertMaterial([
            'title' => $title,
            'description' => $description
        ]);
    }
}
