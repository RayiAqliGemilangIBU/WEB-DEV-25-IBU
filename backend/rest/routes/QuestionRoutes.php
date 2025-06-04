<?php

require_once __DIR__ . '/../services/QuestionService.php';
require_once __DIR__ . '/../middleware/RoleMiddleware.php'; 


$questionService = new QuestionService();
/**
 * @OA\Get(
 *     path="/question",
 *     summary="Get all questions from all quizzes",
 *     tags={"Question"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="A list of all questions.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="questions",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="question_id", type="integer", example=1),
 *                     @OA\Property(property="quiz_id", type="integer", example=10),
 *                     @OA\Property(property="header", type="string", example="Is water an element?"),
 *                     @OA\Property(property="explanation", type="string", nullable=true, example="Water is a compound (H2O)."),
 *                     @OA\Property(property="answer", type="boolean", example=false)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=500, description="Internal Server Error")
 * )
 */
Flight::route('GET /question', function() use ($questionService) {
    // Flight::middleware('AuthMiddleware');
    try {
        $questions = $questionService->getAllQuestions();
        Flight::json(["success" => true, "questions" => $questions]);
    } catch (Exception $e) {
        Flight::halt(500, json_encode(["success" => false, "message" => $e->getMessage()]));
    }
});

// ==================== GET QUESTIONS BY QUIZ ID ====================

/**
 * @OA\Get(
 *     path="/question/quiz/{quiz_id}",
 *     summary="Get all questions for a specific quiz",
 *     tags={"Question"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="quiz_id",
 *         in="path",
 *         required=true,
 *         description="ID of the quiz to retrieve questions for",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="A list of questions for the specified quiz.",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(
 *                 property="questions",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="question_id", type="integer", example=1),
 *                     @OA\Property(property="quiz_id", type="integer", example=10),
 *                     @OA\Property(property="header", type="string", example="Is water an element?"),
 *                     @OA\Property(property="explanation", type="string", nullable=true, example="Water is a compound (H2O)."),
 *                     @OA\Property(property="answer", type="boolean", example=false)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid Quiz ID"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::route('GET /question/quiz/@quiz_id', function($quiz_id) use ($questionService) {
    // Flight::middleware('AuthMiddleware');
    try {
        $questions = $questionService->getQuestionsByQuizId((int)$quiz_id);
        Flight::json(["success" => true, "questions" => $questions]);
    } catch (Exception $e) {
        Flight::halt(400, json_encode(["success" => false, "message" => $e->getMessage()]));
    }
});

// ==================== ADD QUESTION ====================

/**
 * @OA\Post(
 *     path="/question",
 *     summary="Add a new True/False question to a quiz",
 *     tags={"Question"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"quiz_id", "header", "answer"},
 *             @OA\Property(property="quiz_id", type="integer", example=1),
 *             @OA\Property(property="header", type="string", example="The Earth is flat."),
 *             @OA\Property(property="explanation", type="string", nullable=true, example="The Earth is an oblate spheroid."),
 *             @OA\Property(property="answer", type="boolean", example=false)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Question added successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Question added successfully"),
 *             @OA\Property(property="question", type="object",
 *                 @OA\Property(property="question_id", type="integer", example=101),
 *                 @OA\Property(property="quiz_id", type="integer", example=1),
 *                 @OA\Property(property="header", type="string", example="The Earth is flat."),
 *                 @OA\Property(property="explanation", type="string", nullable=true, example="The Earth is an oblate spheroid."),
 *                 @OA\Property(property="answer", type="boolean", example=false)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::route('POST /question', function () use ($questionService) {
    Flight::middleware('AuthMiddleware');
    (new RoleMiddleware())->requireRole('Admin');

    $data = Flight::request()->data->getData();
    try {
        $newQuestion = $questionService->addQuestionToQuiz($data);
        Flight::json(["success" => true, "message" => "Question added successfully", "question" => $newQuestion], 201);
    } catch (Exception $e) {
        if (str_contains($e->getMessage(), "not found")) {
            Flight::halt(404, json_encode(["success" => false, "message" => $e->getMessage()]));
        } else {
            Flight::halt(400, json_encode(["success" => false, "message" => $e->getMessage()]));
        }
    }
});

// ==================== UPDATE QUESTION ====================

/**
 * @OA\Put(
 *     path="/question/{question_id}",
 *     summary="Update an existing True/False question",
 *     tags={"Question"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="question_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID of the question to update"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="header", type="string", example="Is the sky blue?"),
 *             @OA\Property(property="explanation", type="string", nullable=true, example="Due to Rayleigh scattering."),
 *             @OA\Property(property="answer", type="boolean", example=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Question updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Question updated successfully"),
 *             @OA\Property(property="question", type="object",
 *                 @OA\Property(property="question_id", type="integer", example=101),
 *                 @OA\Property(property="quiz_id", type="integer", example=1),
 *                 @OA\Property(property="header", type="string", example="Is the sky blue?"),
 *                 @OA\Property(property="explanation", type="string", nullable=true, example="Due to Rayleigh scattering."),
 *                 @OA\Property(property="answer", type="boolean", example=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Invalid input"),
 *     @OA\Response(response=404, description="Question not found"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::route('PUT /question/@question_id', function ($question_id) use ($questionService) {
    Flight::middleware('AuthMiddleware');
    (new RoleMiddleware())->requireRole('Admin');

    $data = Flight::request()->data->getData();
    try {
        $updatedQuestion = $questionService->updateQuestion((int)$question_id, $data);
        Flight::json(["success" => true, "message" => "Question updated successfully", "question" => $updatedQuestion]);
    } catch (Exception $e) {
        if (stripos($e->getMessage(), "not found") !== false) {
            Flight::halt(404, json_encode(["success" => false, "message" => "Question not found or update failed."]));
        } else {
            Flight::halt(400, json_encode(["success" => false, "message" => $e->getMessage()]));
        }
    }
});

// ==================== DELETE QUESTION ====================

/**
 * @OA\Delete(
 *     path="/question/{question_id}",
 *     summary="Delete a question",
 *     tags={"Question"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="question_id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID of the question to delete"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Question deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Question deleted successfully")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Question not found"),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 */
Flight::route('DELETE /question/@question_id', function ($question_id) use ($questionService) {
    Flight::middleware('AuthMiddleware');
    (new RoleMiddleware())->requireRole('Admin');

    try {
        $questionService->deleteQuestion((int)$question_id);
        Flight::json(["success" => true, "message" => "Question deleted successfully"]);
    } catch (Exception $e) {
        Flight::halt(404, json_encode(["success" => false, "message" => $e->getMessage()]));
    }
});