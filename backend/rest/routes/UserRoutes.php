<?php

require_once __DIR__ . '/../services/MaterialService.php';
require_once __DIR__ . '/../services/TextMaterialService.php';

require_once __DIR__ . '/../services/QuizService.php';
require_once __DIR__ . '/../services/QuestionService.php';
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../services/StudentAnswerService.php';
require_once __DIR__ . '/../middleware/jwtMiddleware.php';



$userService = new UserService();

/**
 * @OA\Get(
 *     path="/user",
 *     summary="Get all users",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="List of all users",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /user', function () use ($userService) {
    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    $users = $userService->getAllUser();
    Flight::json(["success" => true, "data" => $users]);
});

/**
 * @OA\Get(
 *     path="/user/{id}",
 *     summary="Get user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User data",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="role", type="string")
 *         )
 *     ),
 *     @OA\Response(response=404, description="User not found")
 * )
 */
Flight::route('GET /user/@id', function ($id) use ($userService) {
    $user = $userService->getUserById($id);
    if ($user) {
        Flight::json(["success" => true, "data" => $user]);
    } else {
        Flight::halt(404, "User not found");
    }
});

/**
 * @OA\Get(
 *     path="/user/email/{email}",
 *     summary="Get user by email",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         description="User email",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User data",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer"),
 *             @OA\Property(property="email", type="string"),
 *             @OA\Property(property="role", type="string")
 *         )
 *     ),
 *     @OA\Response(response=404, description="User not found")
 * )
 */
Flight::route('GET /user/email/@email', function ($email) use ($userService) {
    $user = $userService->getUserByEmail($email);
    if ($user) {
        Flight::json(["success" => true, "data" => $user]);
    } else {
        Flight::halt(404, "User with email $email not found");
    }
});

/**
 * @OA\Put(
 *     path="/user/{id}",
 *     summary="Update user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="email", type="string", example="updated@example.com"),
 *             @OA\Property(property="password", type="string", example="newpassword123"),
 *             @OA\Property(property="role", type="string", example="student")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad Request")
 * )
 */
