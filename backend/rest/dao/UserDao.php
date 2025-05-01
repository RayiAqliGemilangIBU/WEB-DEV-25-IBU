<?php
require_once 'BaseDao.php';

class UserDao extends BaseDao {

    public function insert($table, $data) {
        // Menyusun query insert dinamis
        $columns = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($values)";

        // Persiapkan statement dan bind parameters
        $stmt = $this->conn->prepare($sql);

        // Bind data
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        // Eksekusi dan cek
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Kembalikan ID terakhir yang dimasukkan
        } else {
            return false; // Jika gagal
        }
    }

    // Fungsi untuk update data user
    public function updateUser($data, $id) {
        // Menentukan nama tabel dan kolom ID
        $table = 'user'; // Nama tabel
        $idField = 'user_id'; // Kolom ID
            
        // Memanggil fungsi update dari BaseDao
        return $this->update($table, $data, $idField, $id);
    }

    // Fungsi untuk verifikasi kredensial login (email dan password)
    public function verifyLogin($email, $password) {
        $sql = "SELECT * FROM user WHERE email = :email AND password = :password";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);

        $stmt->execute();

        // Cek jika ada user yang cocok
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user : null; // Kembalikan data user atau null jika tidak ada
    }


            // Mencari user berdasarkan email
        public function findByEmail($email) {
            $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Verifikasi kredensial login
        public function verifyCredentials($email, $password) {
            $user = $this->findByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                return $user; // Login berhasil
            }
            return false; // Gagal login
        }

        // Mengecek apakah email sudah digunakan
        public function emailExists($email) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM user WHERE email = :email");
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        }

                // Fungsi untuk login / autentikasi user berdasarkan email dan password
                public function authenticate($email, $password) {
                    $stmt = $this->conn->prepare("SELECT * FROM user WHERE email = :email");
                    $stmt->bindValue(':email', $email);
                    $stmt->execute();
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    if ($user) {
                        // DEBUG: cek hash password dari DB dan hasil verifikasi
                        $hashedPassword = $user['password'];
                        echo "Hashed from DB: $hashedPassword\n";
                
                        if (password_verify($password, $hashedPassword)) {
                            echo "Password match!\n";
                            return $user;
                        } else {
                            echo "Password doesn't match.\n";
                        }
                    }
                
                    return false;
                }

        // Fungsi untuk mengambil data user berdasarkan token (jika nanti pakai sistem token)
        public function getUserByToken($token) {
            $sql = "SELECT * FROM user WHERE token = :token";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(":token", $token);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function getUserByEmail($email) {
            $sql = "SELECT * FROM user WHERE email = :email";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC); // return 1 row (user) atau false
        }
}
