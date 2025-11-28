<a href='login.php'>Se connecter</a>
<br/>

<?php
session_start();

// Vérifier si l'utilisateur est connecté
if(isset($_SESSION['username']) && $_SESSION['username'] == "sysadmin"){
    echo "<a href='logout.php'>Se déconnecter</a>";
    echo "<div>
    <p>bonjour <span style='font-weight: bold'>sysadmin</span></p>


    </div>";

} elseif (isset($_SESSION['username']) && $_SESSION['username'] == "adminweb"){
    echo "<a href='logout.php'>Se déconnecter</a>";
    echo "
<div>
<p>bonjour <span style='font-weight: bold'>adminweb</span></p>


</div>";
} elseif (isset($_SESSION['username']) && $_SESSION['username'] == "tech"){
    echo "<a href='logout.php'>Se déconnecter</a>";
    echo "
<div>
<p>bonjour <span style='font-weight: bold'>tech</span></p>


</div>";
} else {
    echo "<p>Vous n'êtes pas connecté.</p>";
}
?>