<?php
require_once __DIR__ . '/../services/QuizService.php';
require_once __DIR__ . '/../services/MaterialService.php';
require_once __DIR__ . '/../services/TextMaterialService.php';
require_once __DIR__ . '/../services/QuestionService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/StudentAnswerService.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';


Flight::set('studentAnswerService', new StudentAnswerService());

/**
 * @OA\Get(
 *     path="/studentanswer",
 *     summary="Get all student answers",
 *     tags={"StudentAnswers"},
 *     @OA\Response(
 *         response=200,
 *         description="List of student answers",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(type="object")
 *         )
 *     )
 * )
 */
Flight::route('GET /studentanswer', function () {
    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    $service = Flight::get('studentAnswerService');
    Flight::json(["success" => true, "data" => $service->getAllAnswers()]);
});

/**
 * @OA\Get(
 *     path="/studentanswer/{id}",
 *     summary="Get student answer by ID",
 *     tags={"StudentAnswers"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the student answer",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Student answer detail",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(response=404, description="Answer not found")
 * )
 */
Flight::route('GET /studentanswer/@id', function ($id) {
    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    $service = Flight::get('studentAnswerService');
    $answer = $service->getAnswerById($id);
    if ($answer) {
        Flight::json(["success" => true, "data" => $answer]);
    } else {
        Flight::json(["success" => false, "message" => "Answer not found"], 404);
    }
});

/**
 * @OA\Post(
 *     path="/studentanswer",
 *     summary="Add a new student answer",
 *     tags={"StudentAnswers"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","question_id","selected_option_id"},
 *             @OA\Property(property="user_id", type="integer"),
 *             @OA\Property(property="question_id", type="integer"),
 *             @OA\Property(property="selected_option_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Answer created",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     )
 * )
 */
Flight::route('POST /studentanswer', function () {
    $data = Flight::request()->data->getData();

    $service = Flight::get('studentAnswerService');
    try {
        $result = $service->addStudentAnswer($data);
        Flight::json(["success" => true, "data" => $result], 201);
    } catch (Exception $e) {
        Flight::json(["success" => false, "message" => $e->getMessage()], 400);
    }
});


/**
 * @OA\Delete(
 *     path="/studentanswer/{id}",
 *     summary="Delete student answer by ID",
 *     tags={"StudentAnswers"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID of the student answer",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Answer deleted successfully",
 *         @OA\JsonContent(type="object",
 *             @OA\Property(property="success", type="boolean")
 *         )
 *     )
 * )
 */
Flight::route('DELETE /studentanswer/@id', function ($id) {
    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();
    $service = Flight::get('studentAnswerService');
    $success = $service->deleteAnswer($id);
    Flight::json(["success" => $success]);
});

/**
 * @OA\Get(
 *     path="/studentanswer/percentage/user/{user_id}",
 *     summary="Get correct answer percentage by user ID",
 *     tags={"StudentAnswers"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         description="User ID to calculate correct answer percentage",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Percentage of correct answers",
 *         @OA\JsonContent(type="object",
 *             @OA\Property(property="user_id", type="integer"),
 *             @OA\Property(property="percentage", type="number", format="float")
 *         )
 *     )
 * )
 */
Flight::route('GET /studentanswer/percentage/user/@user_id', function ($user_id) {
    $service = Flight::get('studentAnswerService');
    $percentage = $service->getCorrectPercentageByUserId($user_id);
    Flight::json([
        "user_id" => (int)$user_id,
        "percentage" => $percentage
    ]);
});

/**
 * @OA\Get(
 *     path="/studentanswer/user/{user_id}",
 *     summary="Get all student answers by user ID",
 *     tags={"StudentAnswers"},
 *     @OA\Parameter(
 *         name="user_id",
 *         in="path",
 *         description="User ID to retrieve answers for",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of student answers",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="answer_id", type="integer"),
 *                 @OA\Property(property="user_id", type="integer"),
 *                 @OA\Property(property="question_id", type="integer"),
 *                 @OA\Property(property="selected_option_id", type="integer"),
 *                 @OA\Property(property="is_correct", type="boolean")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User or answers not found"
 *     )
 * )
 */
Flight::route('GET /studentanswer/user/@user_id', function ($user_id) {
    $service = Flight::get('studentAnswerService');
    $answers = $service->getAnswersByUserId($user_id);
    Flight::json($answers);
});
