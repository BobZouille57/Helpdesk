<?php
require_once 'header.php';
require_once 'bdd.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $categorie = trim($_POST['categorie']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['id_users'];

    if (!empty($titre) && !empty($categorie) && !empty($description)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (titre, categorie, description, id_user, statut) VALUES (?, ?, ?, ?, 'En attente')");
            $stmt->execute([$titre, $categorie, $description, $user_id]);
            $successMessage = "Ticket créé avec succès !";
        } catch (PDOException $e) {
            $errorMessage = "Erreur lors de la création du ticket : " . $e->getMessage();
        }
    } else {
        $errorMessage = "Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helpdesk - Création de ticket</title>
    <link rel="stylesheet" href="css/createTicket.css">
</head>
<body>
    <main class="container">
        <div class="ticket-form">
            <h2>Créer un Ticket</h2>

            <?php if (isset($successMessage)) : ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($successMessage); ?></div>
            <?php elseif (isset($errorMessage)) : ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
            <?php endif; ?>

            <form action="CreateTicket.php" method="POST">
                <div class="form-group">
                    <label for="titre">Titre</label>
                    <input type="text" id="titre" name="titre" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="categorie">Catégorie</label>
                    <select id="categorie" name="categorie" class="form-control" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <option value="Problème Technique">Problème Technique</option>
                        <option value="Demande d'information">Demande d'information</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="5" required></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Créer le Ticket</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

<?php require_once 'footer.php'; ?>
