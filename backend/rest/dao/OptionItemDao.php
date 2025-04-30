<?php
require_once 'BaseDao.php';

class OptionItemDao extends BaseDao {

    public function insertOptionItem($data) {
        return $this->insert('optionitem', $data);
    }

    public function updateOptionItem($data, $id) {
        return $this->update('optionitem', $data, 'option_id', $id);
    }

    // Ditambahkan: updateOption untuk dipanggil di Service
    public function updateOption($data, $id) {
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

    public function deleteOption($id) {
        return $this->delete('optionitem', 'option_id', $id);
    }

    public function getOptionsByQuestionId($question_id) {
        // Koreksi nama tabel agar sesuai (konsisten dengan insert/update)
        $stmt = $this->conn->prepare("SELECT * FROM optionitem WHERE question_id = ?");
        $stmt->execute([$question_id]);
        return $stmt->fetchAll();
    }

    // Opsional: Cek apakah sudah ada option dengan konten sama untuk satu soal
    public function getOptionByContentAndQuestionId($content, $questionId) {
        $stmt = $this->conn->prepare("SELECT * FROM optionitem WHERE content = ? AND question_id = ?");
        $stmt->execute([$content, $questionId]);
        return $stmt->fetch();
    }
}
