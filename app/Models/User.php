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

}
