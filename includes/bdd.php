<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3308;dbname=helpdesk;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}
?>