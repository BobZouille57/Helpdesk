<?php
require_once 'bdd.php';

try {
    $stmt = $pdo->prepare("SELECT nom, prenom, avatar FROM users WHERE id_users = ?");
    if (!empty($_SESSION['id_users'])) {
            $stmt->execute([$_SESSION['id_users']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $user_name = $user['prenom'] . ' ' . $user['nom'];
            $avatar = $user['avatar'];
        }
    }
} catch (PDOException $e) {
    die("❌ Erreur lors de la récupération des informations utilisateur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HelpDesk</title>

    <!-- Lien vers le fichier CSS de Bootstrap -->
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"> -->

    <!-- Lien vers ton fichier CSS personnalisé -->
    <link rel="stylesheet" href="/assets/css/header.css">

    <!-- Script JS de Bootstrap pour le bon fonctionnement du dropdown -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> -->
</head>
<body>
<header>
    <div class="header-container">
        <div class="avatar">
            <?php if (!empty($_SESSION['id_users'])) { ?>
                <?php if ($avatar): ?>
                    <img src="/assets/upload/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar de <?php echo htmlspecialchars($user_name); ?>" width="50" class="rounded-circle">
                <?php else: ?>
                    <img src="/assets/upload/default-avatar.png" alt="Avatar par défaut" width="50" class="rounded-circle">
                <?php endif; ?>
            <?php } ?>
        </div>

        <?php if (!empty($_SESSION['id_users'])) { ?>
            <div class="user-info">
                <p>Bonjour, <?php echo htmlspecialchars($user_name); ?> !</p>
            </div>
        <?php } ?>

        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Menu
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><a class="dropdown-item" href="index.php">Accueil</a></li>
                <li><a class="dropdown-item" href="CreateTicket.php">Créer un ticket</a></li>
                <li><a class="dropdown-item" href="HistTicket.php">Tickets</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Se déconnecter</a></li>
            </ul>
        </div>
    </div>
</header>
</body>
</html>
