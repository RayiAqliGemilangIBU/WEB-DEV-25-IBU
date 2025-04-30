<?php
require_once __DIR__ . '/../dao/TextMaterialDao.php';
require_once __DIR__ . '/../dao/MaterialDao.php';

class TextMaterialService {
    private $dao;
    private $materialDao;

    public function __construct() {
        $this->dao = new TextMaterialDao();
        $this->materialDao = new MaterialDao();
    }

    public function createTextMaterial($data) {
        $materialId = $data['material_id'] ?? null;
        $content = trim(strip_tags($data['content'] ?? ''));

        if (!$materialId || !$this->materialDao->getMaterialById($materialId)) {
            throw new Exception("Material ID tidak valid.");
        }

        if (empty($content) || strlen($content) < 100) {
            throw new Exception("Konten harus minimal 100 karakter.");
        }

        // Cek apakah sudah ada textmaterial untuk material_id ini
        $existing = $this->dao->getTextMaterialByMaterialId($materialId);
        if ($existing) {
            throw new Exception("Sudah ada textmaterial untuk material ini.");
        }

        $data['content'] = $content;
        return $this->dao->insertTextMaterial($data);
    }
}
