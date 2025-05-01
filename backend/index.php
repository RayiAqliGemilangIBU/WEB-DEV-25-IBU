<?php
require_once __DIR__ . '/flight/Flight.php';
require_once __DIR__ . '/service/MaterialService.php';

// Inisialisasi service
$materialService = new MaterialService();

// Routing
Flight::route('GET /materials', function() use ($materialService) {
    $materials = $materialService->getAllMaterials();
    Flight::json($materials);
});

Flight::route('GET /materials/@id', function($id) use ($materialService) {
    $material = $materialService->getMaterialById($id);
    if ($material) {
        Flight::json($material);
    } else {
        Flight::halt(404, 'Material not found');
    }
});

Flight::route('POST /materials', function() use ($materialService) {
    $data = Flight::request()->data->getData();
    $created = $materialService->createMaterial($data);
    Flight::json($created);
});

Flight::route('PUT /materials/@id', function($id) use ($materialService) {
    $data = Flight::request()->data->getData();
    $updated = $materialService->updateMaterial($id, $data);
    Flight::json($updated);
});

Flight::route('DELETE /materials/@id', function($id) use ($materialService) {
    $deleted = $materialService->deleteMaterial($id);
    Flight::json(["deleted" => $deleted]);
});

Flight::start();
