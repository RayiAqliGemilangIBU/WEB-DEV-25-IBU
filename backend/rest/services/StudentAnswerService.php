<?php
require_once __DIR__ . '/../dao/StudentAnswerDao.php';

class StudentAnswerService
{
    private $dao;

    public function __construct()
    {
        $this->dao = new StudentAnswerDao();
    }

    public function getAllAnswers()
    {
        
        return $this->dao->getAllAnswers();
    }

    public function getAnswerById($id)
    {
        return $this->dao->getAnswerById($id);
    }

    public function addStudentAnswer($data) {
        // Validasi minimal: user_id, question_id, selected_option_id harus ada
        if (!isset($data['user_id'], $data['question_id'], $data['selected_option_id'])) {
            throw new Exception("Missing required fields");
        }
        // Bisa tambahkan validasi FK ada di tabel masing-masing (user, question, optionitem) jika perlu

        return $this->dao->insertAnswer($data);
    }

    public function updateAnswer($id, $data)
    {
        return $this->dao->updateAnswer($data, $id);
    }

    public function deleteAnswer($id)
    {
        return $this->dao->deleteAnswer($id);
    }

    public function getCorrectPercentageByUserId($userId)
    {
        return $this->dao->getCorrectAnswerPercentageByUserId($userId);
    }

    public function getAnswersByUserId($userId)
    {
        return $this->dao->getAnswersByUserId($userId);
    }


}
