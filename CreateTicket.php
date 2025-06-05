<?php
require_once 'includes/header.php';
require_once 'includes/bdd.php';
require 'PHPMailer-master/src/PHPMailer.php'; 
require 'PHPMailer-master/src/SMTP.php'; 
require 'PHPMailer-master/src/Exception.php'; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $categorie = trim($_POST['categorie']);
    $priorite = trim($_POST['priorite']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['id_users'];

    if (!empty($titre) && !empty($categorie) && !empty($priorite) && !empty($description)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tickets (titre, categorie, description, id_user, statut, priorite) VALUES (?, ?, ?, ?, 'En attente', ?)");
            $stmt->execute([$titre, $categorie, $description, $user_id, $priorite]);
            $ticketId = $pdo->lastInsertId();
            $successMessage = "Ticket créé avec succès ! Vous allez être redirigé...";

            // Envoi du mail si priorité urgente
            if ($priorite === "Urgente") {
                // Récupère tous les mails des admins
                $stmtAdmin = $pdo->query("SELECT mail FROM users WHERE droits = 1");
                $admins = $stmtAdmin->fetchAll(PDO::FETCH_ASSOC);

                $mail = new PHPMailer(true);
                $mail->CharSet = 'UTF-8';

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'Pierron.clement57@gmail.com'; 
                    $mail->Password = 'hyxz subn rcbl zljk'; 
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('noreply@helpdesk.com', 'HelpDesk');
                    $mail->Subject = "🚨 Ticket URGENT créé";
                    $mail->Body = "Un ticket urgent a été soumis par un utilisateur.\n\n"
                                . "Titre : $titre\n"
                                . "Catégorie : $categorie\n"
                                . "Description : $description\n"
                                . "Lien vers le ticket : http://helpdesk.clementpierron.fr/TicketDetails.php?id=$ticketId";

                    foreach ($admins as $admin) {
                        $mail->addAddress($admin['mail']);
                    }

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Erreur PHPMailer : " . $mail->ErrorInfo);
                }
            }

            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'TicketDetails.php?id=" . $ticketId . "';
                    }, 3000);
                  </script>";

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
    <link rel="stylesheet" href="/assets/css/CreateTicket.css">
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
                    <label for="priorite">Priorité</label>
                    <select id="priorite" name="priorite" class="form-control" required>
                        <option value="">Sélectionnez une priorité</option>
                        <option value="Basse">Basse</option>
                        <option value="Moyenne">Moyenne</option>
                        <option value="Haute">Haute</option>
                        <option value="Urgente">Urgente</option>
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

<?php require_once 'includes/footer.php'; ?>
