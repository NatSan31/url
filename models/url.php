<?php

class Url {
    private $conn;
    private $table = "urls";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . "
                  (original_url, short_code, expires_at, max_uses, creator_ip)
                  VALUES (:original_url, :short_code, :expires_at, :max_uses, :creator_ip)";

        $stmt = $this->conn->prepare($query);

        return $stmt->execute($data);
    }

    public function findByCode($code) {
        $query = "SELECT * FROM " . $this->table . " WHERE short_code = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":code", $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function incrementVisits($id) {
        $query = "UPDATE " . $this->table . "
                  SET visit_count = visit_count + 1
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}