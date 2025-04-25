<?php
require_once 'header.php';
require_once 'bdd.php';

if (!isset($_POST['ticket_id']) || empty($_POST['ticket_id'])) {
    echo "Ticket ID non spécifié.";
    exit();
}

$ticket_id = $_POST['ticket_id'];

$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id_ticket = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    echo "Ticket introuvable.";
    exit();
}

if (!isset($_SESSION['id_users'])) {
    echo "Utilisateur non connecté.";
    exit();
}
$id_user = $_SESSION['id_users'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && !empty($_POST['message'])) {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reponses (id_ticket, id_user, reponse, date_reponse) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$ticket_id, $id_user, $message]);
            $successMessage = "Réponse ajoutée avec succès !";
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de l'ajout de la réponse : " . $e->getMessage();
        }
    } else {
        $errorMessage = "La réponse ne peut pas être vide.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Répondre au Ticket</title>
    <link rel="stylesheet" href="css/addReponse.css">
</head>
<body>
    <main class="container">
        <div class="reponse-form">
            <h2>Répondre au Ticket : <?php echo htmlspecialchars($ticket['titre']); ?></h2>

            <?php if (isset($successMessage)) : ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php elseif (isset($errorMessage)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <form action="addReponse.php" method="POST">
                <textarea name="message" rows="5" class="form-control" placeholder="Votre réponse..." required></textarea>
                <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>">
                <button type="submit" class="btn">Ajouter une réponse</button>
            </form>

            <h3>Historique des réponses</h3>
            <div class="reponses">
                <?php
                $stmt = $pdo->prepare("SELECT reponses.*, users.prenom, users.nom FROM reponses 
                                       JOIN users ON reponses.id_user = users.id_users 
                                       WHERE id_ticket = ? ORDER BY date_reponse ASC");
                $stmt->execute([$ticket_id]);
                $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($reponses) {
                    foreach ($reponses as $reponse) {
                        echo "<div class='reponse-item'>";
                        echo "<b>Répondu par " . htmlspecialchars($reponse['prenom']) . " " . htmlspecialchars($reponse['nom']) . "</b><small> le " . htmlspecialchars($reponse['date_reponse']) . "</small>";
                        echo "<p>" . nl2br(htmlspecialchars($reponse['reponse'])) . "</p>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Aucune réponse pour ce ticket.</p>";
                }
                ?>
            </div>
        </div>
    </main>
</body>
</html>

<?php require_once 'footer.php'; ?>
