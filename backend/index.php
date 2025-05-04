<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use OpenApi\Annotations as OA;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/PostService.php';
require_once __DIR__ . '/services/CommentService.php';
require_once __DIR__ . '/services/CommunityService.php';
require_once __DIR__ . '/services/CountryService.php';
require_once __DIR__ . '/services/CommunityPostService.php';
require_once __DIR__ . '/services/PostLikeService.php';

Flight::before('start', function(&$params, &$output) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if (Flight::request()->method == 'OPTIONS') {
        exit();
    }
});

Flight::map('error', function(Exception $ex) {
    Flight::json([
        'status' => 'error',
        'message' => $ex->getMessage()
    ], 400);
});

$userService = new UserService();
$postService = new PostService();
$commentService = new CommentService();
$communityService = new CommunityService();
$countryService = new CountryService();
$communityPostService = new CommunityPostService();
$postLikeService = new PostLikeService();

/**
 * @OA\Get(
 *     path="/api/users",
 *     tags={"users"},
 *     summary="Get all users with country info",
 *     @OA\Response(
 *         response=200,
 *         description="List of users",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
 *         )
 *     )
 * )
 */
Flight::route('GET /api/users', function() use ($userService) {
    try {
        $users = $userService->getAllUsersWithCountry();
        Flight::json(['status' => 'success', 'data' => $users]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     tags={"users"},
 *     summary="Get user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User details",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('GET /api/users/@id', function($id) use ($userService) {
    try {
        $user = $userService->getUserWithCountry($id);
        Flight::json(['status' => 'success', 'data' => $user]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Post(
 *     path="/api/users",
 *     tags={"users"},
 *     summary="Create a new user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","username","password"},
 *             @OA\Property(property="name", type="string", minLength=2, maxLength=100),
 *             @OA\Property(property="username", type="string", minLength=3, maxLength=100),
 *             @OA\Property(property="password", type="string", minLength=6),
 *             @OA\Property(property="countryId", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('POST /api/users', function() use ($userService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $userService->create($data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Put(
 *     path="/api/users/{id}",
 *     tags={"users"},
 *     summary="Update user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", minLength=2, maxLength=100),
 *             @OA\Property(property="username", type="string", minLength=3, maxLength=100),
 *             @OA\Property(property="password", type="string", minLength=6),
 *             @OA\Property(property="countryId", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('PUT /api/users/@id', function($id) use ($userService) {
    try {
        $data = Flight::request()->data->getData();
        $result = $userService->update($id, $data);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

/**
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     tags={"users"},
 *     summary="Delete user by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="data", type="object")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Bad request")
 * )
 */
Flight::route('DELETE /api/users/@id', function($id) use ($userService) {
    try {
        $result = $userService->delete($id);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

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
        Flight::json(['status' => 'success', 'data' => $user]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

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
    try {
        $posts = $postService->getPostsWithUserInfo();
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
    try {
        $data = Flight::request()->data->getData();
        $result = $postService->update($id, $data);
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
    try {
        $result = $postService->delete($id);
        Flight::json(['status' => 'success', 'data' => $result]);
    } catch (Exception $e) {
        Flight::error($e);
    }
});

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

Flight::route('GET /public/v1/docs', function() {
    include __DIR__ . '/public/v1/docs/index.php';
});

Flight::route('GET /public/v1/docs/swagger.php', function() {
    include __DIR__ . '/public/v1/docs/swagger.php';
});

Flight::route('GET /public/v1/docs/@file', function($file) {
    $filePath = __DIR__ . '/public/v1/docs/' . $file;
    if (file_exists($filePath)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'css':
                header('Content-Type: text/css');
                break;
            case 'js':
                header('Content-Type: application/javascript');
                break;
            case 'png':
                header('Content-Type: image/png');
                break;
        }
        readfile($filePath);
    }
});

Flight::start();
?>
