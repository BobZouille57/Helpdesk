<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
try {
    $pdo = new PDO("mysql:host=localhost;port=3308;dbname=clement_db;charset=utf8", "clement", "Clement2003+");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erreur de connexion : " . $e->getMessage());
}
?>