<?php
require_once __DIR__ . '/../utils/JWTHelper.php';

class AuthMiddleware {
    public static function authenticate() {
        $headers = getallheaders();
        $token = null;
        
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
        }
        
        if (!$token) {
            Flight::json(['status' => 'error', 'message' => 'Token required'], 401);
            return false;
        }
        
        $decoded = JWTHelper::validateToken($token);
        if (!$decoded) {
            Flight::json(['status' => 'error', 'message' => 'Invalid token'], 401);
            return false;
        }
        
        Flight::set('user', $decoded);
        return true;
    }
    
    public static function requireAdmin() {
        if (!self::authenticate()) return false;
        
        $user = Flight::get('user');
        if ($user['role'] !== 'admin') {
            Flight::json(['status' => 'error', 'message' => 'Admin access required'], 403);
            return false;
        }
        
        return true;
    }
}
?>
