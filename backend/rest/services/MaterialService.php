<?php
// File: backend/rest/services/MaterialService.php

require_once __DIR__ . '/../config/config.php'; // Untuk Database::connect()
require_once __DIR__ . '/../dao/MaterialDao.php';
require_once __DIR__ . '/../dao/TextMaterialDao.php';
require_once __DIR__ . '/../dao/QuizDao.php';
require_once __DIR__ . '/../dao/QuestionDao.php';
// Jika Anda menggunakan BaseService dan MaterialService meng-extend-nya:
// require_once __DIR__ . '/BaseService.php';

class MaterialService /* extends BaseService */ { // Sesuaikan jika extends BaseService
    
    // Properti jika extends BaseService dan BaseService menggunakannya
    // protected $table = 'material';
    // protected $idColumn = 'material_id';
    // protected $dao; // Ini akan menjadi instance MaterialDao jika parent::__construct dipanggil

    // Instance DAO spesifik
    private $materialDao;
    private $textMaterialDao;
    private $quizDao;
    private $questionDao;
    private $pdo; // Untuk manajemen transaksi di createMaterialFromUploadedFile

    public function __construct() {
        // Jika Anda meng-extend BaseService dan BaseService mengharapkan DAO di konstruktor:
        // parent::__construct(new MaterialDao()); 
        // $this->materialDao = $this->dao; 

        // Jika tidak, atau jika Anda ingin kontrol penuh:
        $this->materialDao = new MaterialDao();
        
        // Inisialisasi DAO lain yang dibutuhkan oleh createMaterialFromUploadedFile
        $this->textMaterialDao = new TextMaterialDao();
        $this->quizDao = new QuizDao();
        $this->questionDao = new QuestionDao();

        // $this->pdo akan diinisialisasi dalam metode yang memerlukan transaksi
    }

    /**
     * Membuat material baru (bukan dari file upload).
     * @param array $data Data material (harus mengandung title, description, created_by_user_id).
     * @return array Objek material yang baru dibuat.
     * @throws InvalidArgumentException Jika validasi gagal.
     * @throws Exception Jika pembuatan gagal.
     */
    public function createMaterial(array $data) {
        $title = trim(strip_tags($data['title'] ?? ''));
        $description = trim(strip_tags($data['description'] ?? ''));
        $createdByUserId = $data['created_by_user_id'] ?? null;

        if (strlen($title) < 1) { // Validasi minimal
            throw new InvalidArgumentException("Material title is required.");
        }
        if ($createdByUserId === null) {
            throw new InvalidArgumentException("Creator user ID is required.");
        }
        // Anda bisa menambahkan validasi deskripsi jika perlu

        // Cek duplikasi judul jika MaterialDao memiliki metode getMaterialByTitle
        // if (method_exists($this->materialDao, 'getMaterialByTitle') && $this->materialDao->getMaterialByTitle($title)) {
        //     throw new Exception("Material with this title already exists.");
        // }

        $insertData = [
            'title' => $title,
            'description' => $description,
            'created_by_user_id' => $createdByUserId
        ];
        
        $newMaterialId = $this->materialDao->insert('material', $insertData);
        if(!$newMaterialId) {
            throw new Exception("Failed to create material record.");
        }
        $newMaterial = $this->materialDao->findById('material', 'material_id', $newMaterialId);
        if (!$newMaterial) {
            throw new Exception("Failed to retrieve newly created material.");
        }
        return $newMaterial;
    }

    /**
     * Memperbarui material yang sudah ada.
     * @param int $id ID material yang akan diupdate.
     * @param array $data Data untuk update (title, description).
     * @return array Objek material yang telah diupdate.
     * @throws InvalidArgumentException Jika validasi gagal.
     * @throws Exception Jika material tidak ditemukan atau update gagal.
     */
    public function updateMaterial($id, $data) {
        if (empty($id)) {
            throw new InvalidArgumentException("Material ID is required for update.");
        }
        $title = trim(strip_tags($data['title'] ?? ''));
        $description = trim(strip_tags($data['description'] ?? ''));

        if (strlen($title) < 1) { // Validasi minimal
            throw new InvalidArgumentException("Material title cannot be empty for update.");
        }
        // Anda bisa menambahkan validasi deskripsi jika perlu

        $materialToUpdate = $this->materialDao->findById('material', 'material_id', $id);
        if (!$materialToUpdate) {
            throw new Exception("Material with ID {$id} not found.");
        }
        
        $updateData = [
            'title' => $title,
            'description' => $description
        ];
        
        $success = $this->materialDao->update('material', $updateData, 'material_id', $id);
        if (!$success) {
            // Bisa jadi karena tidak ada baris yang terpengaruh (ID tidak ada atau data sama)
            // atau error. Perlu penanganan lebih baik jika BaseDao->update tidak melempar exception.
            // Untuk saat ini, kita anggap jika false adalah error atau tidak ada perubahan.
            // Jika tidak ada perubahan, mengembalikan data lama bisa jadi pilihan.
            // throw new Exception("Failed to update material or no changes were made.");
        }
        $updatedMaterial = $this->materialDao->findById('material', 'material_id', $id);
        if (!$updatedMaterial) {
             throw new Exception("Failed to retrieve material after update attempt.");
        }
        return $updatedMaterial;
    }

