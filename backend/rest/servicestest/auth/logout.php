<?php
require_once __DIR__ . '/../../services/AuthService.php';

$authService = new AuthService();

// Simulasi token yang sebelumnya didapat saat login
$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjgsImVtYWlsIjoibmV3dXNlckBkb21haW4uY29tIiwicm9sZSI6IlN0dWRlbnQiLCJpYXQiOjE3NDYxMDIyODMsImV4cCI6MTc0NjEwNTg4M30.nPu716YWMZnelpa13Jdi7_UxJrFXuIJuBokwKrXvcD8'; // Bisa diambil dari hasil login sebelumnya

// Logout user (misalnya invalidasi token, hapus sesi, dll)
$response = $authService->logoutUser($token);

if ($response['status']) {
    echo "Logout successful!\n";
} else {
    echo "Logout failed: " . $response['message'] . "\n";
}
