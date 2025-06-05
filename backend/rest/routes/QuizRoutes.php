<?php
require_once __DIR__ . '/../services/QuizService.php';
require_once __DIR__ . '/../services/MaterialService.php';
require_once __DIR__ . '/../services/TextMaterialService.php';
require_once __DIR__ . '/../services/QuestionService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/StudentAnswerService.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';


$quizService = new QuizService();

/**
 * @OA\Get(
 * path="/quiz",
 * summary="Get all quizzes (Admin Dashboard)",
 * tags={"Quiz"},
 * security={{"bearerAuth":{}}},
 * @OA\Response(
 * response=200,
 * description="List of quizzes successfully retrieved",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(
 * property="data",
 * type="array",
 * @OA\Items(
 * type="object",
 * @OA\Property(property="quiz_id", type="integer", example=1),
 * @OA\Property(property="material_id", type="integer", example=1),
 * @OA\Property(property="title", type="string", example="Atomic Structure Fundamentals"),
 * @OA\Property(property="description", type="string", nullable=true, example="A basic quiz on atomic structure."),
 * @OA\Property(property="question_count", type="integer", example=5)
 * )
 * )
 * )
 * ),
 * @OA\Response(
 * response=500,
 * description="Internal server error",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=false),
 * @OA\Property(property="message", type="string", example="Unexpected error occurred.")
 * )
 * )
 * )
 */
Flight::route('GET /quiz', function () use ($quizService) {
    // Flight::middleware('AuthMiddleware'); 
    // (new RoleMiddleware())->requireRole('Admin'); // Jika hanya Admin
    try {
        $quizzes = $quizService->getAllQuizzesForAdminDashboard();
        Flight::json(["success" => true, "data" => $quizzes]);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["success" => false, "message" => $e->getMessage()]));
    }
});

/**
 * @OA\Get(
 * path="/quiz/material/{material_id}",
 * summary="Get the quiz associated with a specific material",
 * tags={"Quiz"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="material_id",
 * in="path",
 * required=true,
 * description="ID of the material to fetch the quiz for",
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Quiz object if found, or null if no quiz is associated.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(
 * property="quiz", 
 * type="object", 
 * nullable=true,
 * @OA\Property(property="quiz_id", type="integer", example=1),
 * @OA\Property(property="material_id", type="integer", example=1),
 * @OA\Property(property="title", type="string", example="Atomic Structure Fundamentals"),
 * @OA\Property(property="description", type="string", nullable=true, example="A basic quiz on atomic structure.")
 * )
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid material ID provided.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=false),
 * @OA\Property(property="message", type="string", example="Invalid material ID.")
 * )
 * )
 * )
 */
Flight::route('GET /quiz/material/@material_id', function($material_id) use ($quizService) {
    // Flight::middleware('AuthMiddleware');
    // (new RoleMiddleware())->requireRole('Admin');
    try {
        $quizObject = $quizService->getQuizByMaterialId((int)$material_id);
        Flight::json(["success" => true, "quiz" => $quizObject]);
    } catch (Exception $e) {
        Flight::halt(400, json_encode(["success" => false, "message" => $e->getMessage()]));
    }
});

/**
 * @OA\Get(
 * path="/quiz/{id}",
 * summary="Get a specific quiz by its ID",
 * tags={"Quiz"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="id",
 * in="path",
 * required=true,
 * description="ID of the quiz to retrieve",
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Detailed information about the quiz.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(
 * property="data", 
 * type="object",
 * @OA\Property(property="quiz_id", type="integer", example=1),
 * @OA\Property(property="material_id", type="integer", example=1),
 * @OA\Property(property="title", type="string", example="Atomic Structure Fundamentals"),
 * @OA\Property(property="description", type="string", nullable=true, example="A basic quiz on atomic structure."),
 * @OA\Property(property="question_count", type="integer", example=5)
 * )
 * )
 * ),
 * @OA\Response(
 * response=404,
 * description="Quiz not found.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=false),
 * @OA\Property(property="message", type="string", example="Quiz not found.")
 * )
 * ),
 * @OA\Response(
 * response=500,
 * description="Server error.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=false),
 * @OA\Property(property="message", type="string", example="An internal server error occurred.")
 * )
 * )
 * )
 */
Flight::route('GET /quiz/@id', function ($id) use ($quizService) {
    // Flight::middleware('AuthMiddleware');
    try {
        $quiz = $quizService->getQuizWithDetails((int)$id);
        if ($quiz) {
            Flight::json(["success" => true, "data" => $quiz]);
        } else {
            Flight::halt(404, json_encode(["success" => false, "message" => "Quiz not found"]));
        }
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["success" => false, "message" => $e->getMessage()]));
    }
});

