<?php

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function login(string $name, string $password): ?array
    {
        $query = "SELECT * FROM users WHERE name = :name LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['name' => $name]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC); // WIP : PDO::FETCH_ASSOC, qu'est ce que ca fait exactement ?


        if (!$user) {
            return null;
        }


        if ($password !== $user['password']) {
            return null;
        }

        //pour logs
        if (!isset($_SESSION['session_start_time'])) {
            $_SESSION['session_start_time'] = time();
        }

        return $user;
    }

    public function logout(): bool
    {


        $login = $_SESSION['name'] ?? ""; // ignorer si le login est pas trouver ?
        
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $duration = time() - $_SESSION['session_start_time'];

        // PDO prepared statement
        $query = "INSERT IGNORE INTO logs (login, ip_address, duration_seconds) 
              VALUES (:login, :ip_address, :duration)";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
        $stmt->bindParam(':duration', $duration, PDO::PARAM_STR);
        $stmt->execute();

        $_SESSION = [];

        session_destroy();
        return true;
    }

    public function signup(string $name, string $password, int $rank = 0): bool
    {
        // RSA futurement
        $RSAPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL query
        $query = "INSERT INTO users (name, password, rank) VALUES (:name, :password, :rank)";
        $stmt = $this->db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':rank', $rank, PDO::PARAM_INT);

        // Execute and return success status
        return $stmt->execute();
    }

    public static function isLoggedIn(): bool
    {

    }

    public function DuplicateName(string $name): bool
    {
        $checkQuery = "SELECT COUNT(*) FROM users WHERE name = :name";
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':name', $name, PDO::PARAM_STR);
        $checkStmt->execute();

        if ($checkStmt->fetchColumn() > 0) {
            // User already exists
            return true;
        }
        return false;
    }

    public function getUsersByRank(int $rank): ?array
    {
        $query = "SELECT * FROM users WHERE rank = :rank";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':rank', $rank, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}