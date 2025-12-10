<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/rpi12/">
    <meta charset="UTF-8">
    <title><?= htmlentities($title ?? 'Tech Panel') ?></title>


    <link rel="stylesheet" href="css/tech/tech-panel.css">
</head>
<body>
<?php require BASE_INCLUDES_PATH . 'sidenav.php' ?>
<h1><?php echo ($anwser ?? ''); ?></h1>
<div class="layout-wrapper">



    <main class="main-content">

        <div class="table-wrapper">
        <?php if (($section ?? false) === 'screens'): ?>
            <?php include_once 'screen.php'; ?>
        <?php elseif (($section ?? false) === 'control-units'): ?>
            <?php include_once 'control_unit.php'; ?>
        <?php elseif (($section ?? false) === 'add-screen'): ?>
            <?php include_once 'screen_add.php'; ?>
        <?php elseif (($section ?? false) === 'add-control-unit'): ?>
            <?php include_once 'control_unit_add.php'; ?>
        <?php elseif (($section ?? false) === 'edit-screen'): ?>
            <?php include_once 'screen_edit.php'; ?>
        <?php elseif (($section ?? false) === 'edit-control-unit'): ?>
            <?php include_once 'control_unit_edit.php'; ?>
        <?php elseif (($section ?? false) === 'add-screens'): ?>
            <?php include_once 'screens_add_csv.php'; ?>
        <?php elseif (($section ?? false) === 'add-control-units'): ?>
            <?php include_once 'units_add_csv.php'; ?>
        <?php endif; ?>
        </div>
    </main>

</div>

</body>
</html>