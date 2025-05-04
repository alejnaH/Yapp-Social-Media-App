<?php

/**
 * @OA\Get(
 *     path="/api/countries",
 *     tags={"countries"},
 *     summary="Get all countries with user count",
 *     @OA\Response(
 *         response=200,
 *         description="List of countries",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /api/countries', function() use ($countryService) {
    try {
        $countries = $countryService->getCountriesWithUserCount();
        Flight::json(['status' => 'success', 'data' => $countries]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/countries/{id}",
 *     tags={"countries"},
 *     summary="Get country by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Country details",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/countries/@id', function($id) use ($countryService) {
    try {
        $country = $countryService->getById($id);
        Flight::json(['status' => 'success', 'data' => $country]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Post(
 *     path="/api/countries",
 *     tags={"countries"},
 *     summary="Create a new country",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", minLength=2, maxLength=100)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Country created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('POST /api/countries', function() use ($countryService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $countryService->create($data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Put(
 *     path="/api/countries/{id}",
 *     tags={"countries"},
 *     summary="Update country by ID",
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
 *         description="Country updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('PUT /api/countries/@id', function($id) use ($countryService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $countryService->update($id, $data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Delete(
 *     path="/api/countries/{id}",
 *     tags={"countries"},
 *     summary="Delete country by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Country deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('DELETE /api/countries/@id', function($id) use ($countryService) {
    try {
        $result = $countryService->delete($id);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});