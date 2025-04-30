<?php
require_once 'BaseDao.php';

class QuestionDao extends BaseDao {

    public function insertQuestion($data) {
        return $this->insert('question', $data);
    }

    public function updateQuestion($data, $id) {
        return $this->update('question', $data, 'question_id', $id);
    }

    public function getAllQuestions() {
        return $this->findAll('question');
    }

    public function getQuestionById($id) {
        return $this->findById('question', 'question_id', $id);
    }

    public function getQuestionsByQuizId($quiz_id) {
        $stmt = $this->conn->prepare("SELECT * FROM question WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);
        return $stmt->fetchAll();
    }

    public function deleteQuestion($id) {
        return $this->delete('question', 'question_id', $id);
    }
}
