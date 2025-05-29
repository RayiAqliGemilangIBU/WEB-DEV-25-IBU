<?php
require_once __DIR__ . '/../dao/BaseDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/MaterialDao.php';
class BaseService {
    protected $dao;
    

    public function __construct($dao) {
        $this->dao = $dao;
    }

    public function getAll() {
        return $this->dao->findAll($this->table);
    }

    public function getById($table, $idField, $id) {
        return $this->dao->findById($table, $idField, $id);
    }

    public function create($data) {
        return $this->dao->insert($data);
    }

    public function update($data, $id) {
        return $this->dao->update($this->table, $data, 'user_id', $id);
    }


    public function delete($id) {
        return $this->dao->delete($this->table, 'user_id', $id);
    }

}
