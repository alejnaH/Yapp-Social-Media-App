<?php

/**
 * @OA\Get(
 *     path="/api/comments",
 *     tags={"comments"},
 *     summary="Get all comments",
 *     @OA\Response(
 *         response=200,
 *         description="List of comments",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /api/comments', function() use ($commentService) {
    try {
        $comments = $commentService->getAll();
        Flight::json(['status' => 'success', 'data' => $comments]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/comments/{id}",
 *     tags={"comments"},
 *     summary="Get comment by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment details",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/comments/@id', function($id) use ($commentService) {
    try {
        $comment = $commentService->getById($id);
        Flight::json(['status' => 'success', 'data' => $comment]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/comments/post/{postId}",
 *     tags={"comments"},
 *     summary="Get comments by post ID with user info",
 *     @OA\Parameter(
 *         name="postId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of comments for post",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/comments/post/@postId', function($postId) use ($commentService) {
    try {
        $comments = $commentService->getCommentsWithUserInfo($postId);
        Flight::json(['status' => 'success', 'data' => $comments]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/comments/user/{userId}",
 *     tags={"comments"},
 *     summary="Get comments by user ID",
 *     @OA\Parameter(
 *         name="userId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of comments by user",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/comments/user/@userId', function($userId) use ($commentService) {
    try {
        $comments = $commentService->getByUserId($userId);
        Flight::json(['status' => 'success', 'data' => $comments]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Post(
 *     path="/api/comments",
 *     tags={"comments"},
 *     summary="Create a new comment",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"body","postId","userId"},
 *             @OA\Property(property="body", type="string", maxLength=1000),
 *             @OA\Property(property="postId", type="integer"),
 *             @OA\Property(property="userId", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('POST /api/comments', function() use ($commentService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $commentService->create($data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Put(
 *     path="/api/comments/{id}",
 *     tags={"comments"},
 *     summary="Update comment by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="body", type="string", maxLength=1000),
 *             @OA\Property(property="postId", type="integer"),
 *             @OA\Property(property="userId", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('PUT /api/comments/@id', function($id) use ($commentService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $commentService->update($id, $data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Delete(
 *     path="/api/comments/{id}",
 *     tags={"comments"},
 *     summary="Delete comment by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('DELETE /api/comments/@id', function($id) use ($commentService) {
    try {
        $result = $commentService->delete($id);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});