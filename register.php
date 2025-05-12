<?php
require_once 'includes/bdd.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    if (!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['mail']) && !empty($_POST['password'])) {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $mail = strtolower(trim($_POST['mail']));
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $avatarPath = '';

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $avatar = $_FILES['avatar'];
            $avatarExtension = pathinfo($avatar['name'], PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array(strtolower($avatarExtension), $allowedExtensions)) {
                $avatarName = 'avatar_' . time() . '.' . $avatarExtension;
                $uploadDirectory = __DIR__ . '/assets/upload/';
                $uploadPath = $uploadDirectory . $avatarName;

                if (move_uploaded_file($avatar['tmp_name'], $uploadPath)) {
                    $avatarPath = 'assets/upload/' . $avatarName;
                } else {
                    $message = "❌ Erreur lors du téléchargement de l'avatar.";
                }
            } else {
                $message = "❌ Format d'avatar invalide. Seules les images JPG, JPEG, PNG et GIF sont autorisées.";
            }
        }

        try {
            $stmt = $pdo->prepare("SELECT id_droits FROM droits WHERE libelle_droits = 'User'");
            $stmt->execute();
            $droits = $stmt->fetchColumn();

            if (!$droits) {
                $message = "❌ Erreur : le rôle 'User' n'existe pas.";
            } else {
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE LOWER(mail) = ?");
                $stmt->execute([$mail]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    $message = "❌ Cet email est déjà utilisé.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, mail, password, droits, avatar) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$nom, $prenom, $mail, $password, $droits, $avatarPath]);

                    header("Location: login.php?registered=true");
                    exit();
                }
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
    <title>HelpDesk - Inscription</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
    <header>
        <h1>HelpDesk | Inscription</h1>
    </header>
    <main>
        <?php if (!empty($message)): ?>
            <p class="error"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <section>
            <h2>Inscription</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="register" value="1">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" required>
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" required>
                <label for="mail">Email :</label>
                <input type="email" name="mail" required>
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" required>
                <label for="avatar">Avatar :</label>
                <input type="file" name="avatar" accept="image/*">
                <button type="submit">S'inscrire</button>
            </form>
        </section>
    </main>
</body>
</html>
