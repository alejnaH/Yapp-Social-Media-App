<?php
class JWTHelper {
    private static $secret = 'jwt_secret_key';
    private static $algorithm = 'HS256';

    public static function generateToken($user) {
        $header = json_encode(['typ' => 'JWT', 'alg' => self::$algorithm]);
        $payload = json_encode([
            'iss' => 'community_platform',
            'aud' => 'community_platform',
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60), // 24 hours
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => isset($user['role']) ? $user['role'] : 'user'
        ]);

        $headerEncoded = self::base64UrlEncode($header);
        $payloadEncoded = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, self::$secret, true);
        $signatureEncoded = self::base64UrlEncode($signature);

        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }

    public static function validateToken($token) {
        try {
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return false;
            }

            $header = self::base64UrlDecode($parts[0]);
            $payload = self::base64UrlDecode($parts[1]);
            $signature = self::base64UrlDecode($parts[2]);

            $expectedSignature = hash_hmac('sha256', $parts[0] . "." . $parts[1], self::$secret, true);

            if (!hash_equals($signature, $expectedSignature)) {
                return false;
            }

            $payloadData = json_decode($payload, true);
            
            if ($payloadData['exp'] < time()) {
                return false; // Token expired
            }

            return $payloadData;
        } catch (Exception $e) {
            return false;
        }
    }

    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
?>