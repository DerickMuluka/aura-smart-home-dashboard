<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $input['action'] ?? '';
$pdo = db();

if ($action === 'login') {
    $email    = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if ($email === '' || $password === '') {
        json_response(['error' => 'Email and password are required'], 400);
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        json_response(['error' => 'Invalid email or password'], 401);
    }

    $_SESSION['user_id']   = (int)$user['id'];
    $_SESSION['user_name'] = $user['name'];

    json_response(['redirect' => 'dashboard.php']);
}

if ($action === 'register') {
    $name     = trim($input['name'] ?? '');
    $email    = trim($input['email'] ?? '');
    $password = $input['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        json_response(['error' => 'All fields are required'], 400);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_response(['error' => 'Invalid email address'], 400);
    }
    if (strlen($password) < 6) {
        json_response(['error' => 'Password must be at least 6 characters'], 400);
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        json_response(['error' => 'This email is already registered'], 409);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $stmt->execute([$name, $email, $hash]);
    $userId = (int)$pdo->lastInsertId();

    // Seed default rooms
    foreach (['Living Room', 'Bedroom', 'Kitchen', 'Bathroom'] as $r) {
        $pdo->prepare('INSERT INTO rooms (user_id, name) VALUES (?, ?)')->execute([$userId, $r]);
    }

    // Log initial event
    $pdo->prepare('INSERT INTO events (user_id, message, severity) VALUES (?, ?, ?)')
        ->execute([$userId, 'Account created.', 'info']);

    // Do NOT auto-login. Redirect to login page.
    json_response([
        'redirect' => 'login.php?registered=1',
        'message'  => 'Account created. Please sign in.'
    ]);
}

json_response(['error' => 'Invalid action'], 400);