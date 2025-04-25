<?php
require_once 'bdd.php';

if (!isset($_SESSION['id_users']) || $_SESSION['droits'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_ticket = (int) $_GET['id'];

    // On vérifie si le ticket existe avant de le supprimer
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_ticket = ?");
    $stmt->execute([$id_ticket]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        // Suppression du ticket
        $stmt = $pdo->prepare("DELETE FROM tickets WHERE id_ticket = ?");
        $stmt->execute([$id_ticket]);

        header("Location: HistTicket.php");
        exit();
    } else {
        die('Ticket introuvable.');
    }
} else {
    die('ID ticket manquant.');
}
