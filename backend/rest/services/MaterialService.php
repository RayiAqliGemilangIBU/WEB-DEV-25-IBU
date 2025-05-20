<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/MaterialDao.php';

class MaterialService extends BaseService {
    protected $table = 'material';
    protected $idColumn = 'material_id';

    public function __construct() {
        parent::__construct(new MaterialDao());
    }

    public function createMaterial(array $data) {
        $title = trim(strip_tags($data['title'] ?? ''));
        $description = trim(strip_tags($data['description'] ?? ''));

        if (strlen($title) < 5) {
            throw new Exception("Title harus minimal 5 karakter.");
        }
        if (empty($description)) {
            throw new Exception("Deskripsi tidak boleh kosong.");
        }

        if ($this->dao->getMaterialByTitle($title)) {
            throw new Exception("Material dengan judul ini sudah ada.");
        }

        return $this->dao->insert([
            'title' => $title,
            'description' => $description
        ], $this->table);
    }

    public function updateMaterial($id, $data) {
        $title = trim(strip_tags($data['title'] ?? ''));
        $description = trim(strip_tags($data['description'] ?? ''));

        if (strlen($title) < 5) {
            throw new Exception("Title harus minimal 5 karakter.");
        }
        if (empty($description)) {
            throw new Exception("Deskripsi tidak boleh kosong.");
        }

        // Ganti idField dari 'user_id' menjadi $this->idColumn secara manual
        return $this->dao->update($this->table, [
            'title' => $title,
            'description' => $description
        ], $this->idColumn, $id);
    }

    public function deleteMaterial($id) {
        return $this->dao->delete($this->table, $this->idColumn, $id);
    }

    public function getAllMaterials() {
        return $this->dao->getAllMaterials();
    }

    public function getMaterialById($id) {
        return $this->dao->getMaterialById($id);
    }
}
