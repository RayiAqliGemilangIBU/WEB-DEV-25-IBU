<?php
require_once 'BaseDao.php';

class OptionItemDao extends BaseDao {

    public function insertOptionItem($data) {
        return $this->insert('optionitem', $data);
    }

    public function updateOptionItem($data, $id) {
        return $this->update('optionitem', $data, 'option_id', $id);
    }

    public function getAllOptionItems() {
        return $this->findAll('optionitem');
    }

    public function getOptionItemById($id) {
        return $this->findById('optionitem', 'option_id', $id);
    }

    public function deleteOptionItem($id) {
        return $this->delete('optionitem', 'option_id', $id);
    }
    
    public function getOptionsByQuestionId($question_id) {
        $stmt = $this->conn->prepare("SELECT * FROM option_item WHERE question_id = ?");
        $stmt->execute([$question_id]);
        return $stmt->fetchAll();
    }

}
