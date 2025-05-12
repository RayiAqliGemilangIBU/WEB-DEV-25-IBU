<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/services/MaterialService.php';
require_once __DIR__ . '/services/TextMaterialService.php';
require_once __DIR__ . '/util/JwtExtractor.php';
require_once __DIR__ . '/services/QuizService.php';
require_once __DIR__ . '/services/QuestionService.php';
require_once __DIR__ . '/services/OptionItemService.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/StudentAnswerService.php';
require __DIR__ . '/../../vendor/autoload.php'; // Penting agar anotasi dikenali






// Inisialisasi service
$materialService = new MaterialService();


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


// ----------- text Material
$textMaterialService = new TextMaterialService();


/**
 * POST /textmaterials
 * Membuat TextMaterial baru
 * Data JSON yang dikirim: { "material_id": 1, "title": "Judul", "content": "Isi konten", "image_path": "path/gambar.jpg" }
 */
Flight::route('POST /textmaterials', function() use ($textMaterialService) {
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
// Flight::route('PUT /textmaterials/@text_id', function($text_id) use ($textMaterialService) {
//     $data = Flight::request()->data->getData();

//     // Ambil header Authorization
//     $headers = getallheaders();
//     $jwt = isset($headers['Authorization']) ? $headers['Authorization'] : null;

//     try {
//         $updated = $textMaterialService->updateTextMaterial($jwt, $text_id, $data);
//         Flight::json(["success" => true, "message" => "TextMaterial updated", "updated" => $updated]);
//     } catch (Exception $e) {
//         Flight::json(["success" => false, "message" => $e->getMessage()], 400);
//     }
// });
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


// ------------------------------------- Quiz Section

$quizService = new QuizService();


// Mengambil semua quizzes
Flight::route('GET /quiz', function () {
    $service = new QuizService();
    try {
        $quizzes = $service->getAllQuizzes();
        Flight::json($quizzes);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// Mengambil quiz berdasarkan ID
Flight::route('GET /quiz/@id', function ($id) {
    $service = new QuizService();
    try {
        $quiz = $service->getQuizById($id);
        if ($quiz) {
            Flight::json($quiz);
        } else {
            Flight::halt(404, "Quiz not found");
        }
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// Membuat quiz baru
Flight::route('POST /quiz', function () {
    $data = Flight::request()->data->getData(); // Mendapatkan data yang dikirimkan dalam permintaan
    error_log(print_r($data, true)); // Logging data untuk debugging
    
    $service = new QuizService(); // Membuat objek QuizService
    try {
        // Menggunakan QuizService untuk membuat quiz
        $created = $service->createQuiz($data);
        // Mengirimkan respons JSON jika berhasil
        Flight::json(["message" => "Quiz created", "quiz_id" => $created['last_insert_id']]);
    } catch (Exception $e) {
        // Menangani error jika ada masalah
        Flight::halt(500, $e->getMessage());
    }
});


// Mengupdate quiz berdasarkan ID
Flight::route('PUT /quiz/@quiz_id', function ($quiz_id) {
    $data = Flight::request()->data->getData();
    $service = new QuizService();
    try {
        $updatedQuiz = $service->updateQuiz($quiz_id, $data);
        Flight::json(["message" => "Quiz updated", "quiz" => $updatedQuiz]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// Menghapus quiz berdasarkan ID
Flight::route('DELETE /quiz/@id', function ($id) {
    $service = new QuizService();
    try {
        $service->deleteQuiz($id);
        Flight::json(["message" => "Quiz and its dependencies deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});
// ------------------------------- Question

$questionService = new QuestionService(); // Inisialisasi service di luar route

// Mengambil semua pertanyaan
Flight::route('GET /question', function () use ($questionService) {
    try {
        $questions = $questionService->getAllQuestions();
        Flight::json($questions);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// Mengambil pertanyaan berdasarkan ID
Flight::route('GET /question/@id', function ($id) use ($questionService) {
    try {
        $question = $questionService->getQuestionById($id);
        if ($question) {
            Flight::json($question);
        } else {
            Flight::halt(404, "Question not found");
        }
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// Membuat pertanyaan baru
Flight::route('POST /question', function () use ($questionService) {
    $data = Flight::request()->data->getData(); // Ambil data dari body permintaan
    error_log(print_r($data, true)); // Log untuk debugging

    try {
        $created = $questionService->createQuestion($data); // Simpan data via service
        Flight::json(["message" => "Question created", "question_id" => $created['last_insert_id']]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// Mengupdate pertanyaan berdasarkan ID
Flight::route('PUT /question/@id', function ($id) use ($questionService) {
    $data = Flight::request()->data->getData(); // Ambil data dari request
    try {
        $questionService->updateQuestion($data, $id); // Update via service
        Flight::json(["message" => "Question updated"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// Menghapus pertanyaan berdasarkan ID
Flight::route('DELETE /question/@id', function ($id) use ($questionService) {
    try {
        $questionService->deleteQuestion($id);
        Flight::json(["message" => "Question deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// -------------------------------Optionitem

$optionService = new OptionItemService();

// GET all option items
Flight::route('GET /optionitem', function () use ($optionService) {
    Flight::json($optionService->getAllOptionItems());
});

// GET option item by ID
Flight::route('GET /optionitem/@id', function ($id) use ($optionService) {
    $option = $optionService->getOptionItemById($id);
    if ($option) {
        Flight::json($option);
    } else {
        Flight::halt(404, "Option item not found");
    }
});

// POST create option item
Flight::route('POST /optionitem', function () use ($optionService) {
    $data = Flight::request()->data->getData();
    try {
        $created = $optionService->createOptionItem($data);
        Flight::json(["message" => "Option item created", "option_id" => $created['last_insert_id']]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

// PUT update option item
Flight::route('PUT /optionitem/@id', function ($id) use ($optionService) {
    $data = Flight::request()->data->getData();
    try {
        $optionService->updateOptionItem($id, $data);
        Flight::json(["message" => "Option item updated"]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

// DELETE option item
Flight::route('DELETE /optionitem/@id', function ($id) use ($optionService) {
    try {
        $optionService->deleteOptionItem($id);
        Flight::json(["message" => "Option item deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

// GET options by question_id
Flight::route('GET /optionitem/question/@question_id', function ($question_id) use ($optionService) {
    Flight::json($optionService->getOptionsByQuestionId($question_id));
});

// GET check option content by question_id
Flight::route('GET /optionitem/check/@question_id/@content', function ($question_id, $content) use ($optionService) {
    $result = $optionService->checkOptionContent($question_id, $content);
    if ($result) {
        Flight::json($result);
    } else {
        Flight::halt(404, "Option content not found for this question");
    }
});





// -------------------------------User

$userService = new UserService();

// GET all users
Flight::route('GET /user', function () use ($userService) {
    Flight::json($userService->getAllUser());
});

// GET user by ID
Flight::route('GET /user/@id', function ($id) use ($userService) {
    $user = $userService->getUserById($id);
    if ($user) {
        Flight::json($user);
    } else {
        Flight::halt(404, "User not found");
    }
});

// GET user by email
Flight::route('GET /user/email/@email', function ($email) use ($userService) {
    $user = $userService->getUserByEmail($email);
    if ($user) {
        Flight::json($user);
    } else {
        Flight::halt(404, "User with email $email not found");
    }
});

// PUT update user
Flight::route('PUT /user/@id', function ($id) use ($userService) {
    $data = Flight::request()->data->getData();
    try {
        $userService->updateUser($id, $data);
        Flight::json(["message" => "User updated"]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

// DELETE user
Flight::route('DELETE /user/@id', function ($id) use ($userService) {
    try {
        $userService->deleteUser($id);
        Flight::json(["message" => "User deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});


// Inisialisasi layanan
$studentAnswerService = new StudentAnswerService();

// ------------------------------- Student Answer Routes


// ------------------------------- Student Answer

// GET all student answers
Flight::route('GET /studentAnswer', function () use ($studentAnswerService) {
    Flight::json($studentAnswerService->getAllAnswers());
});

// GET student answer by ID
Flight::route('GET /studentAnswer/@id', function ($id) use ($studentAnswerService) {
    $answer = $studentAnswerService->getAnswerById($id);
    if ($answer) {
        Flight::json($answer);
    } else {
        Flight::halt(404, "Answer not found");
    }
});

// GET all student answers by student ID
Flight::route('GET /studentAnswer/student/@user_id', function ($user_id) use ($studentAnswerService) {
    $answers = $studentAnswerService->getAnswersByStudentId($user_id);
    Flight::json($answers);
});

// GET correct answer percentage by user_id
Flight::route('GET /studentAnswer/correctPercentage/@user_id', function ($user_id) use ($studentAnswerService) {
    try {
        $percentage = $studentAnswerService->calculateCorrectAnswerPercentage($user_id);
        Flight::json(["correct_answer_percentage" => $percentage]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

// GET correct answer percentage by email
Flight::route('GET /studentAnswer/correctPercentage/email/@email', function ($email) use ($studentAnswerService, $userService) {
    // Get user_id based on email
    $user = $userService->getUserByEmail($email);

    if (!$user) {
        Flight::halt(404, "User with email $email not found");
    }

    try {
        $percentage = $studentAnswerService->calculateCorrectAnswerPercentage($user['user_id']);
        Flight::json(["correct_answer_percentage" => $percentage]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

// POST create student answer
Flight::route('POST /studentAnswer', function () use ($studentAnswerService) {
    $data = Flight::request()->data->getData();

    try {
        $studentAnswerService->createAnswer($data);
        Flight::json(["message" => "Answer created successfully"]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

// PUT update student answer
Flight::route('PUT /studentAnswer/@id', function ($id) use ($studentAnswerService) {
    $data = Flight::request()->data->getData();

    try {
        $studentAnswerService->updateAnswer($data, $id);
        Flight::json(["message" => "Answer updated"]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

// DELETE student answer by ID
Flight::route('DELETE /studentAnswer/@id', function ($id) use ($studentAnswerService) {
    try {
        $studentAnswerService->deleteAnswer($id);
        Flight::json(["message" => "Answer deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});
Flight::start();
