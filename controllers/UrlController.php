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

}