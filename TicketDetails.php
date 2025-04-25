<?php
require_once 'header.php';
require_once 'bdd.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_ticket = ? AND id_user = ?");
    $stmt->execute([$ticket_id, $_SESSION['id_users']]);
    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        echo "Ticket non trouvé ou vous n'avez pas accès à ce ticket.";
        exit();
    }
}

$stmt = $pdo->prepare("SELECT * FROM reponses WHERE id_ticket = ?");
$stmt->execute([$ticket_id]);
$reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Ticket</title>
    <link rel="stylesheet" href="css/ticketDetails.css">
</head>
<body>
    <main class="container">
        <div class="ticket-details">
            <h2>Détails du Ticket: <?php echo htmlspecialchars($ticket['titre']); ?></h2>
            <p><strong>Catégorie:</strong> <?php echo htmlspecialchars($ticket['categorie']); ?></p>
            <p><strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($ticket['date_creation'])); ?></p>
            <p><strong>Statut:</strong> <?php echo htmlspecialchars($ticket['statut']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>

            <h3>Conversation</h3>
            <?php if (count($reponses) > 0): ?>
                <ul class="reponses-list">
                    <?php foreach ($reponses as $reponse): ?>
                        <li>
                            <p><strong><?php echo $reponse['id_user']; ?>:</strong> <?php echo nl2br(htmlspecialchars($reponse['reponse'])); ?></p>
                            <p><small><?php echo date('d/m/Y H:i', strtotime($reponse['date_reponse'])); ?></small></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucune réponse de l'administrateur pour ce ticket.</p>
            <?php endif; ?>

            <form action="addReponse.php" method="POST">
                <textarea name="message" rows="5" class="form-control" placeholder="Votre réponse..." required></textarea>
                <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                <button type="submit" class="btn">Ajouter une réponse</button>
            </form>
        </div>
    </main>
</body>
</html>

<?php require_once 'footer.php'; ?>
