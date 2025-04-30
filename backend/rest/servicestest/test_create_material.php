<?php
require_once __DIR__ . '/../services/MaterialService.php';

$service = new MaterialService();

try {
    $result = $service->createMaterial("Topik Baru Kimia", "Penjelasan lengkap tentang topik kimia ini.");
    echo "Material berhasil dibuat dengan ID: $result\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
