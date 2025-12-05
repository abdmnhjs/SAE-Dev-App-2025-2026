<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("adminweb");


if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){

    echo "
<div>
    <form method='post' action='actions/action-add-manufacturer.php'>
        <label>Nom</label>
        <input type='text' name='manufacturer_name' >
        <button type='submit'>Ajouter le frabiquant</button>
    </form>
</div>
    ";

} ?>
