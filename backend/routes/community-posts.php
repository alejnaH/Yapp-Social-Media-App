<?php

/**
 * @OA\Get(
 *     path="/api/community-posts",
 *     tags={"community-posts"},
 *     summary="Get all community-post relationships",
 *     @OA\Response(
 *         response=200,
 *         description="List of community-post relationships",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /api/community-posts', function() use ($communityPostService) {
    try {
        $communityPosts = $communityPostService->getAll();
        Flight::json(['status' => 'success', 'data' => $communityPosts]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/community-posts/community/{communityId}",
 *     tags={"community-posts"},
 *     summary="Get posts in a community",
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
Flight::route('GET /api/community-posts/community/@communityId', function($communityId) use ($communityPostService) {
    try {
        $posts = $communityPostService->getByCommunityId($communityId);
        Flight::json(['status' => 'success', 'data' => $posts]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/community-posts/post/{postId}",
 *     tags={"community-posts"},
 *     summary="Get communities for a post",
 *     @OA\Parameter(
 *         name="postId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of communities for post",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/community-posts/post/@postId', function($postId) use ($communityPostService) {
    try {
        $communities = $communityPostService->getCommunitiesForPost($postId);
        Flight::json(['status' => 'success', 'data' => $communities]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Post(
 *     path="/api/community-posts",
 *     tags={"community-posts"},
 *     summary="Add post to community",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"postId","communityId"},
 *             @OA\Property(property="postId", type="integer"),
 *             @OA\Property(property="communityId", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post added to community successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('POST /api/community-posts', function() use ($communityPostService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $communityPostService->create($data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Delete(
 *     path="/api/community-posts/{id}",
 *     tags={"community-posts"},
 *     summary="Remove post from community",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Post removed from community successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('DELETE /api/community-posts/@id', function($id) use ($communityPostService) {
    try {
        $result = $communityPostService->delete($id);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});