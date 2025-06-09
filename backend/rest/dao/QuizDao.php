<?php
// File: backend/rest/dao/QuizDao.php
require_once __DIR__ . '/BaseDao.php'; // Pastikan path ini benar

class QuizDao extends BaseDao {

    public function __construct() {
        parent::__construct('quiz'); // Set tabel default untuk BaseDao jika digunakan
    }

    public function createQuiz($data) {
        return $this->insert('quiz', $data);
    }

    public function updateQuiz($quiz_id, $data) {
        return $this->update('quiz', $data, 'quiz_id', $quiz_id);
    }

    public function getAllQuizzes() {
        return $this->findAll('quiz');
    }


    public function getQuizById($quiz_id) {
        return $this->findById('quiz', 'quiz_id', $quiz_id);
    }


    public function getQuizzesByMaterialId($materialId) {
        // Menggunakan metode query dari BaseDao jika ada, atau implementasi langsung
        $stmt = $this->conn->prepare("SELECT * FROM quiz WHERE material_id = :material_id");
        $stmt->execute(['material_id' => $materialId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function deleteQuizAndDependencies($quizId) {
        try {
            $this->conn->beginTransaction();

            $stmtQuestions = $this->conn->prepare("DELETE FROM question WHERE quiz_id = :quiz_id");
            $stmtQuestions->execute(['quiz_id' => $quizId]);

            $this->delete('quiz', 'quiz_id', $quizId);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            // Log error $e->getMessage()
            return false;
        }
    }


    public function getQuizWithQuestionCount($quiz_id) {
        $query = "SELECT q.*, COUNT(qu.question_id) as question_count
                  FROM quiz q
                  LEFT JOIN question qu ON q.quiz_id = qu.quiz_id
                  WHERE q.quiz_id = :quiz_id
                  GROUP BY q.quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute(['quiz_id' => $quiz_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getAllQuizzesWithQuestionCount() {
        $query = "SELECT q.*, COUNT(qu.question_id) as question_count
                  FROM quiz q
                  LEFT JOIN question qu ON q.quiz_id = qu.quiz_id
                  GROUP BY q.quiz_id
                  ORDER BY q.title ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}