<?php
require_once __DIR__ . '/../../services/AuthService.php';

$authService = new AuthService();
$response = $authService->registerUser("budi@test.com", "budi", "budi", "Admin"); //registerUser($email, $password, $name, $role) {

if ($response["status"]) {
    echo "Registrasi Sukses: " . $response["message"];
} else {
    echo "Registrasi Gagal: " . $response["message"];
}

