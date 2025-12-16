<?php
session_start();


require '../includes/init.php';
if($_SESSION["role"] !== "adminweb"){
    header('location: ../index.php');
    exit();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Tech Panel</title>
    <link rel="stylesheet" href="../css/adminweb/adminweb.css">
</head>
<body>
<div class='sidebar'>
    <div class='sidebar-sections'>
        <a class='sidebar-section' href='../logout.php' class='sections'>Se déconnecter</a>
        <a class='sidebar-section' href='create-tech-form.php'>Créer un technicien</a>
        <a class='sidebar-section' href='add-os-form.php'>Ajouter un système d'exploitation</a>
        <a class='sidebar-section' href='add-manufacturer-form.php'>Ajouter un fabriquant</a>
        <a class="sidebar-section" href="../stats.php">Statistiques</a>
        <a class="sidebar-section" href="admin_panel-logs.php">Logs</a>

    </div>
</div>


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
</html>