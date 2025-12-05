<?php
session_start();

require '../includes/init.php';
ensureUserAuthorized("adminweb");

if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){
    echo "<div>
<form method='post' action='actions/action-create-tech.php'>
<label for='username'>Nom</label>
<input type='text' name='username' id='username'>
<label for='password'>Mot de passe</label>
<input type='password' name='password' id='password'>
<button type='submit' name='submit'>Créer le technicien</button>
</form>
</div>
";

}