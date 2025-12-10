<?php /** @noinspection SqlNoDataSourceInspection */

class ControlUnit
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $query = "SELECT * FROM control_unit";
        $result = $this->db->query($query);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUnit($data): bool
    {
        $query = "INSERT IGNORE INTO control_unit
        (serial, name, id_manufacturer, model, type, cpu, ram_mb, 
         disk_gb, id_os, domain, location, building, room, 
         macaddr, purchase_date, warranty_end)
        VALUES
(NULLIF(:serial, ''), NULLIF(:name, ''), NULLIF(:id_manufacturer, ''), 
 NULLIF(:model, ''), NULLIF(:type, ''), NULLIF(:cpu, ''), NULLIF(:ram_mb, ''),
 NULLIF(:disk_gb, ''), NULLIF(:id_os, ''), NULLIF(:domain, ''), 
 NULLIF(:location, ''), NULLIF(:building, ''), NULLIF(:room, ''), 
 NULLIF(:macaddr, ''), NULLIF(:purchase_date, ''), NULLIF(:warranty_end, ''));";

        $stmt = $this->db->prepare($query);

// Bind parameters
        $stmt->bindParam(':serial', $data['serial']);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':id_manufacturer', $data['id_manufacturer']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':cpu', $data['cpu']);
        $stmt->bindParam(':ram_mb', $data['ram_mb']);
        $stmt->bindParam(':disk_gb', $data['disk_gb']);
        $stmt->bindParam(':id_os', $data['id_os']);
        $stmt->bindParam(':domain', $data['domain']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':building', $data['building']);
        $stmt->bindParam(':room', $data['room']);
        $stmt->bindParam(':macaddr', $data['macaddr']);
        $stmt->bindParam(':purchase_date', $data['purchase_date']);
        $stmt->bindParam(':warranty_end', $data['warranty_end']);

        return $stmt->execute();
    }

    public function editUnit($serial, $data): bool
    {
        $query = "UPDATE control_unit SET
        name = NULLIF(:name, ''),
        id_manufacturer = NULLIF(:id_manufacturer, ''),
        model = NULLIF(:model, ''),
        type = NULLIF(:type, ''),
        cpu = NULLIF(:cpu, ''),
        ram_mb = NULLIF(:ram_mb, ''),
        disk_gb = NULLIF(:disk_gb, ''),
        id_os = NULLIF(:id_os, ''),
        domain = NULLIF(:domain, ''),
        location = NULLIF(:location, ''),
        building = NULLIF(:building, ''),
        room = NULLIF(:room, ''),
        macaddr = NULLIF(:macaddr, ''),
        purchase_date = NULLIF(:purchase_date, ''),
        warranty_end = NULLIF(:warranty_end, '')
        WHERE serial = :serial;";

        $stmt = $this->db->prepare($query);


        $stmt->bindParam(':serial', $serial);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':id_manufacturer', $data['id_manufacturer']);
        $stmt->bindParam(':model', $data['model']);
        $stmt->bindParam(':type', $data['type']);
        $stmt->bindParam(':cpu', $data['cpu']);
        $stmt->bindParam(':ram_mb', $data['ram_mb']);
        $stmt->bindParam(':disk_gb', $data['disk_gb']);
        $stmt->bindParam(':id_os', $data['id_os']);
        $stmt->bindParam(':domain', $data['domain']);
        $stmt->bindParam(':location', $data['location']);
        $stmt->bindParam(':building', $data['building']);
        $stmt->bindParam(':room', $data['room']);
        $stmt->bindParam(':macaddr', $data['macaddr']);
        $stmt->bindParam(':purchase_date', $data['purchase_date']);
        $stmt->bindParam(':warranty_end', $data['warranty_end']);


        return $stmt->execute();
    }

    public function deleteUnit($serial): bool
    {
        $query = "DELETE FROM control_unit WHERE serial = :serial";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':serial', $serial);

        return $stmt->execute();
    }

    public function getUnit($serial): array
    {
        $query = "SELECT * FROM control_unit WHERE serial = :serial";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':serial', $serial);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUnitByName(?string $name)
{
    if ($name === null) {
        return null;
    }

    $query = "SELECT name FROM control_unit WHERE name = :name LIMIT 1";
    $stmt = $this->db->prepare($query);
    $stmt->execute(['name' => $name]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ?: null;
}

    public function addUnitsCSV($file): bool
    {
        $mapping = [
            'NAME' => 'name',
            'SERIAL' => 'serial',
            'MANUFACTURER' => 'id_manufacturer',
            'MODEL' => 'model',
            'TYPE' => 'type',
            'CPU' => 'cpu',
            'RAM_MB' => 'ram_mb',
            'DISK_GB' => 'disk_gb',
            'OS' => 'id_os',
            'DOMAIN' => 'domain',
            'LOCATION' => 'location',
            'BUILDING' => 'building',
            'ROOM' => 'room',
            'MACADDR' => 'macaddr',
            'PURCHASE_DATE' => 'purchase_date',
            'WARRANTY_END' => 'warranty_end'
        ];

        if (($handle = fopen($file, "r")) !== FALSE) {

            // Read header row
            $header = fgetcsv($handle, 1000, ",");

            // Convert header names to uppercase to avoid mismatch
            $header = array_map('strtoupper', $header);

            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {

                // Map CSV row → $data for DB
                $data = [];

                foreach ($header as $index => $colName) {
                    if (isset($mapping[$colName])) {
                        $key = $mapping[$colName];

                        // Handle manufacturer
                        if ($key === 'id_manufacturer') {
                            $database = new Database();
                            $manufacturerModel = new Manufacturer($database->getConnection());
                            $manu = $manufacturerModel->manufacturerId($row[$index]);
                            $data[$key] = $manu['id'] ?? null;
                        } else if ($key === 'id_os') {
                            $database = new Database();
                            $osModel = new Os($database->getConnection());
                            $os = $osModel->osId($row[$index]);
                            $data[$key] = $os['id'] ?? null;
                        } else {
                            $data[$key] = $row[$index] !== '' ? $row[$index] : null;
                        }
                    }
                }

                $this->addUnit($data);
            }
        }
        return true;
    }


    ###############
    #####STATS#####
    ###############

    public
    function getTypeDistribution(): array
    {
        $query = "SELECT type, 
              COUNT(*) as count,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM control_unit), 2) as percentage
              FROM control_unit
              WHERE type IS NOT NULL
              GROUP BY type
              ORDER BY count DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Statistiques RAM
    public
    function getRAMStatistics(): array
    {
        $query = "SELECT 
              MIN(ram_mb) as min_ram,
              MAX(ram_mb) as max_ram,
              ROUND(AVG(ram_mb), 2) as avg_ram,
              CASE 
                  WHEN ram_mb < 4096 THEN '< 4GB'
                  WHEN ram_mb < 8192 THEN '4-8GB'
                  WHEN ram_mb < 16384 THEN '8-16GB'
                  WHEN ram_mb < 32768 THEN '16-32GB'
                  ELSE '>= 32GB'
              END as ram_range,
              COUNT(*) as count
              FROM control_unit
              WHERE ram_mb IS NOT NULL
              GROUP BY ram_range
              ORDER BY MIN(ram_mb)";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Statistiques Disque dur
    public
    function getDiskStatistics(): array
    {
        $query = "SELECT 
              CASE 
                  WHEN disk_gb < 256 THEN '< 256GB'
                  WHEN disk_gb < 512 THEN '256-512GB'
                  WHEN disk_gb < 1024 THEN '512GB-1TB'
                  WHEN disk_gb < 2048 THEN '1-2TB'
                  ELSE '>= 2TB'
              END as disk_range,
              COUNT(*) as count,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM control_unit WHERE disk_gb IS NOT NULL), 2) as percentage
              FROM control_unit
              WHERE disk_gb IS NOT NULL
              GROUP BY disk_range
              ORDER BY MIN(disk_gb)";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Répartition par fabricant
    public
    function getManufacturerDistribution(): array
    {
        $query = "SELECT id_manufacturer, 
              COUNT(*) as count,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM control_unit), 2) as percentage
              FROM control_unit
              WHERE id_manufacturer IS NOT NULL
              GROUP BY id_manufacturer
              ORDER BY count DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Répartition par OS
    public
    function getOSDistribution(): array
    {
        $query = "SELECT id_os, 
              COUNT(*) as count,
              ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM control_unit), 2) as percentage
              FROM control_unit
              WHERE id_os IS NOT NULL
              GROUP BY id_os
              ORDER BY count DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Répartition géographique
    public
    function getLocationDistribution(): array
    {
        $query = "SELECT location, building, 
              COUNT(*) as count
              FROM control_unit
              WHERE location IS NOT NULL
              GROUP BY location, building
              ORDER BY count DESC";

        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Unités avec garantie expirée ou proche de l'expiration
    public
    function getWarrantyStatus(int $daysThreshold = 90): array
    {
        $query = "SELECT 
              CASE 
                  WHEN warranty_end < CURRENT_DATE THEN 'Expirée'
                  WHEN warranty_end < DATE_ADD(CURRENT_DATE, INTERVAL :days DAY) THEN 'Expire bientôt'
                  ELSE 'Active'
              END as status,
              COUNT(*) as count
              FROM control_unit
              WHERE warranty_end IS NOT NULL
              GROUP BY status";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':days', $daysThreshold, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

// Âge moyen du parc informatique
    public
    function getAverageAge(): array
    {
        $query = "SELECT 
              ROUND(AVG(DATEDIFF(CURRENT_DATE, purchase_date) / 365.25), 2) as avg_age_years,
              MIN(purchase_date) as oldest_purchase,
              MAX(purchase_date) as newest_purchase,
              COUNT(*) as total_with_date
              FROM control_unit
              WHERE purchase_date IS NOT NULL";

        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}