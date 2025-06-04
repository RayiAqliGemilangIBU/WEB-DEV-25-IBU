<?php
// File: backend/rest/services/QuizService.php
require_once __DIR__ . '/../dao/QuizDao.php'; 

class QuizService {
    private $quizDao;

    public function __construct() {
        $this->quizDao = new QuizDao();
    }


    public function createQuiz($data) {
        if (empty($data['title']) || !isset($data['material_id'])) {
            throw new Exception("Invalid data: title and material_id are required.");
        }
        
        $data['description'] = $data['description'] ?? '';

        $quizId = $this->quizDao->createQuiz($data);
        if ($quizId) {
            
            $newQuiz = $this->quizDao->getQuizById($quizId);
            if (!$newQuiz) {
                throw new Exception("Failed to retrieve newly created quiz.");
            }
            return $newQuiz;
        } else {
            throw new Exception("Quiz creation failed at DAO level.");
        }
    }


    public function getAllQuizzes() {
        return $this->quizDao->getAllQuizzes();
    }


    public function getQuizById($quiz_id) {
        $quiz = $this->quizDao->getQuizById($quiz_id);
        if (!$quiz) {
            return false; // atau throw new Exception("Quiz not found with ID: " . $quiz_id);
        }
        return $quiz;
    }
    

    public function getQuizByMaterialId($materialId) {
        if (empty($materialId)) {
            throw new Exception("Material ID cannot be empty.");
        }
        $quizzes = $this->quizDao->getQuizzesByMaterialId($materialId);
        if (!empty($quizzes)) {
            return $quizzes[0]; // Mengembalikan kuis pertama yang ditemukan
        }
        return null; // Tidak ada kuis untuk material ini
    }


    public function updateQuiz($quizId, $data) {
        if (empty($quizId)) {
             throw new Exception("Quiz ID is required for update.");
        }
        // Validasi: title tidak boleh kosong jika disertakan dalam $data
        if (isset($data['title']) && empty($data['title'])) {
            throw new Exception("Quiz title cannot be empty if provided for update.");
        }
        // Hanya field yang relevan (title, description) yang diupdate
        $updateData = [];
        if (isset($data['title'])) $updateData['title'] = $data['title'];
        if (isset($data['description'])) $updateData['description'] = $data['description'];

        if (empty($updateData)) {
            throw new Exception("No data provided for update.");
        }

        $success = $this->quizDao->updateQuiz($quizId, $updateData);
        if ($success) {
            $updatedQuiz = $this->quizDao->getQuizById($quizId);
            if (!$updatedQuiz) {
                 throw new Exception("Failed to retrieve updated quiz.");
            }
            return $updatedQuiz;
        } else {
            throw new Exception("Quiz update failed at DAO level.");
        }
    }


    public function deleteQuiz($quizId) {
        $deleted = $this->quizDao->deleteQuizAndDependencies($quizId);
        if (!$deleted) {
            throw new Exception("Failed to delete quiz and its dependencies.");
        }
        return true;
    }

    public function getQuizWithDetails($quiz_id) {
        $quizDetails = $this->quizDao->getQuizWithQuestionCount($quiz_id);
         if (!$quizDetails) {
            return false; // atau throw new Exception("Quiz details not found with ID: " . $quiz_id);
        }
        return $quizDetails;
    }


    public function getAllQuizzesForAdminDashboard() {
        return $this->quizDao->getAllQuizzesWithQuestionCount();
    }
}