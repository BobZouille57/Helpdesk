<?php
require_once 'includes/header.php';
require_once 'includes/bdd.php';



$stmt = null;

// Si l'utilisateur est administrateur (droits = 1), on récupère tous les tickets
if ($_SESSION['droits'] == 1) {
    $stmt = $pdo->prepare("SELECT id_ticket, titre, categorie, date_creation, statut, id_user FROM tickets");
    $stmt->execute();
} else {
    // Si c'est un utilisateur normal, on récupère uniquement ses tickets
    $stmt = $pdo->prepare("SELECT id_ticket, titre, categorie, date_creation, statut, id_user FROM tickets WHERE id_user = ?");
    $stmt->execute([$_SESSION['id_users']]);
}

$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk - Historique des Tickets</title>
    <link rel="stylesheet" href="/assets/css/HistTicket.css">
</head>
<body>
    <main class="container">
        <div class="ticket-list">
            <h2>Historique de vos Tickets</h2>

            <?php if (count($tickets) > 0): ?>
                <table class="ticket-table">
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Date de création</th>
                            <th>Statut</th>
                            <?php if ($_SESSION['droits'] == 1): ?> <!-- Si administrateur -->
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <tr>
                                <td><a href="TicketDetails.php?id=<?php echo $ticket['id_ticket']; ?>"><?php echo htmlspecialchars($ticket['titre']); ?></a></td>
                                <td><?php echo htmlspecialchars($ticket['categorie']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($ticket['date_creation'])); ?></td>
                                <td><?php echo htmlspecialchars($ticket['statut']); ?></td>
                                <?php if ($_SESSION['droits'] == 1): ?> <!-- Si administrateur -->
                                    <td>
                                        <a href="modifier_statut.php?id=<?php echo $ticket['id_ticket']; ?>">Modifier Statut</a> | 
                                        <a href="supprimer_ticket.php?id=<?php echo $ticket['id_ticket']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce ticket ?')">Supprimer</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucun ticket trouvé.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>
