<?php
session_start();
if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){

    echo "
<div>
    <form method='post' action='actions/action-add-os.php'>
        <label>Nom</label>
        <input type='text' name='os_name' >
        <button type='submit'>Ajouter le système d'exploitation</button>
    </form>
</div>
    ";

 } ?>
