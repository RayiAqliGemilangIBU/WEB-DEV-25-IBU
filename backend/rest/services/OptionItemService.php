<?php
require_once __DIR__ . '/../dao/OptionItemDao.php';

class OptionItemService {
    private $dao;


    public function __construct() {
        $this->dao = new OptionItemDao();
    }

    public function getAllOptions() {
        return $this->dao->getAllOptions();
    }

    public function getOptionsByQuestionId($questionId) {
        return $this->dao->getOptionsByQuestionId($questionId);
    }

    public function getOptionById($id) {
        return $this->dao->getOptionById($id);
    }

    public function createOptionItem($data) {
        // Optional: periksa apakah option dengan text sama sudah ada di pertanyaan yang sama
        return $this->dao->insertOption($data);
    }

    public function updateOption($id, $data) {
        return $this->dao->updateOption($data, $id); 
    }

    public function deleteOptionItem($id) {
        return $this->dao->deleteOption($id);
    }

    public function createOption($data) {
        return $this->dao->insertOptionItem($data);
    }
    
}
