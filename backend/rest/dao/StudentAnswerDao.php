<?php
require_once 'BaseDao.php';

class StudentAnswerDao extends BaseDao {
    
    protected $table = 'student_answer';
    protected $idField = 'answer_id';

    public function getAnswerById($id) {
        return $this->findById($this->table, $this->idField, $id);
    }

    public function getAllAnswers() {
        return $this->findAll($this->table);
    }

    public function insertAnswer($data) {
        echo "Inserting answer with data: ";
        print_r($data);
        return $this->insert($this->table, $data);

        
    }
    

    public function updateAnswer($data, $id) {
        return $this->update($this->table, $data, $this->idField, $id);
    }

    public function deleteAnswer($id) {
        return $this->delete($this->table, $this->idField, $id);
    }

    public function getAnswersByStudentId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Menambahkan metode berikut
    public function getAnswerByStudentAndQuestion($user_id, $question_id) {
        echo "Checking if student has answered the question...\n";
        echo "User ID: $user_id, Question ID: $question_id\n";
        $stmt = $this->conn->prepare("SELECT * FROM student_answer WHERE user_id = ? AND question_id = ?");
        $stmt->execute([$user_id, $question_id]);
        return $stmt->fetch();
    }

    public function getDetailedAnswersByUser($user_id) {
        // Debugging: tampilkan query dan nilai parameter untuk memastikan semuanya benar
        // echo "Executing query: SELECT sa.*, q.header, oi.option_text, oi.is_correct
        // FROM student_answer sa
        // JOIN question q ON sa.question_id = q.question_id
        // LEFT JOIN optionitem oi ON sa.selected_option_id = oi.option_id
        // WHERE sa.user_id = " . $user_id . "\n";
    
        // // Debugging nilai user_id
        // echo "user_id: ";
        // var_dump($user_id);
    
        // Eksekusi query
        $stmt = $this->conn->prepare("
            SELECT sa.*, q.header, oi.option_text, oi.is_correct
            FROM student_answer sa
            JOIN question q ON sa.question_id = q.question_id
            LEFT JOIN optionitem oi ON sa.selected_option_id = oi.option_id
            WHERE sa.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function calculateCorrectAnswerPercentage($user_id) {
        $answers = $this->getDetailedAnswersByStudent($user_id);
        $correct = 0;
        $total = count($answers);

        foreach ($answers as $a) {
            if ($a['is_correct'] == 1) $correct++;
        }

        return $total > 0 ? ($correct / $total) * 100 : 0;
    }

    public function getAnswersByOptionId($option_id) {
        $stmt = $this->conn->prepare("SELECT * FROM student_answer WHERE selected_option_id = ?");
        $stmt->execute([$option_id]);
        return $stmt->fetchAll();
    }

}
