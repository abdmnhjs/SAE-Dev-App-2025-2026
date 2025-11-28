<?php
session_start();
include_once "html/accueil.html";
echo "<a href='logout.php'>Se déconnecter</a>";
if(isset($_SESSION['username']) && $_SESSION['username'] == "sysadmin"){

    echo "<div>
    <p>bonjour <span style='font-weight: bold'>sysadmin</span></p>


    </div>";

} elseif (isset($_SESSION['username']) && $_SESSION['username'] == "adminweb"){

    echo "
<div>
<p>bonjour <span style='font-weight: bold'>adminweb</span></p>


</div>";

    echo "
<div>
<p>bonjour <span style='font-weight: bold'>tech</span></p>


</div>";
} else {
    echo "<p>Vous n'êtes pas connecté.</p>";
}
?>