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

    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    $data = Flight::request()->data->getData();
    try {
        if (empty($data['title']) || empty($data['description'])) {
            Flight::halt(400, 'Title and Description are required');
        }

        $created = $materialService->createMaterial($data);
        Flight::json(["success" => true, "message" => "Material created", "id" => $created], 201);
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

    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

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
    Flight::middleware(); //for auth
    (RoleMiddleware::requireRole('Admin'))();//for auth

    $deleted = $materialService->deleteMaterial($id);
    if ($deleted) {
        Flight::json(["success" => true, "message" => "Material deleted"]);
    } else {
        Flight::halt(404, 'Material not found');
    }
});



/**
 * @OA\Post(
 * path="/materials/upload",
 * summary="Upload a CSV file to create a new material with its text content, quiz, and questions",
 * tags={"Materials"},
 * security={{"bearerAuth":{}}},
 * @OA\RequestBody(
 * required=true,
 * description="CSV file containing all material data.",
 * @OA\MediaType(
 * mediaType="multipart/form-data",
 * @OA\Schema(
 * type="object",
 * required={"material_file"},
 * @OA\Property(
 * property="material_file",
 * type="string",
 * format="binary",
 * description="The CSV file to upload."
 * )
 * )
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Material, text material, quiz, and questions created successfully from file.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Material created successfully from file."),
 * @OA\Property(property="material_id", type="integer", example=10),
 * @OA\Property(property="quiz_id", type="integer", example=12)
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Bad Request - Invalid file, file format error, or data validation failed.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=false),
 * @OA\Property(property="message", type="string", example="Error processing file: Invalid CSV format.")
 * )
 * ),
 * @OA\Response(response=401, description="Unauthorized"),
 * @OA\Response(response=403, description="Forbidden (User is not Admin)"),
 * @OA\Response(response=500, description="Internal Server Error")
 * )
 */
Flight::route('POST /materials/upload', function() use ($materialService) {
    // Otentikasi dan Otorisasi (Admin)
    Flight::middleware('AuthMiddleware'); // Pastikan middleware ini ada dan berfungsi
    (new RoleMiddleware())->requireRole('Admin');

    try {
        // Periksa apakah file diunggah
        if (!isset($_FILES['material_file']) || $_FILES['material_file']['error'] != UPLOAD_ERR_OK) {
            Flight::halt(400, json_encode([
                "success" => false,
                "message" => "No file uploaded or there was an upload error. Please select a CSV file."
            ]));
            return;
        }

        $fileData = $_FILES['material_file'];

        // Periksa tipe file (ekstensi)
        $fileName = $fileData['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if ($fileExtension !== 'csv') {
            Flight::halt(400, json_encode([
                "success" => false,
                "message" => "Invalid file type. Only .csv files are allowed."
            ]));
            return;
        }

        // Dapatkan ID pengguna yang sedang login (pembuat materi)
        // Ini asumsi Anda memiliki cara untuk mendapatkan user_id dari token JWT
        // yang mungkin sudah disimpan oleh AuthMiddleware Anda di Flight::get('user') atau cara lain.
        $loggedInUser = Flight::get('user'); // Ini contoh, sesuaikan dengan implementasi Anda
        if (!$loggedInUser || !isset($loggedInUser['sub'])) { // 'sub' biasanya berisi user_id di JWT payload
            Flight::halt(401, json_encode(["success" => false, "message" => "User not authenticated or user ID not found in token."]));
            return;
        }
        $createdByUserId = $loggedInUser['sub'];

        // Panggil service untuk memproses file
        $result = $materialService->createMaterialFromUploadedFile($fileData, $createdByUserId);

        Flight::json([
            "success" => true,
            "message" => "Material, text material, quiz, and questions created successfully from file.",
            "material_id" => $result['material_id'],
            "quiz_id" => $result['quiz_id']
            // Anda bisa menambahkan detail lain jika diperlukan
        ], 201); // 201 Created

    } catch (InvalidArgumentException $e) {
        Flight::halt(400, json_encode(["success" => false, "message" => "Error processing file: " . $e->getMessage()]));
    } catch (Exception $e) {
        // Log error $e->getMessage() dan $e->getTraceAsString() di server Anda
        error_log("Error in POST /materials/upload: " . $e->getMessage());
        Flight::halt(500, json_encode(["success" => false, "message" => "An unexpected error occurred on the server."]));
    }
});

