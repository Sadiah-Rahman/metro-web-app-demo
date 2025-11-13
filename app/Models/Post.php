<?php
namespace App\Models;

use PDO;

class Post {
    private static function connect(): PDO {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $db = getenv('DB_NAME') ?: 'authboard';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: '';
        $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }

    public static function create(int $user_id, string $content, ?string $image): int {
        $stmt = self::connect()->prepare('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $content, $image]);
        return (int)self::connect()->lastInsertId();
    }

    public static function all(): array {
        $stmt = self::connect()->query('
            SELECT posts.*, users.name, users.email 
            FROM posts 
            JOIN users ON users.id = posts.user_id
            ORDER BY posts.created_at DESC
        ');
        return $stmt->fetchAll();
    }
}

