<?php
require_once __DIR__ . '/../dao/BaseDao.php';
class BaseService {
    protected $dao;
    protected $table = 'user';

    public function __construct($dao) {
        $this->dao = $dao;
    }

    public function getAll() {
        return $this->dao->findAll($this->table);
    }

    public function getById($id) {
        return $this->dao->findById($id);
    }

    public function create($data) {
        return $this->dao->insert($data);
    }

    public function update($data, $id) {
        return $this->dao->update($data, $id);
    }

    public function delete($id) {
        return $this->dao->delete($id);
    }
}
