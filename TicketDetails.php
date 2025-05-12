<?php
require_once 'includes/header.php';
require_once 'includes/bdd.php';



if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];

    if ($_SESSION['droits'] == 1) {
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_ticket = ?");
        $stmt->execute([$ticket_id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_ticket = ? AND id_user = ?");
        $stmt->execute([$ticket_id, $_SESSION['id_users']]);
    }

    $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        echo "Ticket non trouvé ou vous n'avez pas accès à ce ticket.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && !empty($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reponses (id_ticket, id_user, reponse, date_reponse) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$ticket_id, $_SESSION['id_users'], $message]);
            $successMessage = "Réponse ajoutée avec succès !";
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de l'ajout de la réponse : " . $e->getMessage();
        }
    } else {
        $errorMessage = "La réponse ne peut pas être vide.";
    }
}

$stmt = $pdo->prepare("SELECT reponses.*, users.prenom, users.nom, users.droits FROM reponses 
                       JOIN users ON reponses.id_user = users.id_users 
                       WHERE reponses.id_ticket = ? 
                       ORDER BY date_reponse ASC");
$stmt->execute([$ticket_id]);
$reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['supprimer_reponse']) && isset($_POST['id_reponse'])) {
    if ($_SESSION['droits'] == 1) {
        $stmt = $pdo->prepare("DELETE FROM reponses WHERE id_reponse = ?");
        $stmt->execute([$_POST['id_reponse']]);
        header("Location: TicketDetails.php?id=" . $ticket_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk - Détails Ticket</title>
    <link rel="stylesheet" href="/assets/css/TicketDetails.css">
</head>
<body>
    <main class="container">
        <div class="ticket-details">
            <h2>Détails du Ticket: <?php echo htmlspecialchars($ticket['titre']); ?></h2>
            <p><strong>Catégorie:</strong> <?php echo htmlspecialchars($ticket['categorie']); ?></p>
            <p><strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($ticket['date_creation'])); ?></p>
            <p><strong>Statut:</strong> <?php echo htmlspecialchars($ticket['statut']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($ticket['description'])); ?></p>

            <?php if ($ticket['statut'] != 'Résolu'): ?>
                <form action="TicketDetails.php?id=<?php echo $ticket_id; ?>" method="POST">
                    <textarea name="message" rows="5" class="form-control" placeholder="Votre réponse..." required></textarea>
                    <button type="submit" class="btn">Ajouter une réponse</button>
                </form>
            <?php else: ?>
                <div class="ticket-resolu-message">
                    <i class="fas fa-check-circle"></i> <strong>Le ticket est résolu.</strong>
                </div>
            <?php endif; ?>

            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php elseif (isset($errorMessage)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <h3>Historique des réponses</h3>
            <div class="reponses">
                <?php if ($reponses): ?>
                    <?php foreach ($reponses as $reponse): ?>
                        <div class="reponse-item">
                            <b>
                                <?php echo htmlspecialchars($reponse['prenom']) . " " . htmlspecialchars($reponse['nom']); ?>
                                <?php if ($reponse['droits'] == 1): ?>
                                    <span class="badge badge-admin">Admin</span>
                                <?php else: ?>
                                    <span class="badge badge-user">User</span>
                                <?php endif; ?>
                            </b>
                            <small> le <?php echo date('d/m/Y H:i', strtotime($reponse['date_reponse'])); ?></small>

                            <?php if ($_SESSION['droits'] == 1): ?>
                                <form method="POST" style="display:inline; float: right;">
                                    <input type="hidden" name="id_reponse" value="<?php echo $reponse['id_reponse']; ?>">
                                    <input type="hidden" name="supprimer_reponse" value="1">
                                    <button type="submit" class="btn btn-trash" title="Supprimer la réponse">
                                        🗑️
                                    </button>
                                </form>
                            <?php endif; ?>

                            <p><?php echo nl2br(htmlspecialchars($reponse['reponse'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune réponse pour ce ticket.</p>
                <?php endif; ?>
            </div>

            <?php if ($ticket['statut'] == 'Résolu' && $_SESSION['droits'] == 2): ?>
                <form method="POST">
                    <input type="hidden" name="fermer_ticket" value="1">
                    <button type="submit" class="btn btn-danger">Fermer définitivement le ticket</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>
