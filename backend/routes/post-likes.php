<?php

/**
 * @OA\Get(
 *     path="/api/post-likes/post/{postId}",
 *     tags={"post-likes"},
 *     summary="Get likes for a post",
 *     @OA\Parameter(
 *         name="postId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of likes for post",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/post-likes/post/@postId', function($postId) use ($postLikeService) {
    try {
        $likes = $postLikeService->getByPostId($postId);
        Flight::json(['status' => 'success', 'data' => $likes]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/post-likes/user/{userId}",
 *     tags={"post-likes"},
 *     summary="Get posts liked by a user",
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of posts liked by user",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/post-likes/user/@userId', function($userId) use ($postLikeService) {
    try {
        $likes = $postLikeService->getByUserId($userId);
        Flight::json(['status' => 'success', 'data' => $likes]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/post-likes/count/{postId}",
 *     tags={"post-likes"},
 *     summary="Get like count for a post",
 *     @OA\Parameter(
 *         name="postId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Like count for post",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="count", type="integer")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/post-likes/count/@postId', function($postId) use ($postLikeService) {
    try {
        $count = $postLikeService->getLikeCount($postId);
        Flight::json(['status' => 'success', 'data' => ['count' => $count]]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Post(
 *     path="/api/post-likes",
 *     tags={"post-likes"},
 *     summary="Like a post",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"postId","userId"},
 *             @OA\Property(property="postId", type="integer"),
 *             @OA\Property(property="userId", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post liked successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('POST /api/post-likes', function() use ($postLikeService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $postLikeService->create($data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Delete(
 *     path="/api/post-likes/user/{userId}/post/{postId}",
 *     tags={"post-likes"},
 *     summary="Unlike a post",
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="postId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post unliked successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('DELETE /api/post-likes/user/@userId/post/@postId', function($userId, $postId) use ($postLikeService) {
    try {
        $result = $postLikeService->removeLike($userId, $postId);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});