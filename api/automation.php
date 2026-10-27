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
    $stmt = $pdo->prepare('
        SELECT a.*, d.name AS action_device_name
        FROM automations a
        LEFT JOIN devices d ON d.id = a.action_device_id
        WHERE a.user_id = ?
        ORDER BY a.id DESC
    ');
    $stmt->execute([$userId]);
    json_response(['automations' => $stmt->fetchAll()]);
}

/* ------------------------- POST ------------------------- */
if ($method === 'POST') {
    $input  = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $input['action'] ?? '';

    if ($action === 'toggle') {
        $id      = (int)($input['id'] ?? 0);
        $enabled = (int)($input['enabled'] ?? 0);

        $stmt = $pdo->prepare('UPDATE automations SET enabled = ? WHERE id = ? AND user_id = ?');
        $stmt->execute([$enabled, $id, $userId]);

        $stmt = $pdo->prepare('SELECT name FROM automations WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $name = $stmt->fetchColumn();

        if ($name) {
            $pdo->prepare('INSERT INTO events (user_id, message, severity) VALUES (?, ?, ?)')
                ->execute([$userId, "Automation \"$name\" was " . ($enabled ? 'enabled' : 'paused'), 'info']);
        }

        json_response(['success' => true]);
    }

    if ($action === 'create') {
        $name         = trim($input['name'] ?? '');
        $triggerType  = trim($input['trigger_type'] ?? '');
        $triggerValue = trim($input['trigger_value'] ?? '');
        $deviceId     = (int)($input['action_device'] ?? 0);
        $actionValue  = trim($input['action_value'] ?? '');

        if ($name === '' || $triggerType === '' || $deviceId <= 0) {
            json_response(['error' => 'Missing required fields'], 400);
        }

        $allowedTriggers = ['time', 'temperature', 'motion', 'sunset'];
        if (!in_array($triggerType, $allowedTriggers, true)) {
            json_response(['error' => 'Invalid trigger type'], 400);
        }

        // Verify device belongs to user
        $stmt = $pdo->prepare('SELECT id FROM devices WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([$deviceId, $userId]);
        if (!$stmt->fetch()) {
            json_response(['error' => 'Device not found'], 404);
        }

        $stmt = $pdo->prepare('
            INSERT INTO automations
                (user_id, name, trigger_type, trigger_value, action_device_id, action_value)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $stmt->execute([$userId, $name, $triggerType, $triggerValue, $deviceId, $actionValue]);
        $id = (int)$pdo->lastInsertId();

        $pdo->prepare('INSERT INTO events (user_id, message, severity) VALUES (?, ?, ?)')
            ->execute([$userId, "Automation \"$name\" was created", 'info']);

        $stmt = $pdo->prepare('
            SELECT a.*, d.name AS action_device_name
            FROM automations a
            LEFT JOIN devices d ON d.id = a.action_device_id
            WHERE a.id = ?
        ');
        $stmt->execute([$id]);
        json_response(['automation' => $stmt->fetch()]);
    }

    if ($action === 'delete') {
        $id = (int)($input['id'] ?? 0);

        $stmt = $pdo->prepare('SELECT name FROM automations WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $name = $stmt->fetchColumn();

        if ($name) {
            $pdo->prepare('DELETE FROM automations WHERE id = ? AND user_id = ?')->execute([$id, $userId]);
            $pdo->prepare('INSERT INTO events (user_id, message, severity) VALUES (?, ?, ?)')
                ->execute([$userId, "Automation \"$name\" was deleted", 'warning']);
        }

        json_response(['success' => true]);
    }

    json_response(['error' => 'Invalid action'], 400);
}

json_response(['error' => 'Method not allowed'], 405);