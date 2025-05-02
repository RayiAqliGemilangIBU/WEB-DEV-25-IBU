<?php
require_once __DIR__ . '/../dao/TextMaterialDao.php';
require_once __DIR__ . '/../dao/MaterialDao.php';
require_once __DIR__ . '/../util/JwtHelper.php';

class TextMaterialService {
    private $dao;
    private $materialDao;
    private $jwtHelper;

    public function __construct() {
        $this->dao = new TextMaterialDao();
        $this->materialDao = new MaterialDao();
        $this->jwtHelper = new JwtHelper();
    }

    // CREATE: Menambahkan text material baru
    public function createTextMaterial($jwt, $data) {
        // 🔐 Verifikasi token dan role
        $userData = $this->jwtHelper->validateJwt($jwt);
        if (!$userData || $userData['role'] !== 'admin') {
            throw new Exception("Akses ditolak: hanya admin yang dapat menambahkan text material.");
        }

        $materialId = $data['material_id'] ?? null;
        $content = trim(strip_tags($data['content'] ?? ''));

        if (!$materialId || !$this->materialDao->getMaterialById($materialId)) {
            throw new Exception("Material ID tidak valid.");
        }

        if (empty($content) || strlen($content) < 100) {
            throw new Exception("Konten harus minimal 100 karakter.");
        }

        $existing = $this->dao->getTextMaterialByMaterialId($materialId);
        if ($existing) {
            throw new Exception("Sudah ada text material untuk material ini.");
        }

        $data['content'] = $content;
        return $this->dao->insertTextMaterial($data);
    }

    // READ ALL: Mengambil semua text material
    public function getAllTextMaterials() {
        return $this->dao->getAllTextMaterials();
    }

    // READ BY MATERIAL ID: Mengambil text material berdasarkan material_id
    public function getTextMaterialByMaterialId($materialId) {
        return $this->dao->getTextMaterialById($materialId);
    }

    // UPDATE: Mengubah text material berdasarkan material_id
    public function updateTextMaterial($jwt, $materialId, $data) {
        // 🔐 Verifikasi token dan role
        $userData = $this->jwtHelper->validateJwt($jwt);
        if (!$userData || $userData['role'] !== 'admin') {
            throw new Exception("Akses ditolak: hanya admin yang dapat mengubah text material.");
        }

        $content = trim(strip_tags($data['content'] ?? ''));
        if (empty($content) || strlen($content) < 100) {
            throw new Exception("Konten harus minimal 100 karakter.");
        }

        $existing = $this->dao->getTextMaterialByMaterialId($materialId);
        if (!$existing) {
            throw new Exception("Text material tidak ditemukan untuk material ini.");
        }

        $data['content'] = $content;
        return $this->dao->updateTextMaterial($materialId, $data);
    }

    // DELETE: Menghapus text material berdasarkan material_id
    public function deleteTextMaterial($jwt, $materialId) {
        // 🔐 Verifikasi token dan role
        $userData = $this->jwtHelper->validateJwt($jwt);
        if (!$userData || $userData['role'] !== 'admin') {
            throw new Exception("Akses ditolak: hanya admin yang dapat menghapus text material.");
        }

        $existing = $this->dao->getTextMaterialByMaterialId($materialId);
        if (!$existing) {
            throw new Exception("Text material tidak ditemukan untuk material ini.");
        }

        return $this->dao->deleteTextMaterial($materialId);
    }
}