Flight::route('PUT /user/@id', function ($id) use ($userService) {


    $data = Flight::request()->data->getData();
    try {
        $userService->updateUser($id, $data);
        Flight::json(["success" => true, "message" => "User updated"]);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});

/**
 * @OA\Delete(
 *     path="/user/{id}",
 *     summary="Delete user by ID",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string")
 *         )
 *     ),
 *     @OA\Response(response=500, description="Internal Server Error")
 * )
 */
Flight::route('DELETE /user/@id', function ($id) use ($userService) {
    Flight::middleware();
    (RoleMiddleware::requireRole('Admin'))();

    try {
        $userService->deleteUser($id);
        Flight::json(["success" => true, "message" => "User deleted"]);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

/**
 * @OA\Post(
 *     path="/user/register",
 *     summary="Register a new user",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username", "email", "password", "role"},
 *             @OA\Property(property="name", type="string", example="john_doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="secret123"),
 *             @OA\Property(property="role", type="string", example="student")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="User registered"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="john_doe"),
 *                 @OA\Property(property="email", type="string", example="john@example.com"),
 *                 @OA\Property(property="role", type="string", example="student")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input or user already exists"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error"
 *     )
 * )
 */
Flight::route('POST /user/register', function () use ($userService) {
    try {
        $data = Flight::request()->data->getData();
        $newUser = $userService->registerUser($data);
        Flight::json([
            "success" => true,
            "message" => "User registered",
            "data" => $newUser
        ], 201);
    } catch (Exception $e) {
        Flight::halt(500, $e->getMessage());
    }
});

Flight::route('GET /users/@id', function ($id) {
    Flight::middleware(); // cek JWT
    $user = Flight::get('user');

    if ($user['sub'] != $id && $user['role'] != 'admin') {
        Flight::halt(403, "Access denied");
    }

    // lanjutkan akses user
});

/**
 * @OA\Get(
 * path="/students",
 * summary="Get all users with the role 'Student'",
 * description="Retrieves a list of all users who are registered as students. Password fields are omitted.",
 * tags={"Students"},
 * security={{"bearerAuth": {}}},
 * @OA\Response(
 * response=200,
 * description="A list of students",
 * @OA\JsonContent(
 * type="array",
 * @OA\Items(
 * @OA\Property(property="user_id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="John Doe"),
 * @OA\Property(property="email", type="string", example="john.doe@example.com"),
 * @OA\Property(property="address", type="string", example="123 Main St", nullable=true),
 * @OA\Property(property="phone_number", type="string", example="+0987654321", nullable=true),
 * @OA\Property(property="date_of_birth", type="string", format="date", example="1999-05-15", nullable=true),
 * @OA\Property(property="role", type="string", example="Student"),
 * @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-10 10:00:00")
 * )
 * )
 * ),
 * @OA\Response(
 * response=404, 
 * description="No students found",
 * @OA\JsonContent(
 * @OA\Property(property="message", type="string", example="No students found")
 * )
 * ),
 * @OA\Response(response=401, description="Unauthorized - JWT token is missing or invalid"),
 * @OA\Response(response=500, description="Internal Server Error")
 * )
 */
Flight::route('GET /students', function () use ($userService) {
    Flight::middleware();
    try {
        // Assuming "Student" is the exact string used for the role in your database
        $students = $userService->getUsersByRole("Student"); 
        
        if (empty($students)) {
            Flight::json(['message' => 'No students found'], 404);
        } else {
            Flight::json($students, 200);
        }
    } catch (Exception $e) {
        Flight::json(['error' => $e->getMessage()], 500);
    }
});

/**
 * @OA\Post(
 * path="/students",
 * summary="Add a new student",
 * description="Registers a new user with the 'Student' role. Only accessible by Admin.",
 * tags={"Students"},
 * security={{"bearerAuth": {}}},
 * @OA\RequestBody(
 * required=true,
 * @OA\JsonContent(
 * required={"name", "email", "password"},
 * @OA\Property(property="name", type="string", example="Jane Student"),
 * @OA\Property(property="email", type="string", example="jane.student@example.com"),
 * @OA\Property(property="password", type="string", example="studentpass123")
 * )
 * ),
 * @OA\Response(
 * response=201,
 * description="Student added successfully",
 * @OA\JsonContent(
 * @OA\Property(property="success", type="boolean", example=true),
 * @OA\Property(property="message", type="string", example="Student added successfully"),
 * @OA\Property(property="data", type="object",
 * @OA\Property(property="user_id", type="integer", example=2),
 * @OA\Property(property="name", type="string", example="Jane Student"),
 * @OA\Property(property="email", type="string", example="jane.student@example.com"),
 * @OA\Property(property="role", type="string", example="Student")
 * )
 * )
 * ),
 * @OA\Response(
 * response=400,
 * description="Invalid input or email already exists"
 * ),
 * @OA\Response(
 * response=401,
 * description="Unauthorized - JWT token is missing or invalid"
 * ),
 * @OA\Response(
 * response=403,
 * description="Forbidden - User does not have the required role (Admin)"
 * ),
 * @OA\Response(
 * response=500,
 * description="Internal Server Error"
 * )
 * )
 */
Flight::route('POST /students', function () use ($userService) {
    // Flight::middleware();
    // (RoleMiddleware::requireRole('Admin'))(); 

    try {
        $data = Flight::request()->data->getData();
        $newStudentId = $userService->addStudent($data); 
        $newStudent = $userService->getUserById($newStudentId); 
        
      
        unset($newStudent['password']);

        Flight::json([
            "success" => true,
            "message" => "Student added successfully",
            "data" => $newStudent
        ], 201);
    } catch (Exception $e) {
        Flight::halt(400, $e->getMessage());
    }
});