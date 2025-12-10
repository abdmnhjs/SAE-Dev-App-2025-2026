<?php

class Os
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function osName(?string $id)
    {

        if ($id === null) {
            return [
                'id' => null,
                'name' => 'Unknown OS'
            ];
        }

        $query = "SELECT * FROM os_list WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$result) {
            return [
                'id' => $id,
                'name' => 'Unknown OS'
            ];
        }

        return $result;
    }

    public function all(): array
    {
        $query = "SELECT * FROM os_list";
        $result = $this->db->query($query);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function osId(?string $name)
    {
        if ($name === null) {
            return null;
        }

        $query = "SELECT id FROM os_list WHERE name = :name LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['name' => $name]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}