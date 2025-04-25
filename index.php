<?php
require_once 'header.php';
require_once 'bdd.php'; 

if (!isset($_SESSION['id_users'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk - Accueil</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="content-wrapper">
        <main class="container">
            <div class="welcome-section">
                <h2>Bienvenue sur votre espace</h2>
                <p>Gérez vos tickets facilement et accédez à votre historique.</p>
            </div>

            <div class="buttons">
                <a href="CreateTicket.php" class="btn">Créer un Ticket</a>
                <a href="HistTicket.php" class="btn secondary">Historique des Tickets</a>
            </div>
        </main>
    </div>

    <?php require_once 'footer.php'; ?>
</body>
</html>
