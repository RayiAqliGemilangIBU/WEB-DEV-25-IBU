<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/MaterialService.php';
require_once __DIR__ . '/../services/TextMaterialService.php';
require_once __DIR__ . '/../util/JwtExtractor.php';
require_once __DIR__ . '/../services/QuizService.php';
require_once __DIR__ . '/../services/QuestionService.php';
require_once __DIR__ . '/../services/OptionItemService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/StudentAnswerService.php';
require __DIR__ . '/../../vendor/autoload.php'; // Penting agar anotasi dikenali




// Inisialisasi service
$materialService = new MaterialService();

/**
 * @OA\Get(
 *     path="/materials",
 *     summary="Get all materials",
 *     tags={"Materials"},
 *     @OA\Response(
 *         response=200,
 *         description="List of materials",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string")
 *         ))
 *     )
 * )
 */
Flight::route('GET /materials', function() use ($materialService) {
    $materials = $materialService->getAllMaterials();
    Flight::json($materials);
});

/**
 * @OA\Get(
 *     path="/materials/{id}",
 *     summary="Get a material by ID",
 *     tags={"Materials"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Material ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Material found",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Material not found"
 *     )
 * )
 */
Flight::route('GET /materials/@id', function($id) use ($materialService) {
    $material = $materialService->getMaterialById($id);
    if ($material) {
        Flight::json($material);
    } else {
        Flight::halt(404, 'Material not found');
    }
});

/**
 * @OA\Post(
 *     path="/materials",
 *     summary="Create a new material",
 *     tags={"Materials"},
 *     @OA\RequestBody(
 *         required=true,
 *         description="Material data",
 *         @OA\JsonContent(
 *             required={"title", "description"},
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Material created",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Material created"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="description", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request"
 *     )
 * )
 */
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

/**
 * @OA\Put(
 *     path="/materials/{id}",
 *     summary="Update an existing material",
 *     tags={"Materials"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Material ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Updated material data",
 *         @OA\JsonContent(
 *             required={"title", "description"},
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Material updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Material updated")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Material not found"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad Request"
 *     )
 * )
 */
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

/**
 * @OA\Delete(
 *     path="/materials/{id}",
 *     summary="Delete a material",
 *     tags={"Materials"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Material ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Material deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Material deleted")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Material not found"
 *     )
 * )
 */
Flight::route('DELETE /materials/@id', function($id) use ($materialService) {
    $deleted = $materialService->deleteMaterial($id);
    if ($deleted) {
        Flight::json(["success" => true, "message" => "Material deleted"]);
    } else {
        Flight::halt(404, 'Material not found');
    }
});
