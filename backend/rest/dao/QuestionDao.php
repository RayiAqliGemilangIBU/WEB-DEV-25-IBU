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

    // Method untuk cek duplikasi pertanyaan berdasarkan header dan quiz_id
    public function getQuestionByHeaderAndQuizId($header, $quizId) {
        $sql = "SELECT * FROM question WHERE header = :header AND quiz_id = :quizId";
        $stmt = $this->conn->prepare($sql);
        // Menggunakan bindValue() atau bindParam() untuk PDO
        $stmt->bindValue(':header', $header, PDO::PARAM_STR); 
        $stmt->bindValue(':quizId', $quizId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Method untuk menghitung jumlah pertanyaan berdasarkan quiz_id
    public function getCountByQuizId($quizId) {
        $sql = "SELECT COUNT(*) AS count FROM question WHERE quiz_id = :quizId";
        $stmt = $this->conn->prepare($sql);
        // Menggunakan bindValue() atau bindParam() untuk PDO
        $stmt->bindValue(':quizId', $quizId, PDO::PARAM_INT); 
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }


}
