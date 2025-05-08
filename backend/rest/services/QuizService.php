<?php
require_once __DIR__ . '/../dao/QuizDao.php';

class QuizService {
    private $quizDao;

    public function __construct() {
        $this->quizDao = new QuizDao();
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
    
    

    public function getAllQuizzes() {
        // Mengambil semua quizzes
        return $this->quizDao->getAllQuizzes();
    }

    public function getQuizById($id) {
        // Mendapatkan quiz berdasarkan ID
        return $this->quizDao->getQuizById($id);
    }

    public function updateQuiz($quizId, $data) {
        // Validasi dan update data quiz
        if (empty($data['title'])) {
            throw new Exception("Quiz title cannot be empty");
        }

        // Mengupdate quiz berdasarkan ID
        return $this->quizDao->updateQuiz($data, $quizId);
    }

    public function deleteQuiz($quizId) {
        // Hapus quiz dan semua dependensinya
        return $this->quizDao->deleteQuizAndDependencies($quizId);
    }
}
