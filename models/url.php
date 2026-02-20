<?php

class Url {

    private $conn;
    private $table = "urls";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 🔹 Crear nueva URL
    public function create($data) {
        $sql = "INSERT INTO {$this->table}
                (original_url, short_code, expires_at, max_uses, creator_ip, uses)
                VALUES (:original_url, :short_code, :expires_at, :max_uses, :creator_ip, 0)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":original_url" => $data['original_url'],
            ":short_code"   => $data['short_code'],
            ":expires_at"   => $data['expires_at'],
            ":max_uses"     => $data['max_uses'],
            ":creator_ip"   => $data['creator_ip']
        ]);
    }

    // 🔹 Buscar por código corto
    public function findByCode($shortCode) {
        $sql = "SELECT * FROM {$this->table} WHERE short_code = :short_code LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":short_code" => $shortCode]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Incrementar contador de usos
    public function incrementUses($shortCode) {
        $sql = "UPDATE {$this->table}
                SET uses = uses + 1
                WHERE short_code = :short_code";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([":short_code" => $shortCode]);
    }
}