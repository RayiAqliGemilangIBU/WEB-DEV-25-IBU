<?php

require_once __DIR__ . '/../dao/QuestionDao.php';

class QuestionService {
    private $questionDao;

    public function __construct() {
        $this->questionDao = new QuestionDao();
    }

    public function createQuiz($data) {
        // Validasi data input
        if (empty($data['title']) || empty($data['material_id'])) {
            throw new Exception("Data tidak valid, title dan material_id diperlukan.");
        }

        // Memasukkan quiz baru ke dalam database
        $quizId = $this->quizDao->insertQuiz($data); // Memanggil metode insertQuiz di QuizDao
        if ($quizId) {
            return ['last_insert_id' => $quizId];
        } else {
            throw new Exception("Quiz creation failed.");
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
