<?php
require_once 'BaseDao.php';

class UserDao extends BaseDao {
    public function insert($data) {
        return parent::insert('User', $data);
    }
}
