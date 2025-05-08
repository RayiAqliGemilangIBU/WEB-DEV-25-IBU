<?php

require_once __DIR__ . '/../dao/StudentAnswerDao.php';
require_once __DIR__ . '/../dao/OptionItemDao.php';

class StudentAnswerService {
    private $studentAnswerDao;
    private $optionItemDao;

    public function __construct() {
        $this->studentAnswerDao = new StudentAnswerDao();
        $this->optionItemDao = new OptionItemDao();
    }

    /**
     * Create a new student answer with validation and automatic scoring
     */
    public function createAnswer($data) {
        // Validasi field wajib
        if (!isset($data['user_id'], $data['question_id'], $data['selected_option_id'])) {
            throw new Exception("Missing required fields: user_id, question_id, selected_option_id.");
        }

        // Cek apakah siswa sudah menjawab soal ini
        $existing = $this->studentAnswerDao->getAnswerByStudentAndQuestion(
            $data['user_id'],
            $data['question_id']
        );

        if ($existing) {
            throw new Exception("Student has already answered this question.");
        }

        // Ambil data opsi yang dipilih
        $selectedOption = $this->optionItemDao->getOptionItemById($data['selected_option_id']);
        if (!$selectedOption) {
            throw new Exception("Selected option not found.");
        }
        echo "Selected Option: ";
        print_r($selectedOption);
        // Tambahkan flag benar/salah jika tersedia
        $isCorrect = isset($selectedOption['is_correct']) ? (int)$selectedOption['is_correct'] : 0;
        // Jangan masukkan ke dalam $data karena kolom is_correct tidak ada di tabel
        
        return $this->studentAnswerDao->insertAnswer($data);
    }

    /**
     * Get answer by its ID
     */
    public function getAnswerById($id) {
        return $this->studentAnswerDao->getAnswerById($id);
    }

    /**
     * Get all answers (for admin use)
     */
    public function getAllAnswers() {
        return $this->studentAnswerDao->getAllAnswers();
    }

    /**
     * Get all answers by student
     */
    public function getAnswersByStudentId($user_id) {
        return $this->studentAnswerDao->getAnswersByStudentId($user_id);
    }

    /**
     * Get answer by student and question
     */
    public function getAnswerByStudentAndQuestion($user_id, $question_id) {
        $stmt = $this->conn->prepare("SELECT * FROM student_answer WHERE user_id = ? AND question_id = ?");
        $stmt->execute([$user_id, $question_id]);
        return $stmt->fetch();
    }
    

    /**
     * Update an existing answer (e.g., untuk perbaikan manual oleh admin)
     */
    public function updateAnswer($data, $id) {
        return $this->studentAnswerDao->updateAnswer($data, $id);
    }

    /**
     * Delete answer by ID
     */
    public function deleteAnswer($id) {
        return $this->studentAnswerDao->deleteAnswer($id);
    }

    public function getAnswersByOptionId($option_id) {
        return $this->studentAnswerDao->getAnswersByOptionId($option_id);
    }

    public function calculateCorrectAnswerPercentage($user_id) {
        $answers = $this->studentAnswerDao->getDetailedAnswersByUser($user_id);
        $correct = 0;
        $total = count($answers);
    
        foreach ($answers as $answer) {
            if (isset($answer['is_correct']) && $answer['is_correct'] == 1) {
                $correct++;
            }
        }
    
        return $total > 0 ? ($correct / $total) * 100 : 0;
    }

    public function getDetailedAnswersByUser($user_id) {
        return $this->studentAnswerDao->getDetailedAnswersByUser($user_id);
    }
    
    
}
