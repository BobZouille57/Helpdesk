<?php
require_once 'includes/header.php';
require_once 'includes/bdd.php';

$stmt = $pdo->prepare("SELECT nom, prenom, mail, avatar FROM users WHERE id_users = ?");
$stmt->execute([$_SESSION['id_users']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_info'])) {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        if ($nom && $prenom) {
            $stmt = $pdo->prepare("UPDATE users SET nom = ?, prenom = ? WHERE id_users = ?");
            $stmt->execute([$nom, $prenom, $_SESSION['id_users']]);
            $_SESSION['user_name'] = $nom . ' ' . $prenom;
            $message = "✅ Informations mises à jour.";
            $user['nom'] = $nom;
            $user['prenom'] = $prenom;
        } else {
            $message = "❌ Veuillez remplir tous les champs.";
        }
    }

    if (isset($_POST['update_password'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        if ($newPassword === $confirmPassword && !empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id_users = ?");
            $stmt->execute([$hashedPassword, $_SESSION['id_users']]);
            $message = "✅ Mot de passe mis à jour.";
        } else {
            $message = "❌ Les mots de passe ne correspondent pas ou sont vides.";
        }
    }

    if (isset($_POST['delete_account'])) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id_users = ?");
        $stmt->execute([$_SESSION['id_users']]);
        session_destroy();
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk - Mon Compte</title>
    <link rel="stylesheet" href="/assets/css/account.css">
</head>
<body>
<main class="container">
    <div class="account-section">
        <h2>Mon Compte</h2>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <div class="profile-info">
            <?php if (!empty($user['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="avatar">
            <?php endif; ?>
            <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
            <p><strong>Prénom :</strong> <?php echo htmlspecialchars($user['prenom']); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars($user['mail']); ?></p>
        </div>

        <form method="POST" class="form-section">
            <h3>Modifier les informations</h3>
            <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            <input type="text" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            <button type="submit" name="update_info" class="action-btn btn-modifier">Enregistrer</button>
        </form>

        <form method="POST" class="form-section">
            <h3>Changer de mot de passe</h3>
            <input type="password" name="new_password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
            <button type="submit" name="update_password" class="action-btn btn-modifier">Changer</button>
        </form>

        <form method="POST" onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer votre compte ?');">
            <button type="submit" name="delete_account" class="action-btn btn-supprimer">Supprimer le compte</button>
        </form>
    </div>
</main>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>
