<?php
session_start();
if (isset($_SESSION['username']) && $_SESSION['username'] === "adminweb"){
    echo "<div>
<h1>Ici c l'admin panel</h1>
    <a href='logout.php' class='sections'>Se déconnecter</a>

</div>
";
}