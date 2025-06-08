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

Flight::route('GET /debug-env', function() {
    header('Access-Control-Allow-Origin: https://yapp-frontend-29kxs.ondigitalocean.app');
    try {
        Flight::json([
            'status' => 'success',
            'env_vars' => [
                'DB_HOST' => $_ENV['DB_HOST'] ?? 'NOT SET',
                'DB_NAME' => $_ENV['DB_NAME'] ?? 'NOT SET', 
                'DB_USER' => $_ENV['DB_USER'] ?? 'NOT SET',
                'DB_PORT' => $_ENV['DB_PORT'] ?? 'NOT SET',
                'DB_PASSWORD_SET' => !empty($_ENV['DB_PASSWORD']),
            ],
            'config_values' => [
                'DB_HOST' => Config::DB_HOST(),
                'DB_NAME' => Config::DB_NAME(),
                'DB_USER' => Config::DB_USER(),
                'DB_PORT' => Config::DB_PORT(),
                'PASSWORD_SET' => !empty(Config::DB_PASSWORD())
            ]
        ]);
    } catch (Exception $e) {
        Flight::json(['status' => 'error', 'message' => $e->getMessage()]);
    }
});

Flight::route('GET /debug-network', function() {
    header('Access-Control-Allow-Origin: https://yapp-frontend-29kxs.ondigitalocean.app');
    
    $host = Config::DB_HOST();
    $port = Config::DB_PORT();

    $connection = @fsockopen($host, $port, $errno, $errstr, 5);
    
    if ($connection) {
        fclose($connection);
        $network_status = 'SUCCESS - Can reach database host';
    } else {
        $network_status = "FAILED - Cannot reach $host:$port - Error: $errno $errstr";
    }
    
    Flight::json([
        'status' => 'network_test',
        'host' => $host,
        'port' => $port,
        'network_reachable' => $connection !== false,
        'network_status' => $network_status,
        'php_version' => PHP_VERSION,
        'pdo_drivers' => PDO::getAvailableDrivers()
    ]);
});

Flight::route('GET /debug-detailed-connection', function() {
    header('Access-Control-Allow-Origin: https://yapp-frontend-29kxs.ondigitalocean.app');
    
    $host = Config::DB_HOST();
    $dbName = Config::DB_NAME();
    $username = Config::DB_USER();
    $password = Config::DB_PASSWORD();
    $port = Config::DB_PORT();
    
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4";
        
        $start_time = microtime(true);
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $end_time = microtime(true);

        $stmt = $pdo->query("SELECT 1 as test");
        $result = $stmt->fetch();
        
        Flight::json([
            'status' => 'success',
            'message' => 'Database connection successful',
            'connection_time' => ($end_time - $start_time) . ' seconds',
            'test_query_result' => $result,
            'server_info' => $pdo->getAttribute(PDO::ATTR_SERVER_INFO),
            'dsn_used' => $dsn
        ]);
        
    } catch (PDOException $e) {
        Flight::json([
            'status' => 'error',
            'error_code' => $e->getCode(),
            'error_message' => $e->getMessage(),
            'dsn_attempted' => $dsn ?? 'DSN not created',
            'connection_details' => [
                'host' => $host,
                'port' => $port,
                'database' => $dbName,
                'username' => $username
            ]
        ]);
    }
});

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

Flight::route('GET /debug-db', function() {
    try {
        $connection = Database::connect();
        Flight::json(['status' => 'success', 'message' => 'Database connected successfully']);
    } catch (Exception $e) {
        Flight::json(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
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
