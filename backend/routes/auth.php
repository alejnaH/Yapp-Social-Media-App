<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../utils/JWTHelper.php';

/**
 * @OA\Post(
 *     path="/api/auth/login",
 *     tags={"authentication"},
 *     summary="User login",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username","password"},
 *             @OA\Property(property="username", type="string"),
 *             @OA\Property(property="password", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful login",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('POST /api/auth/login', function() use ($userService) {
    try {
        $data = Flight::request()->data->getData();
        if (empty($data['username']) || empty($data['password'])) {
            throw new Exception("Username and password are required");
        }
        
        $user = $userService->authenticate($data['username'], $data['password']);
        $token = JWTHelper::generateToken($user);
        
        Flight::json([
            'status' => 'success', 
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});
?>
