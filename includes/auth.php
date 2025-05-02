<?php
session_start(); // Toujours démarrer la session

if (!isset($_SESSION['id_users'])) {
    header('Location: /login.php');
    exit();
}
?>