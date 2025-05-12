<?php
session_start();
require_once 'includes/bdd.php';

$pageTitle = 'Réinitialisation';
$message   = '';
$isSuccess = false;
$valid     = false;
$token     = $_GET['token'] ?? '';

if ($token) {
    $stmt = $pdo->prepare("
        SELECT pr.id_reset, pr.expires_at, u.id_users
          FROM password_resets pr
          JOIN users u ON pr.id_user = u.id_users
         WHERE pr.token = ?
    ");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && strtotime($row['expires_at']) > time()) {
        $valid = true;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pass1 = $_POST['new_password'] ?? '';
            $pass2 = $_POST['confirm_password'] ?? '';
            if ($pass1 === '' || $pass2 === '') {
                $message = '❌ Tous les champs sont obligatoires.';
            } elseif ($pass1 !== $pass2) {
                $message = '❌ Les mots de passe ne correspondent pas.';
            } else {
                $hash = password_hash($pass1, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id_users = ?");
                $stmt->execute([$hash, $row['id_users']]);
                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE id_reset = ?");
                $stmt->execute([$row['id_reset']]);
                $message   = '✅ Mot de passe réinitialisé. Vous pouvez vous <a href="login.php">connecter</a>.';
                $isSuccess = true;
                $valid     = false;
            }
        }
    } else {
        $message = '❌ Lien invalide ou expiré.';
    }
} else {
    $message = '❌ Aucun jeton fourni.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk | <?= $pageTitle ?></title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
<header><h1><?= $pageTitle ?></h1></header>
<main>
    <?php if ($message): ?>
        <p class="<?= $isSuccess ? 'success' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <?php if ($valid): ?>
        <form method="POST">
            <label for="new_password">Nouveau mot de passe :</label>
            <input type="password" id="new_password" name="new_password" required>

            <label for="confirm_password">Confirmer :</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <button type="submit">Réinitialiser</button>
        </form>
    <?php endif; ?>

    <a href="login.php">← Retour à la connexion</a>
</main>
</body>
</html>
