<?php
require_once 'BaseDao.php';

class OptionItemDao extends BaseDao {

    public function insertOptionItem($data) {
        // Menggunakan 'option_text' bukan 'content'
        return $this->insert('optionitem', [
            'question_id' => $data['question_id'],
            'option_text' => $data['option_text'],  // Ganti 'content' dengan 'option_text'
            'is_correct'  => $data['is_correct'],
            'image_path'  => $data['image_path'] ?? null  // Menangani image_path yang opsional
        ]);
    }

    public function updateOptionItem($data, $id) {
        return $this->update('optionitem', [
            'question_id' => $data['question_id'],
            'option_text' => $data['option_text'],
            'is_correct'  => $data['is_correct'],
            'image_path'  => $data['image_path'] ?? null
        ], 'option_id', $id);
    }

    public function deleteOptionItem($id) {
        return $this->delete('optionitem', 'option_id', $id);
    }

    public function getAllOptionItems() {
        return $this->findAll('optionitem');
    }

    public function getOptionItemById($id) {
        return $this->findById('optionitem', 'option_id', $id);
    }

    public function getOptionsByQuestionId($question_id) {
        $stmt = $this->conn->prepare("SELECT * FROM optionitem WHERE question_id = ?");
        $stmt->execute([$question_id]);
        return $stmt->fetchAll();
    }

    public function checkOptionContentByQuestionId($question_id, $content) {
        $stmt = $this->conn->prepare("SELECT * FROM optionitem WHERE question_id = ? AND content = ?");
        $stmt->execute([$question_id, $content]);
        return $stmt->fetch();
    }
}
