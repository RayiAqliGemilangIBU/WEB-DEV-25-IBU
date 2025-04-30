<?php
require_once __DIR__ . '/../services/TextMaterialService.php';

$service = new TextMaterialService();

try {
    $data = [
        'material_id' => 1,  // Sesuaikan ID sesuai data kamu
        'content' => str_repeat("Penjelasan ini sangat penting. ", 5) // > 100 karakter
    ];

    $newId = $service->createTextMaterial($data);
    echo "TextMaterial berhasil dibuat dengan ID: $newId\n";

} catch (Exception $e) {
    echo "Gagal: " . $e->getMessage() . "\n";
}
