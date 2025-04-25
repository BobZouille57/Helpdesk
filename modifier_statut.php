<?php
require_once 'header.php';
require_once 'bdd.php';

if (!isset($_SESSION['id_users']) || $_SESSION['droits'] != 1) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_ticket = (int) $_GET['id'];

    // On récupère le ticket
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_ticket = ?");
    $stmt->execute([$id_ticket]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        die('Ticket introuvable.');
    }

    if (isset($_POST['statut'])) {
        // Mise à jour du statut
        $nouveauStatut = $_POST['statut'];
        $stmt = $pdo->prepare("UPDATE tickets SET statut = ? WHERE id_ticket = ?");
        $stmt->execute([$nouveauStatut, $id_ticket]);

        // Message de confirmation
        $message = "Statut mis à jour avec succès !";

        // Rediriger vers HistTicket.php avec un message
        header("Location: HistTicket.php?message=" . urlencode($message));
        exit();
    }
} else {
    die('ID ticket manquant.');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le Statut du Ticket</title>
    <link rel="stylesheet" href="css/modifier_statut.css">
</head>
<body>
    <div class="container">
        <h1>Modifier le Statut du Ticket</h1>

        <?php if (isset($message)): ?>
            <p class="success"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="statut">Statut :</label>
            <select name="statut" required>
                <option value="En attente" <?php echo ($ticket['statut'] == 'En attente') ? 'selected' : ''; ?>>En attente</option>
                <option value="En cours" <?php echo ($ticket['statut'] == 'En cours') ? 'selected' : ''; ?>>En cours</option>
                <option value="Résolu" <?php echo ($ticket['statut'] == 'Résolu') ? 'selected' : ''; ?>>Résolu</option>
            </select>
            <button type="submit">Mettre à jour</button>
        </form>

        <a href="HistTicket.php">Retour à l'historique</a>
    </div>
</body>
</html>

<?php require_once 'footer.php'; ?>
