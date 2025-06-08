<?php
class Database {
    private static $connection = null;

    public static function connect() {
        if (self::$connection === null) {
            try {
                $host = Config::DB_HOST();
                $dbName = Config::DB_NAME();
                $username = Config::DB_USER();
                $password = Config::DB_PASSWORD();
                
                self::$connection = new PDO(
                    "mysql:host=" . $host . ";dbname=" . $dbName,
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}

class Config {
    public static function DB_NAME() {
        return Config::get_env("DB_NAME", "yapp");
    }

    public static function DB_PORT() {
        return Config::get_env("DB_PORT", 3306);
    }

    public static function DB_USER() {
        return Config::get_env("DB_USER", "root");
    }

    public static function DB_PASSWORD() {
        return Config::get_env("DB_PASSWORD", "");
    }

    public static function DB_HOST() {
        return Config::get_env("DB_HOST", "localhost");
    }

    public static function JWT_SECRET() {
        return Config::get_env("JWT_SECRET", "jwt_secret_key");
    }

    public static function get_env($name, $default) {
        return isset($_ENV[$name]) && trim($_ENV[$name]) != "" ? $_ENV[$name] : $default;
    }
}