    /**
     * Menghapus material.
     * @param int $id ID material yang akan dihapus.
     * @return bool True jika berhasil.
     * @throws Exception Jika material tidak ditemukan atau penghapusan gagal.
     */
    public function deleteMaterial($id) {
        if (empty($id)) {
            throw new InvalidArgumentException("Material ID is required for deletion.");
        }
        $materialToDelete = $this->materialDao->findById('material', 'material_id', $id);
        if (!$materialToDelete) {
            throw new Exception("Material with ID {$id} not found, cannot delete.");
        }

        // Jika Anda tidak menggunakan ON DELETE CASCADE untuk tabel dependen (quiz, textmaterial),
        // Anda perlu menghapus dependensi tersebut di sini, idealnya dalam transaksi.
        // Contoh (jika tidak ada ON DELETE CASCADE dan QuizService menangani dependensi pertanyaan):
        // $quizDao = new QuizDao();
        // $quizzes = $quizDao->getQuizzesByMaterialId($id);
        // foreach ($quizzes as $quiz) {
        //     (new QuizService())->deleteQuiz($quiz['quiz_id']); // Ini akan menghapus kuis dan pertanyaannya
        // }
        // (new TextMaterialDao())->deleteByMaterialId($id); // Jika TextMaterialDao punya metode ini

        $deleted = $this->materialDao->delete('material', 'material_id', $id);
        if (!$deleted) {
            throw new Exception("Failed to delete material with ID {$id}.");
        }
        return true;
    }

    public function getAllMaterials() {
        // Menggunakan metode yang ada di MaterialDao Anda atau fallback ke BaseDao
        if (method_exists($this->materialDao, 'getAllMaterials')) {
             return $this->materialDao->getAllMaterials();
        }
        return $this->materialDao->findAll('material');
    }

    public function getMaterialById($id) {
        if (empty($id)) {
            throw new InvalidArgumentException("Material ID cannot be empty.");
        }
        $material = $this->materialDao->findById('material', 'material_id', $id);
        if (!$material) {
            return false; // Atau throw exception
        }
        return $material;
    }

    /**
     * Membuat material, materi teks, kuis, dan pertanyaan dari file CSV yang diunggah.
     */
    public function createMaterialFromUploadedFile($fileData, $createdByUserId) {
        $filePath = $fileData['tmp_name'];

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException("File not found or not readable at temporary path.");
        }

        $this->pdo = Database::connect(); // Dapatkan koneksi untuk transaksi
        if (!$this->pdo) {
            throw new Exception("Failed to establish database connection for transaction.");
        }

        $materialInfo = null;
        $quizInfo = null;
        $questionsData = [];

        $fileHandle = fopen($filePath, 'r');
        if ($fileHandle === false) {
            throw new InvalidArgumentException("Could not open the uploaded CSV file.");
        }

        $headerMap = []; 
        $isHeaderRow = true;
        $lineNumber = 0;

