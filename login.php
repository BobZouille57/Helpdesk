<?php
require_once 'includes/bdd.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if (!empty($_POST['mail']) && !empty($_POST['password'])) {
        $mail = strtolower(trim($_POST['mail']));
        $password = $_POST['password'];

        try {
            // Récupération des informations de l'utilisateur (y compris droits)
            $stmt = $pdo->prepare("SELECT id_users, nom, prenom, password, droits FROM users WHERE LOWER(mail) = ?");
            $stmt->execute([$mail]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Connexion réussie
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['id_users'] = $user['id_users'];
                $_SESSION['user_name'] = $user['nom'] . ' ' . $user['prenom'];
                $_SESSION['droits'] = $user['droits'];

                header("Location: index.php");  // Redirection vers la page principale
                exit();
            } else {
                $message = "❌ Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $message = "❌ Erreur SQL : " . $e->getMessage();
        }
    } else {
        $message = "❌ Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk - Connexion</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
    <header>
        <h1>HelpDesk | Identification</h1>
    </header>
    <main>
        <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php if (isset($_GET['registered']) && $_GET['registered'] == 'true'): ?>
            <p class="success">✅ Compte créé avec succès ! Vous pouvez maintenant vous connecter.</p>
        <?php endif; ?>
        <section>
            <h2>Connexion</h2>
            <form method="POST">
                <input type="hidden" name="login" value="1">
                <label for="mail">Email :</label>
                <input type="email" name="mail" required>
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>
        </section>
        <section>
            <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous ici</a></p>
        </section>
    </main>
</body>
</html>
