<?php
echo "<form method='post' action='action-login.php'>

    <label>Nom d'utilisateur</label>
    <input type='text' name='username'/>
    
    <label>Mot de passe</label>
    <input type='password' name='password'/>
  
<button type='submit'>Se connecter</button>

</form>";

if (isset($_GET['error'])) {
    echo "<p style='color: red'>Erreur de connexion</p>";
}
