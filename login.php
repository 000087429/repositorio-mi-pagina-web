<?php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    setFlash('error', 'Completa tu correo y contraseña para continuar.');
    redirect('index.php');
}

$pdo = getDb();
$stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

$passwordMatches = false;
if ($user && $user['status'] === 'active') {
    $passwordMatches = password_verify($password, $user['password_hash']) || $user['password_hash'] === $password;
}

if ($user && $user['status'] === 'active' && $passwordMatches) {
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];

    if ($user['role'] === 'admin') {
        redirect('dashboard.php');
    }

    setFlash('success', 'Bienvenido de nuevo.');
    redirect('index.php');
}

setFlash('error', 'Credenciales inválidas. Intenta otra vez.');
redirect('index.php');