        while (($row = fgetcsv($fileHandle)) !== false) {
            $lineNumber++;
            if (empty($row) || (isset($row[0]) && empty(trim($row[0])))) { 
                continue;
            }
            if (isset($row[0]) && strpos(trim($row[0]), '#') === 0) { 
                continue;
            }

            if ($isHeaderRow) {
                foreach ($row as $index => $headerName) {
                    $headerMap[strtoupper(trim($headerName))] = $index;
                }
                $isHeaderRow = false;
                if (!isset($headerMap['ROW_TYPE'])) { 
                    fclose($fileHandle);
                    throw new InvalidArgumentException("CSV file is missing 'ROW_TYPE' header (Line {$lineNumber}).");
                }
                continue;
            }

            $getRowValue = function($columnName, $isOptional = false, $default = null) use ($row, $headerMap, $lineNumber) {
                $columnNameUpper = strtoupper(trim($columnName));
                if (isset($headerMap[$columnNameUpper]) && isset($row[$headerMap[$columnNameUpper]])) {
                    return trim($row[$headerMap[$columnNameUpper]]);
                }
                if (!$isOptional) {
                     throw new InvalidArgumentException("Missing required column '{$columnName}' in CSV data (Line {$lineNumber}).");
                }
                return $default;
            };
            
            $rowType = strtoupper($getRowValue('ROW_TYPE')); 

            switch ($rowType) {
                case 'MATERIAL_INFO':
                    if ($materialInfo !== null) throw new InvalidArgumentException("Duplicate MATERIAL_INFO row (Line {$lineNumber}).");
                    $materialInfo = [
                        'title' => $getRowValue('MATERIAL_TITLE'),
                        'description' => $getRowValue('MATERIAL_DESCRIPTION', true, ''), 
                        'text_content' => $getRowValue('TEXT_MATERIAL_CONTENT', true, ''), 
                    ];
                    if (empty($materialInfo['title'])) throw new InvalidArgumentException("MATERIAL_TITLE is missing (Line {$lineNumber}).");
                    break;
                case 'QUIZ_INFO':
                    if ($quizInfo !== null) throw new InvalidArgumentException("Duplicate QUIZ_INFO row (Line {$lineNumber}).");
                    $quizInfo = [
                        'title' => $getRowValue('QUIZ_TITLE'),
                        'description' => $getRowValue('QUIZ_DESCRIPTION', true, '')
                    ];
                    if (empty($quizInfo['title'])) throw new InvalidArgumentException("QUIZ_TITLE is missing (Line {$lineNumber}).");
                    break;
                case 'QUESTION':
                    $questionHeader = $getRowValue('QUESTION_HEADER');
                    $questionAnswerStr = strtoupper($getRowValue('QUESTION_ANSWER')); // Nama kolom di CSV
                    
                    if (empty($questionHeader) || !in_array($questionAnswerStr, ['TRUE', 'FALSE'])) {
                         throw new InvalidArgumentException("QUESTION_HEADER missing or QUESTION_ANSWER not TRUE/FALSE (Line {$lineNumber}). Value: '{$questionAnswerStr}'");
                    }
                    $questionsData[] = [
                        'header' => $questionHeader,
                        'answer' => ($questionAnswerStr === 'TRUE'), // Konversi ke boolean untuk DB
                        'explanation' => $getRowValue('QUESTION_EXPLANATION', true, '')
                    ];
                    break;
                default:
                    if (!empty($rowType)) { 
                        error_log("Unknown ROW_TYPE '{$rowType}' encountered in CSV (Line {$lineNumber}). Skipping.");
                    }
                    break;
            }
        }
        fclose($fileHandle);

        if ($materialInfo === null) throw new InvalidArgumentException("Missing MATERIAL_INFO row in CSV.");
        if ($quizInfo === null) throw new InvalidArgumentException("Missing QUIZ_INFO row in CSV.");

        try {
            $this->pdo->beginTransaction();

            $materialDbData = [
                'title' => $materialInfo['title'],
                'description' => $materialInfo['description'],
                'created_by_user_id' => $createdByUserId
            ];
            $newMaterialId = $this->materialDao->insert('material', $materialDbData);
            if (!$newMaterialId) throw new Exception("Failed to create material in database.");

            if (isset($materialInfo['text_content']) && !empty($materialInfo['text_content'])) {
                $textMaterialData = [
                    'material_id' => $newMaterialId,
                    'title' => $materialInfo['title'], 
                    'content' => $materialInfo['text_content']
                ];
                $newTextMaterialId = $this->textMaterialDao->insert('textmaterial', $textMaterialData); 
                if (!$newTextMaterialId) throw new Exception("Failed to create text material.");
            }

            $quizDbData = [
                'material_id' => $newMaterialId,
                'title' => $quizInfo['title'],
                'description' => $quizInfo['description']
            ];
            $newQuizId = $this->quizDao->insert('quiz', $quizDbData);
            if (!$newQuizId) throw new Exception("Failed to create quiz.");

            foreach ($questionsData as $qData) {
                $questionDbData = [
                    'quiz_id' => $newQuizId,
                    'header' => $qData['header'],
                    'explanation' => $qData['explanation'],
                    'answer' => $qData['answer'] // Nama field ini harus sesuai dengan kolom DB Anda
                ];
                $newQuestionId = $this->questionDao->insert('question', $questionDbData);
                if (!$newQuestionId) throw new Exception("Failed to create question for quiz ID {$newQuizId}.");
            }

            $this->pdo->commit();
            return ['material_id' => $newMaterialId, 'quiz_id' => $newQuizId];

        } catch (Exception $e) {
            if ($this->pdo && $this->pdo->inTransaction()) { 
                $this->pdo->rollBack();
            }
            error_log("CreateMaterialFromFile DB Error: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
            throw new Exception("Database operation failed during material creation from file. Details: " . $e->getMessage()); 
        }
    }
}
