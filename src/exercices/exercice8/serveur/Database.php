<?php
class Database {
    private static $host = "localhost";
    private static $dbname = "hockey_stats";
    private static $username = "root"; 
    private static $password = "emf123";
    private static $connection;

    public static function getConnection() {
        if (!self::$connection) {
            try {
                self::$connection = new PDO("mysql:host=" . self::$host . ";dbname=" . self::$dbname, self::$username, self::$password);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
}
