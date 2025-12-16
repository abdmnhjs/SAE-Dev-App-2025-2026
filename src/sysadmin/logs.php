<?php
session_start();

require '../includes/init.php';
if($_SESSION["role"] !== "sysadmin"){
    header('location: ../index.php');
    exit();
} ?>

<h1>Ici c les logs</h1>
