<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/QuizService.php';
require_once __DIR__ . '/../services/MaterialService.php';
require_once __DIR__ . '/../services/TextMaterialService.php';
require_once __DIR__ . '/../services/QuestionService.php';
require_once __DIR__ . '/../services/OptionItemService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/StudentAnswerService.php';
require_once __DIR__ . '/../util/JwtExtractor.php';
require __DIR__ . '/../../vendor/autoload.php'; // Untuk mengenali anotasi

$quizService = new QuizService();

/**
 * @OA\Get(
 *     path="/quiz",
 *     summary="Get all quizzes",
 *     tags={"Quizzes"},
 *     @OA\Response(
 *         response=200,
 *         description="List of quizzes",
 *         @OA\JsonContent(type="array", @OA\Items(type="object"))
 *     )
 * )
 */
Flight::route('GET /quiz', function () use ($quizService) {
    try {
        $quizzes = $quizService->getAllQuizzes();
        Flight::json(["success" => true, "data" => $quizzes]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/quiz/{id}",
 *     summary="Get a quiz by ID",
 *     tags={"Quizzes"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Quiz ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Quiz found",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(response=404, description="Quiz not found")
 * )
 */
Flight::route('GET /quiz/@id', function ($id) use ($quizService) {
    try {
        $quiz = $quizService->getQuizById($id);
        if ($quiz) {
            Flight::json(["success" => true, "data" => $quiz]);
        } else {
            Flight::halt(404, "Quiz not found");
        }
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/quiz",
 *     summary="Create a new quiz",
 *     tags={"Quizzes"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title", "description"},
 *             @OA\Property(property="title", type="string", example="Quiz Kimia"),
 *             @OA\Property(property="description", type="string", example="Deskripsi quiz kimia")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Quiz created",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="quiz_id", type="integer")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad Request")
 * )
 */
Flight::route('POST /quiz', function () use ($quizService) {
    $data = Flight::request()->data->getData();
    try {
        $created = $quizService->createQuiz($data);
        Flight::json(["success" => true, "message" => "Quiz created", "quiz_id" => $created['last_insert_id']]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *     path="/quiz/{quiz_id}",
 *     summary="Update a quiz by ID",
 *     tags={"Quizzes"},
 *     @OA\Parameter(
 *         name="quiz_id",
 *         in="path",
 *         required=true,
 *         description="Quiz ID to update",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", example="Judul baru"),
 *             @OA\Property(property="description", type="string", example="Deskripsi baru")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Quiz updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="quiz", type="object")
 *         )
 *     )
 * )
 */
Flight::route('PUT /quiz/@quiz_id', function ($quiz_id) use ($quizService) {
    $data = Flight::request()->data->getData();
    try {
        $updatedQuiz = $quizService->updateQuiz($quiz_id, $data);
        Flight::json(["success" => true, "message" => "Quiz updated", "quiz" => $updatedQuiz]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/quiz/{id}",
 *     summary="Delete a quiz by ID",
 *     tags={"Quizzes"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Quiz ID to delete",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Quiz deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=500, description="Server error")
 * )
 */
Flight::route('DELETE /quiz/@id', function ($id) use ($quizService) {
    try {
        $quizService->deleteQuiz($id);
        Flight::json(["success" => true, "message" => "Quiz and its dependencies deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});
