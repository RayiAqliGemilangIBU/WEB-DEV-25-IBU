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

    public function getQuizByTitleAndMaterialId($title, $materialId) {
        $stmt = $this->conn->prepare("SELECT * FROM quiz WHERE title = :title AND material_id = :material_id");
        $stmt->execute(['title' => $title, 'material_id' => $materialId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function deactivateAllQuizzesForMaterial($materialId) {
        $stmt = $this->conn->prepare("UPDATE quiz SET is_active = 0 WHERE material_id = :material_id");
        $stmt->execute(['material_id' => $materialId]);
    }
    
    public function deleteQuizAndDependencies($quizId) {
        // Hapus optionitem
        $this->conn->prepare("DELETE FROM optionitem WHERE question_id IN (SELECT question_id FROM question WHERE quiz_id = :quiz_id)")
                  ->execute(['quiz_id' => $quizId]);
        // Hapus question
        $this->conn->prepare("DELETE FROM question WHERE quiz_id = :quiz_id")
                  ->execute(['quiz_id' => $quizId]);
        // Hapus quiz
        return $this->delete("quiz", "quiz_id", $quizId);
    }
    
}
