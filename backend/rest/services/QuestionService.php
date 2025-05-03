<?php

require_once __DIR__ . '/../dao/QuestionDao.php';

class QuestionService {
    private $questionDao;

    public function __construct() {
        $this->questionDao = new QuestionDao();
    }

    public function createQuestion($data) {
        // Validasi data input
        if (empty($data['quiz_id']) || empty($data['header']) || empty($data['explanation'])) {
            throw new Exception("Data tidak valid. 'quiz_id', 'header', dan 'explanation' diperlukan.");
        }
    
        // Memasukkan question baru ke database via DAO
        $questionId = $this->questionDao->insertQuestion($data); // Panggil DAO
    
        if ($questionId) {
            return ['last_insert_id' => $questionId];
        } else {
            throw new Exception("Question creation failed.");
        }
    }

    // Update pertanyaan
    public function updateQuestion($question, $id) {
        // Validasi dan update pertanyaan
        if (empty($question['header']) || empty($question['explanation'])) {
            throw new Exception("Header and explanation cannot be empty.");
        }

        return $this->questionDao->updateQuestion($question, $id);
    }

    // Hapus pertanyaan
    public function deleteQuestion($id) {
        return $this->questionDao->deleteQuestion($id);
    }

    // Ambil semua pertanyaan
    public function getAllQuestions() {
        return $this->questionDao->getAllQuestions();
    }

    // Ambil pertanyaan berdasarkan ID
    public function getQuestionById($id) {
        return $this->questionDao->getQuestionById($id);
    }
}
