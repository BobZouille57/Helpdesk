<?php
require_once 'bdd.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: login.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT nom, prenom, avatar FROM users WHERE id_users = ?");
    $stmt->execute([$_SESSION['id_users']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_name = $user['prenom'] . ' ' . $user['nom'];
        $avatar = $user['avatar'];
    } else {
        session_destroy();
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    die("❌ Erreur lors de la récupération des informations utilisateur : " . $e->getMessage());
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/header.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<header>
    <div class="header-container">
        <div class="avatar">
            <?php if ($avatar): ?>
                <img src="upload/<?php echo htmlspecialchars($avatar); ?>" alt="Avatar de <?php echo htmlspecialchars($user_name); ?>">
            <?php else: ?>
                <img src="upload/default-avatar.png" alt="Avatar par défaut">
            <?php endif; ?>
        </div>
        <div class="user-info">
            <p>Bonjour, <?php echo htmlspecialchars($user_name); ?> !</p>
        </div>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="ticketMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Menu
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="ticketMenu">
                <li><a class="dropdown-item" href="index.php">Accueil</a></li>
                <li><a class="dropdown-item" href="CreateTicket.php">Créer un ticket</a></li>
                <li><a class="dropdown-item" href="HistTicket.php">Tickets</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Se déconnecter</a></li>
            </ul>
        </li>
    </div>
</header>
