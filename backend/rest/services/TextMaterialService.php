<?php
require_once __DIR__ . '/../dao/TextMaterialDao.php';
require_once __DIR__ . '/../dao/MaterialDao.php';


class TextMaterialService {
    private $dao;
    private $materialDao;
    private $jwtHelper;

    public function __construct() {
        $this->dao = new TextMaterialDao();
        $this->materialDao = new MaterialDao();
        
    }

    public function createTextMaterial($material_id, $data) {

        $materialId = $data['material_id'] ?? null;
        $content = trim(strip_tags($data['content'] ?? ''));
        error_log("Konten setelah strip_tags: " . $content);
        error_log("Panjang konten: " . strlen($content));

        if (!$materialId || !$this->materialDao->getMaterialById($materialId)) {
            throw new Exception("Material ID tidak valid.");
        }

        if (empty($content) || strlen($content) < 100) {
            throw new Exception("Konten harus minimal 100 karakter.");
        }

        $existing = $this->dao->getTextMaterialByMaterialId($materialId);
        if ($existing) {
            throw new Exception("Sudah ada text material untuk material ini.");
        }

        $data['content'] = $content;
        return $this->dao->insertTextMaterial($data);
    }

    // READ ALL: Mengambil semua text material
    public function getAllTextMaterials() {
        return $this->dao->getAllTextMaterials();

    }

    public function getTextMaterialById($id) {
        if (empty($id)) {
            throw new Exception("text_id tidak boleh kosong.");
        }

        return $this->dao->getTextMaterialById($id);
    }


    public function getTextMaterialByMaterialId($materialId) {
        return $this->dao->getTextMaterialById($materialId);
    }


        public function updateTextMaterial($text_id, $data) {
        $content = trim(strip_tags($data['content'] ?? ''));

        // Pastikan debugging logs seperti var_dump dan error_log Dihapus di produksi
        // var_dump($content); // Menampilkan isi konten
        // error_log("Content length: " . strlen($content)); // Menulis panjang konten ke log

        if (empty($content) || strlen($content) < 100) {
            throw new Exception("Konten harus minimal 100 karakter.");
        }
        
        // Periksa keberadaan text material sebelum update
        // Pastikan getTextMaterialById($id) di DAO berfungsi dengan baik
        $existing = $this->dao->getTextMaterialById($text_id); 
        if (!$existing) {
            throw new Exception("Text material tidak ditemukan.");
        }
        
        // Siapkan data untuk update. 
        // Penting: Hanya masukkan kolom yang ada di tabel dan memang ingin diupdate.
        // Jika material_id tidak diupdate, jangan masukkan ke sini.
        $update_data = [
            'title' => $data['title'],
            'content' => $data['content'],
            // Jika ada image_path atau kolom lain yang ingin diupdate, tambahkan di sini:
            // 'image_path' => $data['image_path'] ?? null,
        ];

        // Lakukan update melalui DAO. Ini akan mengembalikan true/false.
        $is_updated = $this->dao->updateTextMaterial($update_data, $text_id); 

        if ($is_updated) {
            // !!! BAGIAN KRUSIAL: Ambil data terbaru dari database setelah update berhasil !!!
            return $this->dao->getTextMaterialById($text_id); // Mengembalikan objek/array data yang di-update
        } else {
            // Jika DAO mengembalikan false, berarti update gagal
            throw new Exception("Gagal memperbarui text material di database.");
        }
    }

    


    public function deleteTextMaterialById($textId) {
        return $this->dao->deleteTextMaterial($textId);
    }
}
