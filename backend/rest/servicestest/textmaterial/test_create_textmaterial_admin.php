<?php
require_once __DIR__ . '/../../services/TextMaterialService.php';

// Masukkan JWT yang valid milik admin
$adminJwt = '
eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjEwLCJlbWFpbCI6ImJ1ZGlAdGVzdC5jb20iLCJyb2xlIjoiQWRtaW4iLCJpYXQiOjE3NDYxMDk1NDQsImV4cCI6MjA2MTQ2OTU0NH0.pecnT2kSnRgAxiBGyauSH4RRN47bHO-N5UexkrEzfic'; // Replace dengan token JWT admin valid

$service = new TextMaterialService();

$data = [
    'material_id' => 1, // Pastikan ID ini ada di database
    'content' => str_repeat("Ini adalah konten text material yang cukup panjang. ", 5) // ≥100 chars
];

try {
    $result = $service->createTextMaterial($adminJwt, $data);
    echo "✅ TextMaterial berhasil dibuat. ID: " . $result . PHP_EOL;
} catch (Exception $e) {
    echo "❌ Gagal: " . $e->getMessage() . PHP_EOL;
}
