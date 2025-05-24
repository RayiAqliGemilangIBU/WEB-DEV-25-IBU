<?php
require_once 'BaseDao.php';

class StudentAnswerDao extends BaseDao
{
    protected $table = "student_answer";
    protected $idField = "answer_id";

    public function __construct()
    {
        parent::__construct();
    }

    public function getAnswerById($id)
    {
        return $this->findById($this->table, $this->idField, $id);
    }

    public function getAllAnswers()
    {
        return $this->findAll($this->table);
    }

    public function insertAnswer($data) {
        return $this->insert($this->table, $data);
    }


    public function updateAnswer($data, $id)
    {
        return $this->update($this->table, $data, $this->idField, $id);
    }

    public function deleteAnswer($id)
    {
        return $this->delete($this->table, $this->idField, $id);
    }

    // Fungsi khusus yang tidak tersedia di BaseDao
    public function getCorrectAnswerPercentageByUserId($userId) {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total,
                SUM(CASE WHEN sa.selected_option_id = oi.option_id AND oi.is_correct = 1 THEN 1 ELSE 0 END) AS correct
            FROM student_answer sa
            JOIN optionitem oi ON sa.selected_option_id = oi.option_id
            WHERE sa.user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $total = $result['total'];
        $correct = $result['correct'];

        $percentage = $total > 0 ? ($correct / $total) * 100 : 0;
        return round($percentage, 2);
    }

    public function getAnswersByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM student_answer WHERE user_id = ?");
        $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




}
