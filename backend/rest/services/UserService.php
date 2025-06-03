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



    // class UserService {
//     private $userDao;

//     public function __construct() {
//         $this->userDao = new UserDao();
//     }

//     public function registerUser($data) {
//         if ($this->userDao->emailExists($data['email'])) {
//             return ['success' => false, 'message' => 'Email already registered'];
//         }

//         $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
//         $userId = $this->userDao->insert('user', $data);
//         return $userId ? ['success' => true, 'user_id' => $userId] : ['success' => false, 'message' => 'Registration failed'];
//     }

//     public function loginUser($email, $password) {
//         $user = $this->userDao->authenticate($email, $password);
//         if (!$user) {
//             return ['success' => false, 'message' => 'Invalid credentials'];
//         }

//         // Generate JWT
//         $payload = [
//             'user_id' => $user['user_id'],
//             'email' => $user['email'],
//             'exp' => time() + (60 * 60) // 1 hour expiration
//         ];

//         $jwt = $this->generateJWT($payload);
//         return ['success' => true, 'token' => $jwt, 'user' => $user];
//     }

//     public function updateUser($id, $data) {
//         return $this->userDao->updateUser($data, $id);
//     }

//     public function getUserByEmail($email) {
//         return $this->userDao->getUserByEmail($email);
//     }

//     public function getUserByToken($token) {
//         $decoded = $this->validateJWT($token);
//         if (!$decoded) return null;

//         return $this->userDao->getUserByEmail($decoded->email);
//     }

//     private function generateJWT($payload) {
//         $key = 'your_secret_key';
//         $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
//         $payload = json_encode($payload);
//         $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
//         $base64UrlPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
//         $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $key, true);
//         $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
//         return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
//     }

//     private function validateJWT($jwt) {
//         $key = 'your_secret_key';
//         $parts = explode('.', $jwt);
//         if (count($parts) !== 3) return false;

//         list($header, $payload, $signature) = $parts;
//         $valid_signature = hash_hmac('sha256', "$header.$payload", $key, true);
//         $valid_signature = rtrim(strtr(base64_encode($valid_signature), '+/', '-_'), '=');

//         if (!hash_equals($valid_signature, $signature)) return false;

//         $payload = json_decode(base64_decode($payload));
//         if ($payload->exp < time()) return false;

//         return $payload;
//     }



//     public function getUserById($id) {
//         $user = $this->userDao->getUserById($id);
//         if (!$user) {
//             Flight::halt(404, "User not found");
//         }
//         return $user;
//     }

//     public function getAllUser() {
//         return $this->userDao->getAllUser();
//     }
    
// }

// <?php
// require_once __DIR__ . '/../dao/UserDao.php';
// require_once __DIR__ . '/BaseService.php';

    // private function generateJWT($payload) {
    //     $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    //     $payload = json_encode($payload);

    //     $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
    //     $base64UrlPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');

    //     $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $this->secretKey, true);
    //     $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

    //     return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    // }

    // private function validateJWT($jwt) {
    //     $parts = explode('.', $jwt);
    //     if (count($parts) !== 3) return false;

    //     list($header, $payload, $signature) = $parts;
    //     $validSignature = hash_hmac('sha256', "$header.$payload", $this->secretKey, true);
    //     $validSignature = rtrim(strtr(base64_encode($validSignature), '+/', '-_'), '=');

    //     if (!hash_equals($validSignature, $signature)) return false;

    //     $payload = json_decode(base64_decode($payload));
    //     if ($payload->exp < time()) return false;

    //     return $payload;
    // }
}

