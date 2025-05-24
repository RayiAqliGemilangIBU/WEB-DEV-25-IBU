<?php
require_once 'BaseDao.php';

class QuizDao extends BaseDao {

    public function insertQuiz($data) {
        // Menyusun data untuk tabel quizzes
        return $this->insert('quiz', $data); // Memanggil metode insert di BaseDao
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

    public function getQuizByMaterialId($materialId) {
        $stmt = $this->conn->prepare("SELECT * FROM quiz WHERE material_id = ?");
        $stmt->execute([$materialId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
