<?php
class Database {
    private static $host = 'localhost';
    private static $port = '3306'; // default port MySQL di XAMPP
    private static $db = 'ChemLP1.2'; // Pastikan kamu sudah buat database ini
    private static $user = 'root'; // default user XAMPP
    private static $pass = ''; // default password XAMPP biasanya kosong
    private static $charset = 'utf8mb4';
    private static $pdo = null;

    public static function connect() {
        if (self::$pdo === null) {
            $dsn = "mysql:host=" . self::$host . ";port=" . self::$port . ";dbname=" . self::$db . ";charset=" . self::$charset;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            try {
                self::$pdo = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function JWT_SECRET() {
       return 'your_key_string';
   }


    
}
?>
