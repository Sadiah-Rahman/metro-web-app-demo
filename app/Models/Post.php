<?php
namespace App\Models;

use PDO;
use DateTime;

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
        $pdo = self::connect();
        $stmt = $pdo->prepare('INSERT INTO posts (user_id, content, image) VALUES (?, ?, ?)');
        $stmt->execute([$user_id, $content, $image]);
        return (int)$pdo->lastInsertId();
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

    public static function find(int $id): ?array {
        $stmt = self::connect()->prepare('SELECT posts.*, users.name, users.email FROM posts JOIN users ON users.id = posts.user_id WHERE posts.id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function update(int $id, string $content, ?string $image = null): bool {
        $pdo = self::connect();
        if ($image !== null) {
            $stmt = $pdo->prepare('UPDATE posts SET content = ?, image = ?, edited_at = NOW() WHERE id = ?');
            return $stmt->execute([$content, $image, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE posts SET content = ?, edited_at = NOW() WHERE id = ?');
            return $stmt->execute([$content, $id]);
        }
    }

    public static function delete(int $id): bool {
        $stmt = self::connect()->prepare('DELETE FROM posts WHERE id = ?');
        return $stmt->execute([$id]);
    }
    public static function searchByKeyword(string $term, int $limit = 50): array {
        $pdo = self::connect();
        // Match against posts.content and users.name (joined)
        $q = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';
        $stmt = $pdo->prepare('
        SELECT posts.*, users.name, users.email
        FROM posts
        JOIN users ON users.id = posts.user_id
        WHERE posts.content LIKE ? OR users.name LIKE ?
        ORDER BY posts.created_at DESC
        LIMIT ?
    ');
        $stmt->bindValue(1, $q, PDO::PARAM_STR);
        $stmt->bindValue(2, $q, PDO::PARAM_STR);
        $stmt->bindValue(3, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
