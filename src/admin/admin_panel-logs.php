<?php
session_start();


require '../includes/init.php';
if($_SESSION["role"] !== "adminweb"){
    header('location: ../index.php');
    exit();
}
$sidebarBase = '../';
$sidebarAdminPrefix = '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset='UTF-8'>
    <title>Admin - Logs</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminweb.css">
</head>
<body class="with-sidebar">
<?php require __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-with-sidebar">
<form method="post">
    <button class='admin-panel-section' name="action" value="ip">By IP Address</button>
    <button class='admin-panel-section' name="action" value="user">By Username</button>
    <button class='admin-panel-section' name="action" value="average">Average Duration</button>
    <button class='admin-panel-section' name="action" value="extremes">Duration Extremes</button>
    <button class='admin-panel-section' name="action" value="per_day">Logs Per Day</button>
</form>


<?php
function statsByIp($db)
{
    $sql = "SELECT ip_address, COUNT(*) AS count FROM logs GROUP BY ip_address ORDER BY count DESC";
    $result = mysqli_query($db, $sql);
    $stats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats[$row['ip_address']] = $row['count'];
    }
    return $stats;
}

// 2. Count logs per username
function statsByUser($db)
{
    $sql = "SELECT username, COUNT(*) AS count FROM logs GROUP BY username ORDER BY count DESC";
    $result = mysqli_query($db, $sql);
    $stats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats[$row['username']] = $row['count'];
    }
    return $stats;
}

// 3. Average session duration
function averageDuration($db)
{
    $sql = "SELECT AVG(duration_seconds) AS avg_duration FROM logs";
    $result = mysqli_query($db, $sql);
    $row = mysqli_fetch_assoc($result);
    return round($row['avg_duration'], 2); // in seconds
}

// 4. Longest and shortest session
function durationExtremes($db)
{
    $sql = "SELECT MAX(duration_seconds) AS max_duration, MIN(duration_seconds) AS min_duration FROM logs";
    $result = mysqli_query($db, $sql);
    return mysqli_fetch_assoc($result);
}

// 5. Logs per day
function logsPerDay($db)
{
    $sql = "SELECT DATE(log_time) AS day, COUNT(*) AS count FROM logs GROUP BY day ORDER BY day ASC";
    $result = mysqli_query($db, $sql);
    $stats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats[$row['day']] = $row['count'];
    }
    return $stats;
}

$action = $_POST['action'] ?? '';
$results = [];

if ($action === 'ip') $results = statsByIp($loginToDb);
if ($action === 'user') $results = statsByUser($loginToDb);
if ($action === 'average') $results = ["Average duration" => averageDuration($loginToDb) . " seconds"];
if ($action === 'extremes') $results = durationExtremes($loginToDb);
if ($action === 'per_day') $results = logsPerDay($loginToDb);

?>

<?php if ($results): ?>
    <table>
        <tr>
            <th>valeur</th>
            <th>données</th>
        </tr>
        <?php foreach ($results as $key => $value): ?>
            <tr>
                <td><?= htmlspecialchars($key) ?></td>
                <td><?= htmlspecialchars($value) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
</main>
</body>
</html>