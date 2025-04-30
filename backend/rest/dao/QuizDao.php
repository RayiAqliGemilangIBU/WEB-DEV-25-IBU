<?php
require_once 'BaseDao.php';

class QuizDao extends BaseDao {

    public function insertQuiz($data) {
        return $this->insert('quiz', $data);
    }

    public function updateQuiz($data, $id) {
        return $this->update('quiz', $data, 'quiz_id', $id);
    }

    public function getAllQuizzes() {
        return $this->findAll('quiz');
    }

    public function getQuizById($id) {
        return $this->findById('quiz', 'quiz_id', $id);
    }

    public function deleteQuiz($id) {
        return $this->delete('quiz', 'quiz_id', $id);
    }
}
