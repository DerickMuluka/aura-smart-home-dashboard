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

$limit = 50;
if (isset($_GET['limit'])) {
    $limit = max(1, min(200, (int)$_GET['limit']));
}

$stmt = $pdo->prepare('SELECT * FROM events WHERE user_id = ? ORDER BY created_at DESC LIMIT ' . $limit);
$stmt->execute([$userId]);
json_response(['events' => $stmt->fetchAll()]);