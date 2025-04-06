<?php
require_once 'BaseDao.php';

class StudentDao extends BaseDao {
    public function insert($user_id) {
        return parent::insert('Student', ['user_id' => $user_id]);
    }
}
