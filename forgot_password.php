<?php
session_start();
require_once 'includes/bdd.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['mail']));

    $stmt = $pdo->prepare("SELECT id_users, prenom FROM users WHERE LOWER(mail)=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token   = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 3600);
        $stmt = $pdo->prepare("INSERT INTO password_resets (id_user, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user['id_users'], $token, $expires]);

        $protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        $link = "{$protocol}://{$_SERVER['HTTP_HOST']}/reset_password.php?token={$token}";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'Pierron.clement57@gmail.com';
            $mail->Password   = 'hyxz subn rcbl zljk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('Pierron.clement57@gmail.com', 'HelpDesk');
            $mail->addAddress($email, $user['prenom']);
            $mail->Subject = 'Réinitialisation de mot de passe';
            $mail->isHTML(true);
            $mail->Body = "
              <p>Bonjour {$user['prenom']},</p>
              <p>Pour réinitialiser votre mot de passe, cliquez sur ce lien (valable 1 h) :</p>
              <p><a href=\"{$link}\">Réinitialiser mon mot de passe</a></p>
            ";

            $mail->send();
            $message = "✅ Un email de réinitialisation vient de vous être envoyé.";
        } catch (Exception $e) {
            $message = "❌ Erreur d'envoi : {$mail->ErrorInfo}";
        }
    } else {
        $message = "❌ Aucun compte associé à cet email.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>HelpDesk - MDP oublié</title>
  <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>
  <?php if ($message): ?><p><?= htmlspecialchars($message) ?></p><?php endif; ?>
  <form method="POST">
    <label>Email :</label>
    <input type="email" name="mail" required>
    <button type="submit">Envoyer le lien de réinitialisation</button>
  </form>
  <p><a href="login.php">← Retour à la connexion</a></p>
</body>
</html>
