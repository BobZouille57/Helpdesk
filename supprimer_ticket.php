<?php
require_once 'includes/bdd.php';
session_start();

if (!isset($_SESSION['id_users']) || $_SESSION['droits'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_ticket = (int) $_GET['id'];

    // Récupérer le ticket
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_ticket = ?");
    $stmt->execute([$id_ticket]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ticket) {
        // Si la priorité est "urgente", on archive
        if ($ticket['priorite'] === 'urgente') {
            // Archiver le ticket dans tickets_supp
            $stmtInsertTicket = $pdo->prepare("
                INSERT INTO tickets_supp 
                (id_ticket, titre, categorie, description, id_user, statut, date_creation, priorite)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmtInsertTicket->execute([
                $ticket['id_ticket'], $ticket['titre'], $ticket['categorie'],
                $ticket['description'], $ticket['id_user'], $ticket['statut'],
                $ticket['date_creation'], $ticket['priorite']
            ]);

            // Récupérer les réponses liées
            $stmtReponses = $pdo->prepare("SELECT * FROM reponses WHERE id_ticket = ?");
            $stmtReponses->execute([$id_ticket]);
            $reponses = $stmtReponses->fetchAll(PDO::FETCH_ASSOC);

            // Archiver chaque réponse
            foreach ($reponses as $reponse) {
                $stmtInsertReponse = $pdo->prepare("
                    INSERT INTO reponses_supp 
                    (id_reponse, id_ticket, id_user, reponse, date_reponse)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmtInsertReponse->execute([
                    $reponse['id_reponse'], $reponse['id_ticket'],
                    $reponse['id_user'], $reponse['reponse'],
                    $reponse['date_reponse']
                ]);
            }

            // Supprimer les réponses originales
            $stmtDeleteReponses = $pdo->prepare("DELETE FROM reponses WHERE id_ticket = ?");
            $stmtDeleteReponses->execute([$id_ticket]);
        } else {
            // Si pas urgente, on supprime juste les réponses
            $stmtDeleteReponses = $pdo->prepare("DELETE FROM reponses WHERE id_ticket = ?");
            $stmtDeleteReponses->execute([$id_ticket]);
        }

        // Supprimer le ticket (dans tous les cas)
        $stmtDeleteTicket = $pdo->prepare("DELETE FROM tickets WHERE id_ticket = ?");
        $stmtDeleteTicket->execute([$id_ticket]);

        header("Location: HistTicket.php");
        exit();
    } else {
        die('Ticket introuvable.');
    }
} else {
    die('ID ticket manquant.');
}
