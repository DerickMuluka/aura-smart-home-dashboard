<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // If this is an API/AJAX request, return JSON; otherwise redirect
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $xrw = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    $isAjax = str_contains($accept, 'application/json')
        || strtolower($xrw) === 'xmlhttprequest'
        || str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/');

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(401);
        echo json_encode(['error' => 'Session expired. Please sign in again.']);
        exit;
    }

    header('Location: login.php');
    exit;
}

function currentUser(): array {
    return [
        'id'   => (int)$_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? 'User',
    ];
}