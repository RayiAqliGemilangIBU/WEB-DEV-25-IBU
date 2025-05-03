<?php
require_once __DIR__ . '/../dao/OptionItemDao.php';

class OptionItemService {
    private $dao;

    public function __construct() {
        $this->dao = new OptionItemDao();
    }

    public function getAllOptionItems() {
        return $this->dao->getAllOptionItems();
    }

    public function getOptionItemById($id) {
        return $this->dao->getOptionItemById($id);
    }

    public function createOptionItem($data) {
        if (empty($data['question_id']) || empty($data['option_text']) || !isset($data['is_correct'])) {
            throw new Exception("Invalid data: question_id, option_text, and is_correct are required.");
        }
        $id = $this->dao->insertOptionItem($data);
        return ['last_insert_id' => $id];
    }
    

    public function updateOptionItem($id, $data) {
        return $this->dao->updateOptionItem($data, $id);
    }

    public function deleteOptionItem($id) {
        return $this->dao->deleteOptionItem($id);
    }

    public function getOptionsByQuestionId($question_id) {
        return $this->dao->getOptionsByQuestionId($question_id);
    }

    public function checkOptionContent($question_id, $content) {
        return $this->dao->checkOptionContentByQuestionId($question_id, $content);
    }
}
