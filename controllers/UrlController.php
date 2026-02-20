<?php

require_once __DIR__ . '/../models/Url.php';
require_once __DIR__ . '/../models/Visit.php';

class UrlController {

    private $urlModel;
    private $visitModel;

    public function __construct($db) {
        $this->urlModel = new Url($db);
        $this->visitModel = new Visit($db);
    }

    // 🔹 GENERAR CÓDIGO CORTO
    private function generateShortCode($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $code;
    }

    // 🔹 CREAR URL CORTA
    public function store() {

        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['original_url'])) {
            http_response_code(400);
            echo json_encode(["error" => "original_url es requerido"]);
            return;
        }

        if (!filter_var($data['original_url'], FILTER_VALIDATE_URL)) {
            http_response_code(400);
            echo json_encode(["error" => "URL inválida"]);
            return;
        }

        // Generar código único
        do {
            $shortCode = $this->generateShortCode();
            $exists = $this->urlModel->findByCode($shortCode);
        } while ($exists);

        $urlData = [
            "original_url" => $data['original_url'],
            "short_code"   => $shortCode,
            "expires_at"   => $data['expires_at'] ?? null,
            "max_uses"     => $data['max_uses'] ?? null,
            "creator_ip"   => $_SERVER['REMOTE_ADDR']
        ];

        $this->urlModel->create($urlData);

        http_response_code(201);

        echo json_encode([
            "short_code" => $shortCode,
            "short_url"  => "http://localhost:8080/url/" . $shortCode
        ]);
    }

    // 🔹 REDIRECCIONAR URL CORTA
    public function redirect($shortCode)
    {
        $url = $this->urlModel->findByCode($shortCode);

        if (!$url) {
            http_response_code(404);
            echo json_encode(["error" => "URL no encontrada"]);
            return;
        }

        // 🔹 Verificar expiración (si existe)
        if (!empty($url['expires_at']) && strtotime($url['expires_at']) < time()) {
            http_response_code(410);
            echo json_encode(["error" => "URL expirada"]);
            return;
        }

        // 🔹 Verificar límite de usos (si existe)
        if (!empty($url['max_uses']) && $url['uses'] >= $url['max_uses']) {
            http_response_code(410);
            echo json_encode(["error" => "Límite de usos alcanzado"]);
            return;
        }

        // 🔹 Registrar visita
        $visitData = [
            "short_code" => $shortCode,
            "ip_address" => $_SERVER['REMOTE_ADDR'],
            "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        $this->visitModel->create($visitData);

        // 🔹 Aumentar contador de usos
        $this->urlModel->incrementUses($shortCode);

        // 🔹 Redirigir
        header("Location: " . $url['original_url']);
        exit;
    }

}