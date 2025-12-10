<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="/rpi12/">
    <meta charset="UTF-8">
    <title><?= htmlentities($title ?? 'Admin Panel') ?></title>


    <link rel="stylesheet" href="css/adminweb/adminweb.css">
</head>
<body>
<?php require BASE_INCLUDES_PATH . 'sidenav.php' ?>
<h1><?php echo ($anwser ?? null); ?></h1>
<div class="layout-wrapper">



    <main class="main-content">

        <div class="table-wrapper">
        <?php if (($section ?? false) === 'tech'): ?>
            <?php include_once 'tech.php'; ?>
        <?php endif; ?>
        </div>
    </main>

</div>

</body>
</html>

