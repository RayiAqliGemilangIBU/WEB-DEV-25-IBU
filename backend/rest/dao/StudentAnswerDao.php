<?php
require_once 'BaseDao.php';

class StudentAnswerDao extends BaseDao {
    
    protected $table = 'student_answer';
    protected $idField = 'answer_id';

    public function getStudentAnswerById($id) {
        return $this->findById($this->table, $this->idField, $id);
    }

    public function getAllAnswers() {
        return $this->findAll($this->table);
    }

    public function insertStudentAnswer($data) {
        return $this->insert($this->table, $data);
    }

    public function updateStudentAnswer($data, $id) {
        return $this->update($this->table, $data, $this->idField, $id);
    }

    public function deleteStudentAnswer($id) {
        return $this->delete($this->table, $this->idField, $id);
    }

    // Tambahan: ambil jawaban berdasarkan user
    public function getAnswersByUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Tambahan: ambil jawaban + opsi + soal untuk analisis
    public function getDetailedAnswersByUser($user_id) {
        $stmt = $this->conn->prepare("
            SELECT sa.*, q.header, oi.option_text, oi.is_correct
            FROM student_answer sa
            JOIN question q ON sa.question_id = q.question_id
            LEFT JOIN optionitem oi ON sa.selected_option_id = oi.option_id
            WHERE sa.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Fungsi untuk menghitung persentase jawaban benar peserta
    public function calculateCorrectAnswerPercentage($user_id) {
        // Ambil semua jawaban yang diberikan oleh user
        $answers = $this->getDetailedAnswersByUser($user_id);
        
        // Inisialisasi variabel untuk menghitung jawaban benar dan total jawaban
        $correctAnswers = 0;
        $totalAnswers = count($answers);

        // Loop melalui jawaban dan hitung yang benar
        foreach ($answers as $answer) {
            if ($answer['is_correct'] == 1) { // Asumsikan is_correct = 1 berarti benar
                $correctAnswers++;
            }
        }

        // Jika total jawaban adalah 0, kembalikan persentase 0
        if ($totalAnswers == 0) {
            return 0;
        }

        // Hitung persentase jawaban benar
        $percentage = ($correctAnswers / $totalAnswers) * 100;
        return $percentage;
    }

    
}
