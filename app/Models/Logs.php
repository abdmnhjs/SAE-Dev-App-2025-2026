<?php

class Logs
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Statistiques de base
    public function getTotalConnections(): int
    {
        $query = "SELECT COUNT(*) as total FROM logs";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getUniqueUsers(): int
    {
        $query = "SELECT COUNT(DISTINCT login) as total FROM logs";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getAverageSessionDuration(): float
    {
        $query = "SELECT AVG(duration_seconds) as avg_duration FROM logs";
        $stmt = $this->db->query($query);
        return round($stmt->fetch(PDO::FETCH_ASSOC)['avg_duration'], 2);
    }

    // Distribution des durées de connexion
    public function getSessionDurationDistribution(): array
    {
        $query = "SELECT 
            CASE 
                WHEN duration_seconds < 300 THEN '0-5 min'
                WHEN duration_seconds < 900 THEN '5-15 min'
                WHEN duration_seconds < 1800 THEN '15-30 min'
                WHEN duration_seconds < 3600 THEN '30-60 min'
                ELSE '> 1h'
            END as duration_range,
            COUNT(*) as count,
            ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM logs), 2) as percentage
            FROM logs
            GROUP BY duration_range
            ORDER BY MIN(duration_seconds)";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Top utilisateurs par nombre de connexions
    public function getTopUsersByConnections(int $limit = 10): array
    {
        $query = "SELECT login, 
                  COUNT(*) as connection_count,
                  ROUND(AVG(duration_seconds), 2) as avg_duration,
                  SUM(duration_seconds) as total_duration
                  FROM logs
                  GROUP BY login
                  ORDER BY connection_count DESC
                  LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Top utilisateurs par temps total de connexion
    public function getTopUsersByDuration(int $limit = 10): array
    {
        $query = "SELECT login, 
                  SUM(duration_seconds) as total_duration,
                  COUNT(*) as connection_count,
                  ROUND(AVG(duration_seconds), 2) as avg_duration
                  FROM logs
                  GROUP BY login
                  ORDER BY total_duration DESC
                  LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Adresses IP les plus utilisées
    public function getTopIPAddresses(int $limit = 10): array
    {
        $query = "SELECT ip_address, 
                  COUNT(*) as connection_count,
                  COUNT(DISTINCT login) as unique_users
                  FROM logs
                  GROUP BY ip_address
                  ORDER BY connection_count DESC
                  LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Détection d'anomalies : sessions très courtes
    public function getAnomalouslyShortSessions(int $threshold = 60): array
    {
        $query = "SELECT login, ip_address, duration_seconds
                  FROM logs
                  WHERE duration_seconds < :threshold
                  ORDER BY duration_seconds ASC
                  LIMIT 100";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Détection d'anomalies : sessions très longues
    public function getAnomalouslyLongSessions(int $threshold = 28800): array
    {
        $query = "SELECT login, ip_address, duration_seconds
                  FROM logs
                  WHERE duration_seconds > :threshold
                  ORDER BY duration_seconds DESC
                  LIMIT 100";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':threshold', $threshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // IPs suspectes (plusieurs utilisateurs différents)
    public function getSuspiciousIPs(int $minUsers = 5): array
    {
        $query = "SELECT ip_address, 
                  COUNT(DISTINCT login) as user_count,
                  COUNT(*) as connection_count
                  FROM logs
                  GROUP BY ip_address
                  HAVING user_count >= :minUsers
                  ORDER BY user_count DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':minUsers', $minUsers, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}