/**
 * @OA\Post(
 * path="/quiz",
 * summary="Create a new quiz",
 * tags={"Quiz"},
 * security={{"bearerAuth":{}}},
 * @OA\RequestBody(
 * description="Quiz object that needs to be added",
 * required=true,
 * @OA\JsonContent(
 * type="object",
 * required={"material_id", "title"},
 * @OA\Property(property="material_id", type="integer", example=1),
 * @OA\Property(property="title", type="string", example="New Quiz on Bonding"),
 * @OA\Property(property="description", type="string", nullable=true, example="Covers ionic and covalent bonds.")
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Quiz created successfully.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Quiz created successfully"),
 * @OA\Property(
 * property="quiz", 
 * type="object",
 * @OA\Property(property="quiz_id", type="integer", example=2),
 * @OA\Property(property="material_id", type="integer", example=1),
 * @OA\Property(property="title", type="string", example="New Quiz on Bonding"),
 * @OA\Property(property="description", type="string", nullable=true, example="Covers ionic and covalent bonds.")
 * )
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid input provided.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=false),
 * @OA\Property(property="message", type="string", example="Invalid input: material_id and title are required.")
 * )
 * ),
 * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string"))),
 * @OA\Response(response=403, description="Forbidden (User is not Admin)", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string")))
 * )
 */
Flight::route('POST /quiz', function () use ($quizService) {
    Flight::middleware('AuthMiddleware'); 
    (new RoleMiddleware())->requireRole('Admin');

    $data = Flight::request()->data->getData();
    try {
        $newQuiz = $quizService->createQuiz($data);
        Flight::json(["success" => true, "message" => "Quiz created successfully", "quiz" => $newQuiz], 201);
    } catch (Exception $e) {
        Flight::halt(400, json_encode(["success" => false, "message" => $e->getMessage()]));
    }
});

/**
 * @OA\Put(
 * path="/quiz/{quiz_id}",
 * summary="Update an existing quiz",
 * tags={"Quiz"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="quiz_id",
 * in="path",
 * required=true,
 * description="ID of the quiz to update",
 * @OA\Schema(type="integer")
 * ),
 * @OA\RequestBody(
 * description="Quiz object with updated fields.",
 * required=true,
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="title", type="string", example="Advanced Atomic Structure Quiz"),
 * @OA\Property(property="description", type="string", nullable=true, example="An in-depth quiz covering advanced topics.")
 * )
 * ),
 * @OA\Response(
 * response=200,
 * description="Quiz updated successfully.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Quiz updated successfully"),
 * @OA\Property(
 * property="quiz", 
 * type="object",
 * @OA\Property(property="quiz_id", type="integer", example=1),
 * @OA\Property(property="material_id", type="integer", example=1),
 * @OA\Property(property="title", type="string", example="Advanced Atomic Structure Quiz"),
 * @OA\Property(property="description", type="string", nullable=true, example="An in-depth quiz covering advanced topics.")
 * )
 * )
 * ),
 * @OA\Response(response=400, description="Invalid input or update failed.", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string"))),
 * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string"))),
 * @OA\Response(response=403, description="Forbidden", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string"))),
 * @OA\Response(response=404, description="Quiz not found.", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string")))
 * )
 */
Flight::route('PUT /quiz/@quiz_id', function ($quiz_id) use ($quizService) {
    Flight::middleware('AuthMiddleware');
    (new RoleMiddleware())->requireRole('Admin');

    $data = Flight::request()->data->getData();
    try {
        $updatedQuiz = $quizService->updateQuiz((int)$quiz_id, $data);
        Flight::json(["success" => true, "message" => "Quiz updated successfully", "quiz" => $updatedQuiz]);
    } catch (Exception $e) {
        if (stripos($e->getMessage(), "not found") !== false || stripos($e->getMessage(), "failed to retrieve") !== false) {
             Flight::halt(404, json_encode(["success" => false, "message" => "Quiz not found or update failed."]));
        } else {
             Flight::halt(400, json_encode(["success" => false, "message" => $e->getMessage()]));
        }
    }
});

/**
 * @OA\Delete(
 * path="/quiz/{quiz_id}",
 * summary="Delete a quiz and its dependencies (questions)",
 * tags={"Quiz"},
 * security={{"bearerAuth":{}}},
 * @OA\Parameter(
 * name="quiz_id",
 * in="path",
 * required=true,
 * description="ID of the quiz to delete",
 * @OA\Schema(type="integer")
 * ),
 * @OA\Response(
 * response=200,
 * description="Quiz and its dependencies deleted successfully.",
 * @OA\JsonContent(
 * type="object",
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Quiz and its dependencies deleted successfully")
 * )
 * ),
 * @OA\Response(response=401, description="Unauthorized", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string"))),
 * @OA\Response(response=403, description="Forbidden", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string"))),
 * @OA\Response(response=404, description="Quiz not found.", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string"))),
 * @OA\Response(response=500, description="Server error during deletion.", @OA\JsonContent(type="object", @OA\Property(property="success", type="boolean", example=false), @OA\Property(property="message", type="string")))
 * )
 */
Flight::route('DELETE /quiz/@quiz_id', function ($quiz_id) use ($quizService) {
    Flight::middleware('AuthMiddleware');
    (new RoleMiddleware())->requireRole('Admin');

    try {
        $quizService->deleteQuiz((int)$quiz_id);
        Flight::json(["success" => true, "message" => "Quiz and its dependencies deleted successfully"]);
    } catch (Exception $e) {
         if (stripos($e->getMessage(), "failed to delete") !== false) {
             Flight::halt(500, json_encode(["success" => false, "message" => $e->getMessage()]));
         } else {
            Flight::halt(404, json_encode(["success" => false, "message" => "Quiz not found or error during deletion: " . $e->getMessage()]));
         }
    }
});
