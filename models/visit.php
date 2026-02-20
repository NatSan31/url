<?php

class Visit {
    private $conn;
    private $table = "visits";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($data) {
        $query = "INSERT INTO " . $this->table . "
                  (url_id, ip_address, user_agent)
                  VALUES (:url_id, :ip_address, :user_agent)";

        $stmt = $this->conn->prepare($query);
        return $stmt->execute($data);
    }
}