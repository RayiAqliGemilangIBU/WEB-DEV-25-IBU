<?php
// File: backend/rest/services/QuestionService.php
require_once __DIR__ . '/../dao/QuestionDao.php'; // Pastikan path ini benar
require_once __DIR__ . '/../dao/QuizDao.php';   // Untuk validasi quiz_id

class QuestionService {
    private $questionDao;
    private $quizDao;

    public function __construct() {
        $this->questionDao = new QuestionDao();
        $this->quizDao = new QuizDao(); // Inisialisasi QuizDao
    }


    public function addQuestionToQuiz($data) {
        if (!isset($data['quiz_id']) || empty($data['header']) || !isset($data['correct_is_true'])) {
            throw new Exception("Invalid data: quiz_id, header, and correct_is_true are required.");
        }

        // Validasi apakah quiz_id ada
        $quiz = $this->quizDao->getQuizById($data['quiz_id']);
        if (!$quiz) {
            throw new Exception("Quiz with ID " . $data['quiz_id'] . " not found.");
        }

        // Set default explanation jika kosong
        $data['explanation'] = $data['explanation'] ?? '';
        // Pastikan boolean dikelola dengan benar
        $data['correct_is_true'] = filter_var($data['correct_is_true'], FILTER_VALIDATE_BOOLEAN);


        $questionId = $this->questionDao->createQuestion($data);
        if ($questionId) {
            $newQuestion = $this->questionDao->getQuestionById($questionId);
            if (!$newQuestion) {
                throw new Exception("Failed to retrieve newly created question.");
            }
            // Konversi kembali boolean untuk output JSON yang konsisten jika perlu
            $newQuestion['correct_is_true'] = (bool)$newQuestion['correct_is_true'];
            return $newQuestion;
        } else {
            throw new Exception("Question creation failed at DAO level.");
        }
    }

    public function getQuestionsByQuizId($quiz_id) {
        if (empty($quiz_id)) {
            throw new Exception("Quiz ID cannot be empty.");
        }
        $questions = $this->questionDao->getQuestionsByQuizId($quiz_id);
        // Konversi boolean untuk output JSON
        return array_map(function($q) {
            $q['correct_is_true'] = (bool)$q['correct_is_true'];
            return $q;
        }, $questions);
    }

    public function getQuestionById($question_id) {
        $question = $this->questionDao->getQuestionById($question_id);
        if ($question) {
            $question['correct_is_true'] = (bool)$question['correct_is_true'];
        }
        return $question;
    }

    public function updateQuestion($question_id, $data) {
        if (empty($question_id)) {
            throw new Exception("Question ID is required for update.");
        }

        // Validasi apakah pertanyaan ada
        $existingQuestion = $this->questionDao->getQuestionById($question_id);
        if (!$existingQuestion) {
            throw new Exception("Question with ID " . $question_id . " not found.");
        }
        
        // Hanya field yang relevan yang diupdate
        $updateData = [];
        if (isset($data['header'])) $updateData['header'] = $data['header'];
        if (isset($data['explanation'])) $updateData['explanation'] = $data['explanation'];
        if (isset($data['correct_is_true'])) {
            $updateData['correct_is_true'] = filter_var($data['correct_is_true'], FILTER_VALIDATE_BOOLEAN);
        }

        if (empty($updateData)) {
            // Jika tidak ada data yang diupdate, kembalikan data yang ada
            // atau throw new Exception("No data provided for update.");
            $existingQuestion['correct_is_true'] = (bool)$existingQuestion['correct_is_true'];
            return $existingQuestion; 
        }

        $success = $this->questionDao->updateQuestion($question_id, $updateData);
        if ($success) {
            $updatedQuestion = $this->questionDao->getQuestionById($question_id);
            if (!$updatedQuestion) {
                throw new Exception("Failed to retrieve updated question.");
            }
            $updatedQuestion['correct_is_true'] = (bool)$updatedQuestion['correct_is_true'];
            return $updatedQuestion;
        } else {
            throw new Exception("Question update failed at DAO level.");
        }
    }

    public function deleteQuestion($question_id) {
        if (empty($question_id)) {
            throw new Exception("Question ID is required for deletion.");
        }
        // Validasi apakah pertanyaan ada sebelum menghapus
        $existingQuestion = $this->questionDao->getQuestionById($question_id);
        if (!$existingQuestion) {
            throw new Exception("Question with ID " . $question_id . " not found, cannot delete.");
        }

        $deleted = $this->questionDao->deleteQuestion($question_id);
        if (!$deleted) {
            throw new Exception("Failed to delete question.");
        }
        return true;
    }

    public function getAllQuestions() {
        $questions = $this->questionDao->getAllQuestions();
        // Konversi boolean untuk output JSON dan tambahkan informasi kuis jika ada (dari JOIN di DAO)
        return array_map(function($q) {
            $q['correct_is_true'] = (bool)$q['correct_is_true'];
            // Jika DAO mengembalikan quiz_title, itu akan ada di sini
            return $q;
        }, $questions);
    }
}
