<?php
require_once __DIR__ . '/../dao/TextMaterialDao.php';
require_once __DIR__ . '/../dao/MaterialDao.php';


class TextMaterialService {
    private $dao;
    private $materialDao;
    private $jwtHelper;

    public function __construct() {
        $this->dao = new TextMaterialDao();
        $this->materialDao = new MaterialDao();
        
    }

    public function createTextMaterial($material_id, $data) {

        $materialId = $data['material_id'] ?? null;
        $content = trim(strip_tags($data['content'] ?? ''));
        error_log("Konten setelah strip_tags: " . $content);
        error_log("Panjang konten: " . strlen($content));

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

    public function getTextMaterialById($id) {
        if (empty($id)) {
            throw new Exception("text_id tidak boleh kosong.");
        }

        return $this->dao->getTextMaterialById($id);
    }


    public function getTextMaterialByMaterialId($materialId) {
        return $this->dao->getTextMaterialById($materialId);
    }

    public function updateTextMaterial($text_id, $data) {
        $content = trim(strip_tags($data['content'] ?? ''));
        var_dump($content); // Menampilkan isi konten
        error_log(strlen($content)); // Menulis panjang konten ke log
    
        if (empty($content) || strlen($content) < 100) {
            throw new Exception("Konten harus minimal 100 karakter.");
        }
    
        $existing = $this->dao->getTextMaterialById($text_id); // <--- Ubah sesuai method DAO yang tersedia
        if (!$existing) {
            throw new Exception("Text material tidak ditemukan.");
        }
    
        $data = [
            'content' => $data['content'],
            'title' => $data['title'],
            // tambahkan kolom lain yang sesuai
        ];
    
        return $this->dao->updateTextMaterial($data, $text_id);
    }  


    public function deleteTextMaterialById($textId) {
        return $this->dao->deleteTextMaterial($textId);
    }
}
