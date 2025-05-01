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
            throw new Exception("Sudah ada textmaterial untuk material ini.");
        }

        $data['content'] = $content;
        return $this->dao->insertTextMaterial($data);
    }
}
