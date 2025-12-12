<?php /** @noinspection SqlNoDataSourceInspection */

class Screen
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $query = "SELECT * FROM screen";
        $result = $this->db->query($query);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addScreen($data): bool
    {
        $query = "INSERT IGNORE INTO screen
        (
    serial,
    id_manufacturer,
    model,
    size_inch,
    resolution,
    connector,
    attached_to
        )
        VALUES
        (
    NULLIF(:serial, ''),
    NULLIF(:id_manufacturer, ''),
    NULLIF(:model, ''),
    NULLIF(:size_inch, ''),
    NULLIF(:resolution, ''),
    NULLIF(:connector, ''),
    NULLIF(:attached_to, '')
        );";

        $stmt = $this->db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':serial', $data['serial']);
        $stmt->bindParam(':id_manufacturer', $data['id_manufacturer']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':size_inch', $data['size_inch']);
        $stmt->bindParam(':resolution', $data['resolution']);
        $stmt->bindParam(':connector', $data['connector']);
        $stmt->bindParam(':attached_to', $data['attached_to']);

        return $stmt->execute();
    }

    public function editScreen($serial, $data): bool
    {
        $query = "UPDATE screen SET
    id_manufacturer = NULLIF(:id_manufacturer, ''),
    model = NULLIF(:model, ''),
    size_inch = NULLIF(:size_inch, ''),
    resolution = NULLIF(:resolution, ''),
    connector = NULLIF(:connector, ''),
    attached_to = NULLIF(:attached_to, '')
WHERE serial = :serial;";

        $stmt = $this->db->prepare($query);

        // New values
        $stmt->bindParam(':serial', $serial);
        $stmt->bindParam(':id_manufacturer', $data['id_manufacturer']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':size_inch', $data['size_inch']);
        $stmt->bindParam(':resolution', $data['resolution']);
        $stmt->bindParam(':connector', $data['connector']);
        $stmt->bindParam(':attached_to', $data['attached_to']);


        return $stmt->execute();
    }

    public function deleteScreen($serial): bool
    {
        $query = "DELETE FROM screen WHERE serial = :serial";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':serial', $serial);

        return $stmt->execute();
    }

    public function getScreen($serial): array
    {
        $query = "SELECT * FROM screen WHERE serial = :serial";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':serial', $serial);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addScreensCSV($file): bool
    {
        $mapping = [
            'SERIAL' => 'serial',
            'MANUFACTURER' => 'id_manufacturer',
            'MODEL' => 'model',
            'SIZE_INCH' => 'size_inch',
            'RESOLUTION' => 'resolution',
            'CONNECTOR' => 'connector',
            'ATTACHED_TO' => 'attached_to'
        ];

        if (($handle = fopen($file, "r")) !== FALSE) {

            $header = fgetcsv($handle, 1000, ",");

            $header = array_map('strtoupper', $header);

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {


                $data = [];

                foreach ($header as $index => $colName) {
                    if (isset($mapping[$colName])) {
                        $key = $mapping[$colName];


                        if ($key === 'id_manufacturer') {
                            $database = new Database();
                            $manufacturerModel = new Manufacturer($database->getConnection());
                            $manu = $manufacturerModel->manufacturerId($row[$index]);
                            $data[$key] = $manu['id'] ?? null;
                        }
                        else if ($key === 'attached_to') {
                            if ($row[$index] !== '' && $row[$index] !== null) {
                                $database = new Database();
                                $controlUnitModel = new ControlUnit($database->getConnection());
                                $unit = $controlUnitModel->getUnitByName($row[$index]);
                                $data[$key] = $unit['name'] ?? null;
                            } else {
                                $data[$key] = null;
                            }
                        } else {
                            $data[$key] = $row[$index] !== '' ? $row[$index] : null;
                        }
                    }
                }

                $this->addScreen($data);
            }

            fclose($handle);
        }
        return true;
    }

    ###############
    #####STATS#####
    ###############

    public function getSizeDistribution(): array
    {
        $query = "SELECT size_inch, 
              COUNT(*) as count,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM screen WHERE size_inch IS NOT NULL), 2) as percentage
              FROM screen
              WHERE size_inch IS NOT NULL
              GROUP BY size_inch
              ORDER BY size_inch";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Distribution des résolutions
    public function getResolutionDistribution(): array
    {
        $query = "SELECT resolution, 
              COUNT(*) as count,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM screen WHERE resolution IS NOT NULL), 2) as percentage
              FROM screen
              WHERE resolution IS NOT NULL
              GROUP BY resolution
              ORDER BY count DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Distribution des connecteurs
    public function getConnectorDistribution(): array
    {
        $query = "SELECT connector, 
              COUNT(*) as count,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM screen WHERE connector IS NOT NULL), 2) as percentage
              FROM screen
              WHERE connector IS NOT NULL
              GROUP BY connector
              ORDER BY count DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Répartition par fabricant
    public function getManufacturerDistribution(): array
    {
        $query = "SELECT id_manufacturer, 
              COUNT(*) as count,
              ROUND(AVG(size_inch), 2) as avg_size,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM screen), 2) as percentage
              FROM screen
              WHERE id_manufacturer IS NOT NULL
              GROUP BY id_manufacturer
              ORDER BY count DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Écrans non attachés (disponibles)
    public function getUnattachedScreens(): array
    {
        $query = "SELECT COUNT(*) as count,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM screen), 2) as percentage
              FROM screen
              WHERE attached_to IS NULL OR attached_to = ''";

        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
        public function getUnattachedScreensList(): array
    {
        $query = "SELECT * FROM screen
              WHERE attached_to IS NULL OR attached_to = ''";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Nombre d'écrans par unité centrale
    public function getScreensPerUnit(): array
    {
        $query = "SELECT attached_to, 
              COUNT(*) as screen_count
              FROM screen
              WHERE attached_to IS NOT NULL AND attached_to != ''
              GROUP BY attached_to
              ORDER BY screen_count DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}