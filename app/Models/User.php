<?php
namespace App\Models;

use PDO;

class User {
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

    public static function findByEmail(string $email): ?array {
        $stmt = self::connect()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(string $name, string $email, string $password): int {
        $stmt = self::connect()->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, $password]);
        return (int)self::connect()->lastInsertId();
    }
    public static function findById(int $id): ?array {
        $stmt = self::connect()->prepare('SELECT id, name, email, created_at FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function postsByUser(int $user_id, int $limit = 100): array {
        $pdo = self::connect();
        $stmt = $pdo->prepare('
        SELECT posts.*, users.name, users.email
        FROM posts
        JOIN users ON users.id = posts.user_id
        WHERE posts.user_id = ?
        ORDER BY posts.created_at DESC
        LIMIT ?
    ');
        $stmt->bindValue(1, $user_id, \PDO::PARAM_INT);
        $stmt->bindValue(2, (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function followerCount(int $user_id): int {
        $pdo = self::connect();
        $stmt = $pdo->prepare('SELECT COUNT(*) as cnt FROM followers WHERE followed_id = ?');
        $stmt->execute([$user_id]);
        $row = $stmt->fetch();
        return (int)($row['cnt'] ?? 0);
    }

    public static function isFollowing(int $follower_id, int $followed_id): bool {
        if ($follower_id === 0) return false;
        $pdo = self::connect();
        $stmt = $pdo->prepare('SELECT 1 FROM followers WHERE follower_id = ? AND followed_id = ? LIMIT 1');
        $stmt->execute([$follower_id, $followed_id]);
        return (bool)$stmt->fetch();
    }

    public static function follow(int $follower_id, int $followed_id): bool {
        if ($follower_id === $followed_id) return false;
        $pdo = self::connect();
        try {
            $stmt = $pdo->prepare('INSERT IGNORE INTO followers (follower_id, followed_id) VALUES (?, ?)');
            return (bool)$stmt->execute([$follower_id, $followed_id]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public static function unfollow(int $follower_id, int $followed_id): bool {
        $pdo = self::connect();
        $stmt = $pdo->prepare('DELETE FROM followers WHERE follower_id = ? AND followed_id = ?');
        return (bool)$stmt->execute([$follower_id, $followed_id]);
    }

    public static function searchByName(string $term, int $limit = 50): array {
        $pdo = self::connect();
        // Use wildcard search, prepared statement to avoid injection
        $q = '%' . str_replace(['%', '_'], ['\%', '\_'], $term) . '%';
        $stmt = $pdo->prepare('SELECT id, name, email, created_at FROM users WHERE name LIKE ? ORDER BY name LIMIT ?');
        $stmt->bindValue(1, $q, PDO::PARAM_STR);
        $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function updateProfile(int $id, string $name, string $email, ?string $bio = null, ?string $avatar = null): bool {
        $pdo = self::connect();

        if ($avatar !== null) {
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, bio = ?, avatar = ? WHERE id = ?');
            return (bool)$stmt->execute([$name, $email, $bio, $avatar, $id]);
        } else {
            $stmt = $pdo->prepare('UPDATE users SET name = ?, email = ?, bio = ? WHERE id = ?');
            return (bool)$stmt->execute([$name, $email, $bio, $id]);
        }
    }
}
