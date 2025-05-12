<?php
session_start();
require_once 'includes/bdd.php';

$pageTitle = 'Connexion';
$message   = '';
$isSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail     = strtolower(trim($_POST['mail'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($mail === '' || $password === '') {
        $message = '❌ Tous les champs sont obligatoires.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id_users, nom, prenom, password, droits FROM users WHERE LOWER(mail) = ?");
            $stmt->execute([$mail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['id_users']   = $user['id_users'];
                $_SESSION['user_name']  = $user['nom'] . ' ' . $user['prenom'];
                $_SESSION['droits']     = $user['droits'];
                header('Location: index.php');
                exit;
            } else {
                $message = '❌ Email ou mot de passe incorrect.';
            }
        } catch (PDOException $e) {
            $message = '❌ Erreur SQL : ' . $e->getMessage();
        }
    }
}

if (isset($_GET['registered']) && $_GET['registered'] === 'true') {
    $message   = '✅ Inscription réussie ! Vous pouvez vous connecter.';
    $isSuccess = true;
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
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label for="mail">Email :</label>
        <input type="email" id="mail" name="mail" required>

        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Se connecter</button>
    </form>

    <a href="forgot_password.php">Mot de passe oublié ?</a>
    <a href="register.php"><u>Pas encore de compte ? Inscrivez-vous</u></a>
</main>
</body>
</html>
