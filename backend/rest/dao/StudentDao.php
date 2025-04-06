<?php
require_once 'BaseDao.php';

class StudentDao extends BaseDao {

    public function __construct() {
        parent::__construct(); // Memanggil konstruktor dari BaseDao
    }

    // Menambahkan data student ke dalam tabel Student
    public function insertStudent($data) {
        // Kamu bisa menambahkan logika khusus untuk Student jika diperlukan
        return $this->insert('student', $data); // Memanggil insert() dari BaseDao
    }

    // Menampilkan semua student
    public function getAllStudents() {
        return $this->findAll('student');
    }

    // Menampilkan student berdasarkan id
    public function getStudentById($id) {
        return $this->findById('student', 'student_id', $id);
    }

    // Update data student berdasarkan id
    public function updateStudent($data, $id) {
        return $this->update('student', $data, 'student_id', $id);
    }

    // Menghapus data student berdasarkan id
    public function deleteStudent($id) {
        return $this->delete('student', 'student_id', $id);
    }
}
