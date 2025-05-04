<?php

/**
 * @OA\Get(
 *     path="/api/communities",
 *     tags={"communities"},
 *     summary="Get all communities with post count",
 *     @OA\Response(
 *         response=200,
 *         description="List of communities",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /api/communities', function() use ($communityService) {
    try {
        $communities = $communityService->getCommunitiesWithPostCount();
        Flight::json(['status' => 'success', 'data' => $communities]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/communities/{id}",
 *     tags={"communities"},
 *     summary="Get community by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community details",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/communities/@id', function($id) use ($communityService) {
    try {
        $community = $communityService->getById($id);
        Flight::json(['status' => 'success', 'data' => $community]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Post(
 *     path="/api/communities",
 *     tags={"communities"},
 *     summary="Create a new community",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", minLength=2, maxLength=100)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('POST /api/communities', function() use ($communityService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $communityService->create($data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Put(
 *     path="/api/communities/{id}",
 *     tags={"communities"},
 *     summary="Update community by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", minLength=2, maxLength=100)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('PUT /api/communities/@id', function($id) use ($communityService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $communityService->update($id, $data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Delete(
 *     path="/api/communities/{id}",
 *     tags={"communities"},
 *     summary="Delete community by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Community deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('DELETE /api/communities/@id', function($id) use ($communityService) {
    try {
        $result = $communityService->delete($id);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});
