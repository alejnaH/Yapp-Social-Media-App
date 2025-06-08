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
require_once __DIR__ . '/middleware/AuthMiddleware.php';
require_once __DIR__ . '/utils/JWTHelper.php';

Flight::before('start', function(&$params, &$output) {
    header('Access-Control-Allow-Origin: https://yapp-frontend-29kxs.ondigitalocean.app');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    
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

require_once __DIR__ . '/routes/auth.php';
require_once __DIR__ . '/routes/users.php';
require_once __DIR__ . '/routes/posts.php';
require_once __DIR__ . '/routes/comments.php';
require_once __DIR__ . '/routes/communities.php';
require_once __DIR__ . '/routes/countries.php';
require_once __DIR__ . '/routes/community-posts.php';
require_once __DIR__ . '/routes/post-likes.php';
require_once __DIR__ . '/routes/docs.php';

Flight::start();
?>
