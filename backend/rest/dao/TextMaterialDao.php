<?php
require_once 'BaseDao.php';

class TextMaterialDao extends BaseDao {
    public function __construct() {
        parent::__construct();
    }

    public function insertTextMaterial($textMaterial) {
        return $this->insert("textmaterial", $textMaterial);
    }

    public function getAllTextMaterials() {
        return $this->findAll("textmaterial");
    }

    public function getTextMaterialById($id) {
        return $this->findById("textmaterial", "text_id", $id);
    }

    public function updateTextMaterial($textMaterial, $id) {
        return $this->update("textmaterial", $textMaterial, "text_id", $id);
    }

    public function deleteTextMaterial($id) {
        return $this->delete("textmaterial", "text_id", $id);
    }

    public function getTextMaterialByMaterialId($materialId) {
        $stmt = $this->conn->prepare("SELECT * FROM textmaterial WHERE material_id = ?");
        $stmt->execute([$materialId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
