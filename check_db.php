<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = getDb();
    echo "DB connection ok\n";
    echo "Host: " . (getenv('DB_HOST') ?: getenv('DB_HOST')) . "\n";
    echo "DB_NAME: " . (getenv('DB_NAME') ?: getenv('DB_NAME')) . "\n";
    echo "DB_USER length: " . strlen(getenv('DB_USER') ?: getenv('DB_USERNAME') ?: '') . "\n";
    exit(0);
} catch (Throwable $e) {
    echo "DB connection error: " . $e->getMessage() . "\n";
    // Help debugging: show raw env var lengths so we can detect trailing whitespace
    $vars = ['DB_HOST','DB_PORT','DB_NAME','DB_USER','DB_USERNAME','DB_PASS','DB_PASSWORD'];
    foreach ($vars as $v) {
        $val = getenv($v);
        $len = $val === false ? 'not set' : strlen((string)$val);
        echo "$v length: $len\n";
    }
    exit(1);
}
