<?php

class Visit {

    private $conn;
    private $table = "visits";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {

        $sql = "INSERT INTO {$this->table}
                (short_code, ip_address, user_agent, visited_at)
                VALUES (:short_code, :ip_address, :user_agent, NOW())";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":short_code" => $data['short_code'],
            ":ip_address" => $data['ip_address'],
            ":user_agent" => $data['user_agent']
        ]);
    }
}