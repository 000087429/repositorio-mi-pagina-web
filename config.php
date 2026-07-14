<?php
session_start();

// Load .env file if present (simple loader for local development)
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }
        list($k, $v) = explode('=', $line, 2);
        $k = trim($k);
        $v = trim($v);
        if ($k !== '' && getenv($k) === false) {
            putenv("$k=$v");
            $_ENV[$k] = $v;
            $_SERVER[$k] = $v;
        }
    }
}

const APP_NAME = 'Pizzeria Trejo';

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function getCurrentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function getDb(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Trim env values to avoid accidental whitespace/newlines
    $host = trim((string) (getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost'));
    $port = trim((string) (getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306'));
    $dbName = trim((string) (getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'pizzeria_trejo'));
    // Support both DB_USER/DB_PASS and DB_USERNAME/DB_PASSWORD (Railway may provide DB_USERNAME)
    $user = trim((string) (getenv('DB_USER') ?: getenv('DB_USERNAME') ?: getenv('MYSQLUSER') ?: 'root'));
    $password = trim((string) (getenv('DB_PASS') ?: getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: ''));

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);

    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}
