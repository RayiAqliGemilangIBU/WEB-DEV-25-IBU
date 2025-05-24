<?php
require_once __DIR__ . '/../services/AuthService.php';




/**
 * @OA\Post(
 *     path="/auth/login",
 *     summary="Login user",
 *     tags={"Authentication"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="your_password")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful login",
 *         @OA\JsonContent(
 *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGci..."),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="user@example.com")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid email or password"
 *     )
 * )
 */
Flight::route('POST /auth/login', function () {
    $data = Flight::request()->data->getData();

    $authService = new AuthService();
    $result = $authService->login($data['email'], $data['password']);

    if ($result) {
        Flight::json($result);
    } else {
        Flight::halt(401, 'Invalid email or password');
    }
});
