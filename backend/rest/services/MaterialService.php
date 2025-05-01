<?php
require_once __DIR__ . '/../dao/MaterialDao.php';

class MaterialService {
    private $dao;

    public function __construct() {
        $this->dao = new MaterialDao();
    }

    public function getAllMaterials() {
        return $this->dao->getAllMaterials();
    }

    public function getMaterialById($id) {
        return $this->dao->getMaterialById($id);
    }

    public function createMaterial($data) {
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';

        if (empty($title) || strlen(trim($title)) < 5) {
            throw new Exception("Title harus minimal 5 karakter.");
        }
        if (empty($description)) {
            throw new Exception("Deskripsi tidak boleh kosong.");
        }

        $title = trim(strip_tags($title));
        $description = trim(strip_tags($description));

        $existing = $this->dao->getMaterialByTitle($title);
        if ($existing) {
            throw new Exception("Material dengan judul ini sudah ada.");
        }

        return $this->dao->insertMaterial([
            'title' => $title,
            'description' => $description
        ]);
    }

    public function updateMaterial($id, $data) {
        $title = $data['title'] ?? '';
        $description = $data['description'] ?? '';

        if (empty($title) || strlen(trim($title)) < 5) {
            throw new Exception("Title harus minimal 5 karakter.");
        }
        if (empty($description)) {
            throw new Exception("Deskripsi tidak boleh kosong.");
        }

        $title = trim(strip_tags($title));
        $description = trim(strip_tags($description));

        return $this->dao->updateMaterial($id, [
            'title' => $title,
            'description' => $description
        ]);
    }

    public function deleteMaterial($id) {
        return $this->dao->deleteMaterial($id);
    }
}
