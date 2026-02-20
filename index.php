<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/UrlController.php';

header("Content-Type: application/json");

$database = new Database();
$db = $database->getConnection();

$controller = new UrlController($db);

$requestMethod = $_SERVER["REQUEST_METHOD"];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/url';
if (str_starts_with($requestUri, $basePath)) {
    $requestUri = substr($requestUri, strlen($basePath));
}

$requestUri = rtrim($requestUri, '/');

// ===== RUTAS =====

// ✅ CREAR URL CORTA (POST)
if (
    $requestMethod === 'POST' &&
    ($requestUri === '/api/v1/urls' || $requestUri === '/index.php')
) {
    $controller->store();
    exit;
}

// ✅ REDIRECCIÓN POR SHORT CODE (GET)
if (
    $requestMethod === 'GET' &&
    preg_match('/^\/([a-zA-Z0-9]+)$/', $requestUri, $matches)
) {
    $shortCode = $matches[1];
    $controller->redirect($shortCode);
    exit;
}

// ===== 404 =====
http_response_code(404);
echo json_encode([
    "error" => "Ruta no encontrada",
    "method" => $requestMethod,
    "uri_detectada" => $requestUri,
    "uri_original" => $_SERVER['REQUEST_URI']
]);