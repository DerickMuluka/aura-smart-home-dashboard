<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id'])) {
    json_response(['error' => 'Session expired. Please sign in again.'], 401);
}
$userId = (int)$_SESSION['user_id'];
$pdo    = db();
$method = $_SERVER['REQUEST_METHOD'];

/* ------------------------- GET ------------------------- */
if ($method === 'GET') {
    $stmt = $pdo->prepare('SELECT * FROM rooms WHERE user_id = ? ORDER BY id ASC');
    $stmt->execute([$userId]);
    json_response(['rooms' => $stmt->fetchAll()]);
}

/* ------------------------- POST ------------------------- */
if ($method === 'POST') {
    $input  = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? 'create';

    if ($action === 'create') {
        $name = trim($input['name'] ?? '');
        if ($name === '') {
            json_response(['error' => 'Room name is required'], 400);
        }

        $stmt = $pdo->prepare('INSERT INTO rooms (user_id, name) VALUES (?, ?)');
        $stmt->execute([$userId, $name]);
        $id = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare('SELECT * FROM rooms WHERE id = ?');
        $stmt->execute([$id]);
        json_response(['room' => $stmt->fetch()]);
    }

    if ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);
        $stmt = $pdo->prepare('DELETE FROM rooms WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        json_response(['success' => true]);
    }

    json_response(['error' => 'Invalid action'], 400);
}

json_response(['error' => 'Method not allowed'], 405);