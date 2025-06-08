<?php
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

/**
 * @OA\Get(
 *     path="/api/posts",
 *     tags={"posts"},
 *     summary="Get all posts with user info",
 *     @OA\Response(
 *         response=200,
 *         description="List of posts",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /api/posts', function() use ($postService) {
    if (!AuthMiddleware::authenticate()) return;
    
    try {
        $user = AuthMiddleware::getCurrentUser();
        $posts = $postService->getPostsWithUserInfo($user['user_id']);
        Flight::json(['status' => 'success', 'data' => $posts]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/posts/{id}",
 *     tags={"posts"},
 *     summary="Get post by ID with details",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post details",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/posts/@id', function($id) use ($postService) {
    if (!AuthMiddleware::authenticate()) return;

    try {
        $post = $postService->getPostWithDetails($id);
        Flight::json(['status' => 'success', 'data' => $post]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/posts/user/{userId}",
 *     tags={"posts"},
 *     summary="Get posts by user ID",
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of posts by user",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/posts/user/@userId', function($userId) use ($postService) {
    if (!AuthMiddleware::authenticate()) return;

    try {
        $posts = $postService->getByUserId($userId);
        Flight::json(['status' => 'success', 'data' => $posts]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/posts/community/{communityId}",
 *     tags={"posts"},
 *     summary="Get posts by community ID",
 *     @OA\Parameter(
 *         name="communityId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of posts in community",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/posts/community/@communityId', function($communityId) use ($postService) {
    if (!AuthMiddleware::authenticate()) return;

    try {
        $posts = $postService->getPostsByCommunity($communityId);
        Flight::json(['status' => 'success', 'data' => $posts]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Post(
 *     path="/api/posts",
 *     tags={"posts"},
 *     summary="Create a new post",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"title","body","userId"},
 *             @OA\Property(property="title", type="string", maxLength=255),
 *             @OA\Property(property="body", type="string"),
 *             @OA\Property(property="userId", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('POST /api/posts', function() use ($postService) {
    if (!AuthMiddleware::authenticate()) return;

    try {
        $data = Flight::request()->data->getData();
        $result = $postService->create($data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Put(
 *     path="/api/posts/{id}",
 *     tags={"posts"},
 *     summary="Update post by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="title", type="string", maxLength=255),
 *             @OA\Property(property="body", type="string"),
 *             @OA\Property(property="userId", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('PUT /api/posts/@id', function($id) use ($postService) {
    if (!AuthMiddleware::authenticate()) return;

    try {
        $user = AuthMiddleware::getCurrentUser();
        $data = Flight::request()->data->getData();
        $result = $postService->updateWithAuth($id, $data, $user['user_id'], $user['role']);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Delete(
 *     path="/api/posts/{id}",
 *     tags={"posts"},
 *     summary="Delete post by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('DELETE /api/posts/@id', function($id) use ($postService) {
    if (!AuthMiddleware::authenticate()) return;

    try {
        $user = AuthMiddleware::getCurrentUser();
        $result = $postService->deleteWithAuth($id, $user['user_id'], $user['role']);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});