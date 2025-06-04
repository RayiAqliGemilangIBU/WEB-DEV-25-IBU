<?php
// File: backend/rest/dao/QuestionDao.php
require_once __DIR__ . '/BaseDao.php'; // Pastikan path ini benar

class QuestionDao extends BaseDao {

    public function __construct() {
        parent::__construct('question'); // Set tabel default untuk BaseDao
    }


    public function createQuestion($data) {
        // Pastikan field boolean dikonversi dengan benar jika perlu
        if (isset($data['answer'])) {
            $data['answer'] = filter_var($data['answer'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }
        return $this->insert('question', $data);
    }


    public function updateQuestion($question_id, $data) {
        // Pastikan field boolean dikonversi dengan benar jika perlu
        if (isset($data['answer'])) {
            $data['answer'] = filter_var($data['answer'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }
        return $this->update('question', $data, 'question_id', $question_id);
    }

    public function getQuestionsByQuizId($quiz_id) {
        $stmt = $this->conn->prepare("SELECT * FROM question WHERE quiz_id = :quiz_id ORDER BY question_id ASC");
        $stmt->execute(['quiz_id' => $quiz_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getQuestionById($question_id) {
        return $this->findById('question', 'question_id', $question_id);
    }

    public function deleteQuestion($question_id) {
        // Jika ada tabel student_answer yang memiliki foreign key ke question_id
        // dan tidak ada ON DELETE CASCADE, Anda mungkin perlu menghapus jawaban siswa dulu.
        // Namun, berdasarkan skema ChemLP1.3 kita, student_answer.question_id memiliki ON DELETE CASCADE.
        return $this->delete('question', 'question_id', $question_id);
    }

    // --- Fungsi Baru yang Mungkin Berguna ---


    public function countQuestionsInQuiz($quiz_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM question WHERE quiz_id = :quiz_id");
        $stmt->execute(['quiz_id' => $quiz_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['count'] : 0;
    }

    
    public function getAllQuestions() {
        // Menggunakan metode findAll dari BaseDao
        return $this->findAll('question'); 
  
    }
}