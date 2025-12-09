<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("tech");


$allControlUnitsQuery = "SELECT name FROM `control_unit` ";
$allControlUnitsResult = mysqli_query($loginToDb, $allControlUnitsQuery);


?>

<div>

    <form method="post">
        <label>Probabilité qu'une unité de contrôle va être dans le rébut</label>
        <select name="control_unit">
            <?php
            if ($allControlUnitsResult) {
                while($row = mysqli_fetch_assoc($allControlUnitsResult)) {
                    echo "<option value='" . htmlspecialchars($row['serial']) . "' >"
                        . htmlspecialchars($row['name']) . "</option>";
                }
            }
            ?>
        </select>
        <button type="submit">Calculer la probabilité</button>
    </form>
</div>