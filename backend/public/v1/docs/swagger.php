<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/../../../rest/vendor/autoload.php';

// Tentukan BASE_URL sesuai dengan kondisi server
if ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1') {
    define('BASE_URL', 'http://localhost/WEB-DEV-25-IBU/backend');
} else {
    define('BASE_URL', 'https://add-production-server-after-deployment/backend/');
}

// Pastikan path ke doc_setup.php dan index.php sesuai
$openapi = \OpenApi\Generator::scan([
    __DIR__ . '/doc_setup.php',  // doc_setup.php ada di folder yang sama dengan swagger.php
    // __DIR__ . '/../../../rest/index.php'  // Sesuaikan dengan path relatif ke file index.php
    __DIR__ . '/../../../rest/routes' 
]);

// Output JSON untuk dokumentasi Swagger
header('Content-Type: application/json');
echo $openapi->toJson();
?>
