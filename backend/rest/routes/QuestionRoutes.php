<?php
require_once __DIR__ . '/../services/QuestionService.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php';

$questionService = new QuestionService();

/**
 * @OA\Get(
 *     path="/question",
 *     summary="Get all questions",
 *     tags={"Questions"},
 *     @OA\Response(
 *         response=200,
 *         description="List of questions",
 *         @OA\JsonContent(type="array", @OA\Items(type="object"))
 *     )
 * )
 */
Flight::route('GET /question', function () use ($questionService) {
    try {
        $questions = $questionService->getAllQuestions();
        Flight::json(["success" => true, "data" => $questions]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Get(
 *     path="/question/{id}",
 *     summary="Get a question by ID",
 *     tags={"Questions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Question found"),
 *     @OA\Response(response=404, description="Question not found")
 * )
 */
Flight::route('GET /question/@id', function ($id) use ($questionService) {
    try {
        $question = $questionService->getQuestionById($id);
        if ($question) {
            Flight::json(["success" => true, "data" => $question]);
        } else {
            Flight::halt(404, "Question not found");
        }
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/question",
 *     summary="Create a new question",
 *     tags={"Questions"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quiz_id", "header"},
 *             @OA\Property(property="quiz_id", type="integer", example=1),
 *             @OA\Property(property="header", type="string", example="Apa rumus H2O?"),
 *             @OA\Property(property="explanation", type="string", example="Penjelasan tentang air")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Question created")
 * )
 */
Flight::route('POST /question', function () use ($questionService) {

    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    $data = Flight::request()->data->getData();
    try {
        $created = $questionService->createQuestion($data);
        Flight::json(["success" => true, "message" => "Question created", "question_id" => $created['last_insert_id']]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Put(
 *     path="/question/{id}",
 *     summary="Update a question by ID",
 *     tags={"Questions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="header", type="string", example="Apa lambang air?"),
 *             @OA\Property(property="explanation", type="string", example="Penjelasan baru")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Question updated")
 * )
 */
Flight::route('PUT /question/@id', function ($id) use ($questionService) {

    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();


    $data = Flight::request()->data->getData();
    try {
        $questionService->updateQuestion($data, $id);
        Flight::json(["success" => true, "message" => "Question updated"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/question/{id}",
 *     summary="Delete a question by ID",
 *     tags={"Questions"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(response=200, description="Question deleted")
 * )
 */
Flight::route('DELETE /question/@id', function ($id) use ($questionService) {

    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    try {
        $questionService->deleteQuestion($id);
        Flight::json(["success" => true, "message" => "Question deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});


/**
 * @OA\Get(
 *     path="/question/quiz/{quiz_id}",
 *     summary="Get all questions by quiz ID",
 *     tags={"Questions"},
 *     @OA\Parameter(
 *         name="quiz_id",
 *         in="path",
 *         required=true,
 *         description="ID of the quiz",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of questions for a specific quiz",
 *         @OA\JsonContent(type="array", @OA\Items(type="object"))
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid quiz ID"
 *     )
 * )
 */
Flight::route('GET /question/quiz/@quiz_id', function($quiz_id) use ($questionService) {
    try {
        $questions = $questionService->getQuestionsByQuizId($quiz_id);
        Flight::json(["success" => true, "data" => $questions]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

