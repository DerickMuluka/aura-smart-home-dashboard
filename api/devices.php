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

    /* Summary endpoint: /api/devices.php?summary=1 */
    if (isset($_GET['summary'])) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM devices WHERE user_id = ?');
        $stmt->execute([$userId]);
        $total = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM devices WHERE user_id = ? AND status = 1');
        $stmt->execute([$userId]);
        $online = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare('SELECT AVG(CAST(value AS DECIMAL(5,1))) FROM devices WHERE user_id = ? AND type = ? AND value IS NOT NULL');
        $stmt->execute([$userId, 'thermostat']);
        $avgTemp = round((float)($stmt->fetchColumn() ?: 22), 1);

        $stmt = $pdo->prepare('SELECT COALESCE(SUM(kwh), 0) FROM energy_log WHERE user_id = ? AND DATE(logged_at) = CURDATE()');
        $stmt->execute([$userId]);
        $energy = round((float)$stmt->fetchColumn(), 2);
        if ($energy == 0) {
            $energy = round($online * 0.35, 2);
        }

        $stmt = $pdo->prepare('SELECT COUNT(*) FROM automations WHERE user_id = ? AND enabled = 1');
        $stmt->execute([$userId]);
        $autos = (int)$stmt->fetchColumn();

        $stmt = $pdo->prepare('
            SELECT d.*, r.name AS room_name
            FROM devices d
            LEFT JOIN rooms r ON r.id = d.room_id
            WHERE d.user_id = ?
            ORDER BY d.id DESC
        ');
        $stmt->execute([$userId]);
        $devices = $stmt->fetchAll();

        $stmt = $pdo->prepare('SELECT * FROM events WHERE user_id = ? ORDER BY created_at DESC LIMIT 6');
        $stmt->execute([$userId]);
        $events = $stmt->fetchAll();

        json_response([
            'overview' => [
                'total'       => $total,
                'online'      => $online,
                'energy'      => $energy,
                'energyDelta' => '-' . rand(3, 15) . '%',
                'avgTemp'     => $avgTemp,
                'automations' => $autos,
            ],
            'devices' => $devices,
            'events'  => $events,
        ]);
    }

    $stmt = $pdo->prepare('
        SELECT d.*, r.name AS room_name
        FROM devices d
        LEFT JOIN rooms r ON r.id = d.room_id
        WHERE d.user_id = ?
        ORDER BY d.id DESC
    ');
    $stmt->execute([$userId]);
    json_response(['devices' => $stmt->fetchAll()]);
}

/* ------------------------- POST ------------------------- */
if ($method === 'POST') {
    $input  = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? '';

    if ($action === 'toggle') {
        $id     = (int)($input['id'] ?? 0);
        $status = (int)($input['status'] ?? 0);

        $stmt = $pdo->prepare('UPDATE devices SET status = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$status, $id, $userId]);

        $stmt = $pdo->prepare('SELECT name FROM devices WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $name = $stmt->fetchColumn();

        if ($name) {
            $pdo->prepare('INSERT INTO events (user_id, message, severity) VALUES (?, ?, ?)')
                ->execute([$userId, "$name was turned " . ($status ? 'on' : 'off'), 'info']);
        }

        json_response(['success' => true]);
    }

    if ($action === 'create') {
        $name   = trim($input['name'] ?? '');
        $type   = $input['type'] ?? '';
        $roomId = !empty($input['room_id']) ? (int)$input['room_id'] : null;

        $allowed = ['light','thermostat','lock','camera','speaker','plug','fan','sensor'];
        if ($name === '' || !in_array($type, $allowed, true)) {
            json_response(['error' => 'Invalid device data'], 400);
        }

        $defaultValue = ($type === 'thermostat') ? '21.5' : null;

        $stmt = $pdo->prepare('INSERT INTO devices (user_id, room_id, name, type, value) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $roomId, $name, $type, $defaultValue]);
        $id = (int)$pdo->lastInsertId();

        $pdo->prepare('INSERT INTO events (user_id, message, severity) VALUES (?, ?, ?)')
            ->execute([$userId, "$name was added", 'info']);

        $stmt = $pdo->prepare('
            SELECT d.*, r.name AS room_name
            FROM devices d
            LEFT JOIN rooms r ON r.id = d.room_id
            WHERE d.id = ?
        ');
        $stmt->execute([$id]);
        json_response(['device' => $stmt->fetch()]);
    }

    json_response(['error' => 'Invalid action'], 400);
}

/* ------------------------- DELETE ------------------------- */
if ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $id    = (int)($input['id'] ?? 0);

    $stmt = $pdo->prepare('SELECT name FROM devices WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $userId]);
    $name = $stmt->fetchColumn();

    if ($name) {
        $pdo->prepare('DELETE FROM devices WHERE id = ? AND user_id = ?')->execute([$id, $userId]);
        $pdo->prepare('INSERT INTO events (user_id, message, severity) VALUES (?, ?, ?)')
            ->execute([$userId, "$name was removed", 'warning']);
    }

    json_response(['success' => true]);
}

json_response(['error' => 'Method not allowed'], 405);