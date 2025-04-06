<?php
require_once 'BaseDao.php';

class AdminDao extends BaseDao {
    public function insert($user_id) {
        return parent::insert('Admin', ['user_id' => $user_id]);
    }
}
