<?php
require_once __DIR__ . '/../../services/AuthService.php';

$authService = new AuthService();

// Simulasi token yang sebelumnya didapat saat login
$token = '
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjksImVtYWlsIjoidWNva0B0ZXN0LmNvbSIsInJvbGUiOiJTdHVkZW50IiwiaWF0IjoxNzQ2MTA0MzMwLCJleHAiOjIwNjE0NjQzMzB9.0HHosY1xm7m_7PuY-6zixryTQw8UO0YLfrL3QSBe2PQ
'; // Bisa diambil dari hasil login sebelumnya

// Logout user (misalnya invalidasi token, hapus sesi, dll)
$response = $authService->logoutUser($token);

if ($response['status']) {
    echo "Logout successful!\n";
} else {
    echo "Logout failed: " . $response['message'] . "\n";
}
