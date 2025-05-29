<?php
require_once __DIR__ . '/BaseDao.php';

class MaterialDao extends BaseDao {

    protected $table = 'material';

    public function __construct() {
         $this->table = 'material';
        parent::__construct();
    }

    public function getTable() {
        return $this->table;
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
    public function updateMaterial($id, $data) {
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
