<?php
include_once "html/connexion.html";

if (isset($_GET['error'])) {
    echo "<p style='color: red'>Erreur de connexion</p>";
}
