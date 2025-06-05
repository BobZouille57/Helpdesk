<?php
require_once 'includes/header.php';
require_once 'includes/bdd.php';

$stmt = null;

if ($_SESSION['droits'] == 1) {
    $stmt = $pdo->prepare("
        SELECT id_ticket, titre, categorie, date_creation, statut, id_user, priorite 
        FROM tickets 
        ORDER BY FIELD(priorite, 'Urgente', 'Haute', 'Moyenne', 'Basse'), date_creation DESC
    ");
    $stmt->execute();
} else {
    $stmt = $pdo->prepare("
        SELECT id_ticket, titre, categorie, date_creation, statut, id_user, priorite 
        FROM tickets 
        WHERE id_user = ? 
        ORDER BY FIELD(priorite, 'Urgente', 'Haute', 'Moyenne', 'Basse'), date_creation DESC
    ");
    $stmt->execute([$_SESSION['id_users']]);
}

$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>HelpDesk - Historique des Tickets</title>
    <link rel="stylesheet" href="/assets/css/HistTicket.css" />
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
                            <th>Priorité</th>
                            <?php if ($_SESSION['droits'] == 1): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tickets as $ticket): ?>
                            <?php
                                $class = '';
                                switch ($ticket['priorite']) {
                                    case 'urgente':
                                        $class = 'urgent';
                                        break;
                                    case 'haute':
                                        $class = 'haute';
                                        break;
                                    case 'moyenne':
                                        $class = 'moyenne';
                                        break;
                                    case 'basse':
                                        $class = 'basse';
                                        break;
                                }
                            ?>
                            <tr class="<?php echo $class; ?>">
                                <td><a href="TicketDetails.php?id=<?php echo $ticket['id_ticket']; ?>"><?php echo htmlspecialchars($ticket['titre']); ?></a></td>
                                <td><?php echo htmlspecialchars($ticket['categorie']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($ticket['date_creation'])); ?></td>
                                <td><?php echo htmlspecialchars($ticket['statut']); ?></td>
                                <td><?php echo htmlspecialchars($ticket['priorite']); ?></td>
                                <?php if ($_SESSION['droits'] == 1): ?>
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
