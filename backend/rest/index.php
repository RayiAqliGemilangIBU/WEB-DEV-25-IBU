<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/services/MaterialService.php';

require_once __DIR__ . '/services/TextMaterialService.php';

// Inisialisasi service
$materialService = new MaterialService();

// Routing GET All Materials
Flight::route('GET /materials', function() use ($materialService) {
    $materials = $materialService->getAllMaterials();
    Flight::json($materials);
});

// Routing GET Material by ID
Flight::route('GET /materials/@id', function($id) use ($materialService) {
    $material = $materialService->getMaterialById($id);
    if ($material) {
        Flight::json($material);
    } else {
        Flight::halt(404, 'Material not found');
    }
});

// Routing POST Create Material
Flight::route('POST /materials', function() use ($materialService) {
    $data = Flight::request()->data->getData();
    try {
        if (empty($data['title']) || empty($data['description'])) {
            Flight::halt(400, 'Title and Description are required');
        }

        $created = $materialService->createMaterial($data);
        Flight::json(["success" => true, "message" => "Material created", "data" => $created], 201);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});

// Routing PUT Update Material
Flight::route('PUT /materials/@id', function($id) use ($materialService) {
    $data = Flight::request()->data->getData();
    try {
        // Validasi data (contoh)
        if (empty($data['title']) || empty($data['description'])) {
            Flight::halt(400, 'Title and Description are required');
        }

        $updated = $materialService->updateMaterial($id, $data);
        if ($updated) {
            Flight::json(["success" => true, "message" => "Material updated"]);
        } else {
            Flight::halt(404, 'Material not found');
        }
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400); // 400 Bad Request
    }
});

// Routing DELETE Material
Flight::route('DELETE /materials/@id', function($id) use ($materialService) {
    $deleted = $materialService->deleteMaterial($id);
    if ($deleted) {
        Flight::json(["success" => true, "message" => "Material deleted"]);
    } else {
        Flight::halt(404, 'Material not found');
    }
});


// -----------

$textMaterialService = new TextMaterialService();
/**
 * POST /textmaterials
 * Membuat TextMaterial baru
 * Data JSON yang dikirim: { "material_id": 1, "title": "Judul", "content": "Isi konten", "image_path": "path/gambar.jpg" }
 */
Flight::route('POST /textmaterials', function() use ($textMaterialService) {
    $data = Flight::request()->data->getData();
    try {
        $createdId = $textMaterialService->createTextMaterial($data);
        Flight::json(["success" => true, "message" => "TextMaterial created", "id" => $createdId], 201);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});

/**
 * GET /textmaterials
 * Mengambil semua TextMaterial yang ada di database
 */
Flight::route('GET /textmaterials', function() use ($textMaterialService) {
    $list = $textMaterialService->getAllTextMaterials();
    Flight::json(["success" => true, "data" => $list]);
});

/**
 * GET /textmaterials/@material_id
 * Mengambil semua TextMaterial berdasarkan material_id tertentu
 */
Flight::route('GET /textmaterials/@material_id', function($material_id) use ($textMaterialService) {
    // Panggil fungsi yang benar sesuai yang ada di service
    $list = $textMaterialService->getTextMaterialByMaterialId($material_id);
    Flight::json(["success" => true, "data" => $list]);
});
/**
 * PUT /textmaterials/@text_id
 * Mengubah TextMaterial berdasarkan text_id
 * Data JSON yang dikirim bisa meliputi: { "title": "...", "content": "...", "image_path": "..." }
 */
Flight::route('PUT /textmaterials/@text_id', function($text_id) use ($textMaterialService) {
    $data = Flight::request()->data->getData();
    try {
        $updated = $textMaterialService->updateTextMaterial($text_id, $data);
        Flight::json(["success" => true, "message" => "TextMaterial updated", "updated" => $updated]);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});

/**
 * DELETE /textmaterials/@material_id
 * Menghapus semua TextMaterial berdasarkan material_id
 */
Flight::route('DELETE /textmaterials/@material_id', function($material_id) use ($textMaterialService) {
    try {
        $deleted = $textMaterialService->deleteTextMaterialsByMaterialId($material_id);
        Flight::json(["success" => true, "message" => "TextMaterials deleted", "deleted" => $deleted]);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});

/**
 * OPTIONAL: DELETE /textmaterials/id/@text_id
 * Jika ingin menghapus satu TextMaterial berdasarkan text_id
 */
Flight::route('DELETE /textmaterials/id/@text_id', function($text_id) use ($textMaterialService) {
    try {
        $deleted = $textMaterialService->deleteTextMaterialById($text_id);
        Flight::json(["success" => true, "message" => "TextMaterial deleted", "deleted" => $deleted]);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});

Flight::start();
