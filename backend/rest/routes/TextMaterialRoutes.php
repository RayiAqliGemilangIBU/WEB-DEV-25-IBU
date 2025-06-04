<?php

require_once __DIR__ . '/../services/MaterialService.php';
require_once __DIR__ . '/../services/TextMaterialService.php';
// require_once __DIR__ . '/../util/JwtExtractor.php';
require_once __DIR__ . '/../services/QuizService.php';
require_once __DIR__ . '/../services/QuestionService.php';
require_once __DIR__ . '/../services/OptionItemService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/StudentAnswerService.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';



$textMaterialService = new TextMaterialService();

/**
 * @OA\Post(
 *     path="/textmaterials",
 *     summary="Create a new TextMaterial",
 *     tags={"TextMaterials"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"material_id", "title", "content"},
 *             @OA\Property(property="material_id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Judul"),
 *             @OA\Property(property="content", type="string", example="Isi konten"),
 *             @OA\Property(property="image_path", type="string", example="path/gambar.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="TextMaterial created",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad Request")
 * )
 */
Flight::route('POST /textmaterials', function() use ($textMaterialService) {

    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    $data = Flight::request()->data->getData();

    try {
        $material_id = $data['material_id'] ?? null;

        if (!$material_id) {
            throw new Exception("Material ID harus disediakan.");
        }

        $created = $textMaterialService->createTextMaterial($material_id, $data);
        Flight::json(["success" => true, "message" => "TextMaterial created", "data" => $created]);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});

/**
 * @OA\Get(
 *     path="/textmaterials",
 *     summary="Get all TextMaterials",
 *     tags={"TextMaterials"},
 *     @OA\Response(
 *         response=200,
 *         description="List of all text materials",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /textmaterials', function() use ($textMaterialService) {
    $list = $textMaterialService->getAllTextMaterials();
    Flight::json(["success" => true, "data" => $list]);
});

/**
 * @OA\Get(
 *     path="/textmaterial/{id}",
 *     summary="Get text material by ID",
 *     tags={"TextMaterials"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the text material",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Text material detail",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid text material ID"
 *     )
 * )
 */
Flight::route('GET /textmaterial/@id', function($id) use ($textMaterialService) {
    try {
        $textMaterial = $textMaterialService->getTextMaterialById($id);
        Flight::json(["success" => true, "data" => $textMaterial]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});


/**
 * @OA\Get(
 *     path="/textmaterials/{material_id}",
 *     summary="Get TextMaterials by material ID",
 *     tags={"TextMaterials"},
 *     @OA\Parameter(
 *         name="material_id",
 *         in="path",
 *         required=true,
 *         description="Material ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of text materials by material ID",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /textmaterials/@material_id', function($material_id) use ($textMaterialService) {
    $list = $textMaterialService->getTextMaterialByMaterialId($material_id);
    // Flight::json(["success" => true, "data" => $list]);
    Flight::json($list);
});

/**
 * @OA\Put(
 *     path="/textmaterials/{text_id}",
 *     summary="Update a TextMaterial by text ID",
 *     tags={"TextMaterials"},
 *     @OA\Parameter(
 *         name="text_id",
 *         in="path",
 *         required=true,
 *         description="TextMaterial ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Judul baru"),
 *             @OA\Property(property="content", type="string", example="Konten baru"),
 *             @OA\Property(property="image_path", type="string", example="path/gambar_baru.jpg")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="TextMaterial updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="updated", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad Request")
 * )
 */
Flight::route('PUT /textmaterials/@text_id', function($text_id) use ($textMaterialService) {

    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    $data = Flight::request()->data->getData();

    try {
        $updated = $textMaterialService->updateTextMaterial($text_id, $data);
        Flight::json(["success" => true, "message" => "TextMaterial updated", "updated" => $updated]);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});

/**
 * @OA\Delete(
 *     path="/textmaterials/id/{text_id}",
 *     summary="Delete a TextMaterial by ID",
 *     tags={"TextMaterials"},
 *     @OA\Parameter(
 *         name="text_id",
 *         in="path",
 *         required=true,
 *         description="TextMaterial ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="TextMaterial deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="deleted", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad Request")
 * )
 */
Flight::route('DELETE /textmaterials/id/@text_id', function($text_id) use ($textMaterialService) {

    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    try {
        $deleted = $textMaterialService->deleteTextMaterialById($text_id);
        Flight::json(["success" => true, "message" => "TextMaterial deleted", "deleted" => $deleted]);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});
