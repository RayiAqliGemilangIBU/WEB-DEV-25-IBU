<?php
require_once __DIR__ . '/../dao/QuizDao.php';
require_once __DIR__ . '/../dao/MaterialDao.php';

class QuizService {
    private $quizDao;
    private $materialDao;

    public function __construct() {
        $this->quizDao = new QuizDao();
        $this->materialDao = new MaterialDao();
    }

    public function createQuiz($quiz) {
        // Validasi: title tidak boleh kosong
        if (empty($quiz['title'])) {
            throw new Exception("Quiz title cannot be empty");
        }

        // Validasi: material_id harus valid
        $material = $this->materialDao->getMaterialById($quiz['material_id']);
        if (!$material) {
            throw new Exception("Invalid material ID");
        }

        // Logika Bisnis: Cegah duplikat quiz dengan title yang sama dalam satu material
        $existing = $this->quizDao->getQuizByTitleAndMaterialId($quiz['title'], $quiz['material_id']);
        if ($existing) {
            throw new Exception("Quiz with this title already exists for the material");
        }


        // Pengolahan: trim dan sanitasi input
        $quiz['title'] = htmlspecialchars(trim($quiz['title']));

        return $this->quizDao->insertQuiz($quiz);
    }

    public function deleteQuiz($quizId) {
        // Opsional: hapus semua question dan option terkait
        return $this->quizDao->deleteQuizAndDependencies($quizId);
    }
}
