<?php

class Manufacturer
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function manufacturerName(?string $id)
    {

        if ($id === null) {
            return [
                'id' => null,
                'name' => ''
            ];
        }

        $query = "SELECT * FROM manufacturer_list WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$result) {
            return [
                'id' => $id,
                'name' => ''
            ];
        }

        return $result;
    }

    public function manufacturerId(?string $name)
    {
        if ($name === null) {
            return [
                'id' => null,
                'name' => null
            ];
        }

        $query = "SELECT * FROM manufacturer_list WHERE name = :name LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['name' => $name]); // FIXED

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If manufacturer not found → return null id
        if (!$result) {
            return [
                'id' => null,
                'name' => null
            ];
        }

        return $result;
    }

    public function all(): array
    {
        $query = "SELECT * FROM manufacturer_list";
        $result = $this->db->query($query);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
}