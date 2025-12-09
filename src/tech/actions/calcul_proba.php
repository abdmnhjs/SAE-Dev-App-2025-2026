<?php
class calcul_proba {
    private $conn;

    public function __construct($mysqli_connection) {
        $this->conn = $mysqli_connection;
    }

    // Moyenne RAM par OS ou type de machine
    public function avg_ram($type = null) {
        $where = $type ? "WHERE type = '$type'" : "";
        $query = "SELECT AVG(ram_gb) AS mean_ram FROM control_unit $where";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['mean_ram'];
    }

    // Moyenne disque par OS ou type de machine
    public function avg_disk($type = null) {
        $where = $type ? "WHERE type = '$type'" : "";
        $query = "SELECT AVG(disk_gb) AS mean_disk FROM control_unit $where";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['mean_disk'];
    }

    // Distribution des OS en pourcentage
    public function os_distribution() {
        $query = "SELECT os, COUNT(*) * 100.0 / (SELECT COUNT(*) FROM control_unit) AS pct FROM control_unit GROUP BY os";
        $result = mysqli_query($this->conn, $query);
        $distribution = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $distribution[$row['os']] = $row['pct'];
        }
        return $distribution;
    }

    // Probabilité qu'une machine ait ≥ seuil RAM
    public function prob_high_ram($threshold) {
        $query = "SELECT COUNT(*) AS cnt FROM control_unit WHERE ram_gb >= $threshold";
        $result = mysqli_query($this->conn, $query);
        $count = mysqli_fetch_assoc($result)['cnt'];
        $totalQuery = "SELECT COUNT(*) AS total FROM control_unit";
        $totalResult = mysqli_query($this->conn, $totalQuery);
        $total = mysqli_fetch_assoc($totalResult)['total'];
        return $total > 0 ? $count / $total : 0;
    }

    // Moyenne taille écran par fabricant
    public function avg_screen_size($manuId) {
        $query = "SELECT AVG(size_inch) AS mean FROM screen WHERE id_manufacturer = $manuId";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['mean'];
    }

    // Probabilité qu'un écran soit ≥ seuil
    public function prob_large_screen($threshold) {
        $query = "SELECT COUNT(*) AS cnt FROM screen WHERE size_inch >= $threshold";
        $result = mysqli_query($this->conn, $query);
        $count = mysqli_fetch_assoc($result)['cnt'];
        $totalQuery = "SELECT COUNT(*) AS total FROM screen";
        $totalResult = mysqli_query($this->conn, $totalQuery);
        $total = mysqli_fetch_assoc($totalResult)['total'];
        return $total > 0 ? $count / $total : 0;
    }

    // Statistiques de connexion pour un utilisateur
    public function connection_stats($user) {
        $query = "SELECT AVG(duration) AS avg_duration, MAX(duration) AS max_duration, MIN(duration) AS min_duration
                  FROM logs WHERE login = '$user'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Probabilité qu'une session soit ≥ seuil
    public function prob_long_session($threshold) {
        $query = "SELECT COUNT(*) AS cnt FROM logs WHERE duration >= $threshold";
        $result = mysqli_query($this->conn, $query);
        $count = mysqli_fetch_assoc($result)['cnt'];
        $totalQuery = "SELECT COUNT(*) AS total FROM logs";
        $totalResult = mysqli_query($this->conn, $totalQuery);
        $total = mysqli_fetch_assoc($totalResult)['total'];
        return $total > 0 ? $count / $total : 0;
    }

    // Pourcentage machines encore sous garantie
    public function machines_under_warranty() {
        $today = date('Y-m-d');
        $query = "SELECT COUNT(*) AS cnt FROM control_unit WHERE warranty_end >= '$today'";
        $result = mysqli_query($this->conn, $query);
        $count = mysqli_fetch_assoc($result)['cnt'];
        $totalQuery = "SELECT COUNT(*) AS total FROM control_unit";
        $totalResult = mysqli_query($this->conn, $totalQuery);
        $total = mysqli_fetch_assoc($totalResult)['total'];
        return $total > 0 ? $count / $total : 0;
    }

    // Données CPU vs RAM pour scatter plot
    public function scatter_cpu_ram() {
        $query = "SELECT cpu_ghz, ram_gb FROM control_unit";
        $result = mysqli_query($this->conn, $query);
        $points = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $points[] = ['cpu' => $row['cpu_ghz'], 'ram' => $row['ram_gb']];
        }
        return $points;
    }
}
?>
