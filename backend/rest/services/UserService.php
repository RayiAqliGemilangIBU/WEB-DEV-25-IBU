<?php
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/BaseService.php';


class UserService extends BaseService {
    private $secretKey = 'your_secret_key';

    // protected $table = 'user';


    public function __construct() {
        parent::__construct(new UserDao());
         $this->table = 'user'; 
    }

public function registerUser($userData)

{
    error_log("RAW userData: " . file_get_contents("php://input"));
    error_log("DECODED userData: " . print_r($userData, true));


    foreach ($userData as $key => $value) {
    $userData[$key] = trim($value); // buang spasi, termasuk NBSP
}
    // Validasi: pastikan semua field penting tersedia
    if (!isset($userData['name'], $userData['email'], $userData['password'], $userData['role'])) {
        throw new Exception("Data not complete.");
    }

    // Hash password
    $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);

    // Logging isi data sebelum insert
    error_log("DATA INSERT: " . print_r($userData, true));

    // Insert ke DB
    return $this->dao->insert('user', $userData);
}





    public function updateUser($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            // Hash password sebelum dikirim ke BaseDao
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            // Jika tidak ada password, jangan update password
            unset($data['password']);
        }

        return $this->update($data, $id);  // Panggil fungsi dari BaseService
    }


    public function getUserByEmail($email) {
        return $this->dao->getUserByEmail($email);
    }

    public function getUserByToken($token) {
        $decoded = $this->validateJWT($token);
        if (!$decoded) return null;

        return $this->dao->getUserByEmail($decoded->email);
    }

    public function getUserById($id) {
        $user = $this->getById($this->table, 'user_id', $id);
        if (!$user) {
            Flight::halt(404, "User not found");
        }
        return $user;
    }

    public function getAllUser() {
        return $this->getAll();
    }

    public function deleteUser($id) {
        return $this->delete($id); // memanggil delete dari BaseService
    }

    public function getUsersByRole($role) {
    // Ensure $this->dao is an instance of UserDao
    if (!($this->dao instanceof UserDao)) {
        // This should ideally not happen if constructor is correct
        // but as a safeguard or if dao property can be something else.
        // Or, if your BaseService structure means $this->dao is generic,
        // you might need to cast or call a specific UserDao method.
        // For now, assuming $this->dao is always UserDao instance for UserService.
        throw new Exception("DAO is not an instance of UserDao in UserService.");
    }

    $users = $this->dao->getUsersByRole($role);
    
    // Remove password field from each user before returning
    // and any other fields you don't want to send to the frontend.
    foreach ($users as &$user) { // Use reference to modify array directly
        unset($user['password']);
        // unset($user['token']); // If you have a token column you want to hide
    }
    return $users;
    }   

    public function addStudent($studentData) {
        
        if (!isset($studentData['name'], $studentData['email'], $studentData['password'])) {
            throw new Exception("Student data not complete. Missing name, email, or password.");
        }

        
        $studentData['role'] = 'Student';

        
        return $this->registerUser($studentData);
    }



}

