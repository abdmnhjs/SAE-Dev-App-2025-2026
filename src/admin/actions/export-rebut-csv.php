<?php
session_start();
require '../../includes/init.php';
if ($_SESSION["role"] !== "adminweb") {
    header('location: ../../index.php');
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : '';
if (!in_array($type, ['screen', 'central_unit'], true)) {
    header('location: ../rebut-list.php');
    exit();
}

if ($type === 'screen') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="rebut_ecrans.csv"');
    $out = fopen('php://output', 'w');
    fprintf($out, "\xEF\xBB\xBF"); // BOM UTF-8
    fputcsv($out, ['SERIAL', 'FABRICANT', 'MODELE', 'TAILLE_POUCES', 'RESOLUTION', 'CONNECTEUR', 'ATTACHED_TO', 'REBUT_DATE'], ',');
    $rows = mysqli_query($loginToDb, "SELECT s.serial, m.name AS manufacturer, s.model, s.size_inch, s.resolution, s.connector, s.attached_to, s.rebut_date FROM screen s LEFT JOIN manufacturer_list m ON m.id = s.id_manufacturer WHERE s.rebut_date IS NOT NULL ORDER BY s.serial");
    while ($row = mysqli_fetch_assoc($rows)) {
        fputcsv($out, [
            $row['serial'] ?? '',
            $row['manufacturer'] ?? '',
            $row['model'] ?? '',
            $row['size_inch'] ?? '',
            $row['resolution'] ?? '',
            $row['connector'] ?? '',
            $row['attached_to'] ?? '',
            $row['rebut_date'] ?? '',
        ], ',');
    }
    fclose($out);
    exit;
}

// central_unit
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="rebut_unites_centrales.csv"');
$out = fopen('php://output', 'w');
fprintf($out, "\xEF\xBB\xBF");
fputcsv($out, ['NAME', 'SERIAL', 'FABRICANT', 'MODELE', 'TYPE', 'CPU', 'RAM_MB', 'DISK_GB', 'OS', 'DOMAIN', 'LOCATION', 'BUILDING', 'ROOM', 'MACADDR', 'PURCHASE_DATE', 'WARRANTY_END', 'REBUT_DATE'], ',');
$rows = mysqli_query($loginToDb, "SELECT cu.name, cu.serial, m.name AS manufacturer, cu.model, cu.type, cu.cpu, cu.ram_mb, cu.disk_gb, o.name AS os_name, cu.domain, cu.location, cu.building, cu.room, cu.macaddr, cu.purchase_date, cu.warranty_end, cu.rebut_date FROM central_unit cu LEFT JOIN manufacturer_list m ON m.id = cu.id_manufacturer LEFT JOIN os_list o ON o.id = cu.id_os WHERE cu.rebut_date IS NOT NULL ORDER BY cu.name");
while ($row = mysqli_fetch_assoc($rows)) {
    fputcsv($out, [
        $row['name'] ?? '',
        $row['serial'] ?? '',
        $row['manufacturer'] ?? '',
        $row['model'] ?? '',
        $row['type'] ?? '',
        $row['cpu'] ?? '',
        $row['ram_mb'] ?? '',
        $row['disk_gb'] ?? '',
        $row['os_name'] ?? '',
        $row['domain'] ?? '',
        $row['location'] ?? '',
        $row['building'] ?? '',
        $row['room'] ?? '',
        $row['macaddr'] ?? '',
        $row['purchase_date'] ?? '',
        $row['warranty_end'] ?? '',
        $row['rebut_date'] ?? '',
    ], ',');
}
fclose($out);
exit;
