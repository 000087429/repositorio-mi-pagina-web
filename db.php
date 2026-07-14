<?php
/**
 * Simple DB connection helper using environment variables.
 * Usage: $pdo = require __DIR__ . '/db.php';
 * or: $pdo = dbConnect();
 */

function dbConnect(): PDO
{
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $dbName = getenv('DB_NAME') ?: 'pizzeria_trejo';
    // Accept either DB_USER/DB_PASS or DB_USERNAME/DB_PASSWORD (Railway uses DB_USERNAME/DB_PASSWORD)
    $user = getenv('DB_USER') ?: getenv('DB_USERNAME') ?: 'root';
    $pass = getenv('DB_PASS') ?: getenv('DB_PASSWORD') ?: '';

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);

    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        throw new RuntimeException('DB connection failed: ' . $e->getMessage());
    }
}

// Allow requiring the file directly: $pdo = require 'db.php';
return dbConnect();
