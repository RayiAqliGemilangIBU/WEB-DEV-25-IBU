<?php
require_once __DIR__ . '/BaseDao.php';

class MaterialDao extends BaseDao {

    private $table = 'material';

    public function __construct() {
        parent::__construct();
    }

    // CREATE
    public function insertMaterial($data) {
        return $this->insert($this->table, $data);
    }

    // READ ALL
    public function getAllMaterials() {
        return $this->findAll($this->table);
    }

    // READ BY ID
    public function getMaterialById($id) {
        return $this->findById($this->table, 'material_id', $id);
    }

    // UPDATE
    public function updateMaterial($data, $id) {
        return $this->update($this->table, $data, 'material_id', $id);
    }

    // DELETE
    public function deleteMaterial($id) {
        return $this->delete($this->table, 'material_id', $id);
    }

    public function getMaterialByTitle($title) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE title = ?");
        $stmt->execute([$title]);
        return $stmt->fetch();
    }
}
?>
