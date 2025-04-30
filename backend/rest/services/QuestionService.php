<?php

require_once __DIR__ . '/../dao/QuestionDao.php';

class QuestionService {
    private $questionDao;

    public function __construct() {
        $this->questionDao = new QuestionDao();
    }

    // Validasi dan penambahan pertanyaan
    public function createQuestion($question) {
        // Validasi data
        if (empty($question['header']) || empty($question['explanation'])) {
            throw new Exception("Header and explanation cannot be empty.");
        }

        if (strlen($question['header']) < 10) {
            throw new Exception("Header must be at least 10 characters long.");
        }

        // Cek duplikasi pertanyaan dalam satu kuis
        $existingQuestion = $this->questionDao->getQuestionByHeaderAndQuizId($question['header'], $question['quiz_id']);
        if ($existingQuestion) {
            throw new Exception("Duplicate question found in the quiz.");
        }

        // Validasi jumlah pertanyaan per kuis
        $questionCount = $this->questionDao->getCountByQuizId($question['quiz_id']);
        if ($questionCount >= 10) {
            throw new Exception("You can only have a maximum of 10 questions per quiz.");
        }

        // Insert pertanyaan baru
        return $this->questionDao->insertQuestion($question);
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
