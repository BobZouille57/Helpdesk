<?php
session_start();
require 'PHPMailer-master/src/PHPMailer.php'; 
require 'PHPMailer-master/src/SMTP.php'; 
require 'PHPMailer-master/src/Exception.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mail = new PHPMailer\PHPMailer\PHPMailer();
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'Pierron.clement57@gmail.com'; 
        $mail->Password = 'hyxz subn rcbl zljk'; 
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($user_email, 'Utilisateur de formulaire de contact');
        $mail->addAddress('Pierron.clement57@gmail.com');

        $mail->Subject = 'Nouveau message de contact : ' . $subject;
        $mail->Body    = 'Email de l\'utilisateur : ' . $user_email . "\n" .
                         'Sujet : ' . $subject . "\n" .
                         'Message :\n' . $message;

        if ($mail->send()) {
            $_SESSION['contact_message'] = "Votre message a été envoyé avec succès !";
        } else {
            $_SESSION['contact_message'] = "L'envoi du message a échoué. Erreur : " . $mail->ErrorInfo;
        }
    } catch (Exception $e) {
        $_SESSION['contact_message'] = "Erreur de PHPMailer : " . $mail->ErrorInfo;
    }
}
?>

<?php require_once 'header.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous</title>
    <link rel="stylesheet" href="css/contact.css">
</head>
<body>

<main class="container">
    <h1>Contactez-nous</h1>

    <?php
    if (isset($_SESSION['contact_message'])) {
        echo '<p class="' . (strpos($_SESSION['contact_message'], 'succès') !== false ? 'success' : 'error') . '">' . $_SESSION['contact_message'] . '</p>';
        unset($_SESSION['contact_message']);
    }
    ?>

    <form action="contact.php" method="POST">
        <label for="email">Votre adresse e-mail :</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="subject">Sujet :</label><br>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="message">Votre message :</label><br>
        <textarea id="message" name="message" rows="4" required></textarea><br><br>

        <button type="submit">Envoyer le message</button>
    </form>
</main>

<?php require_once 'footer.php'; ?>

</body>
</html>
