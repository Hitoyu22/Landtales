<?php

require "../Structure/Functions/function.php";
require "PHPMailerAutoload.php";
require "../Structure/Bdd/config.php";

// Récupération de la durée en BDD pour l'envoi
$timeEmail = $bdd->prepare('SELECT time FROM email_time WHERE id = 1');
$timeEmail->execute();
$timeEmail = $timeEmail->fetch();
$time = $timeEmail['time'];

$date_limite = date('Y-m-d', strtotime("-$time days"));

$recupUserInfo = $bdd->prepare('SELECT email, pseudo, last_login_date, last_notification_date FROM client WHERE last_login_date < :date_limite AND (last_notification_date IS NULL OR last_notification_date < DATE_SUB(NOW(), INTERVAL 7 DAY))');
$recupUserInfo->bindParam(':date_limite', $date_limite);
$recupUserInfo->execute();
$users = $recupUserInfo->fetchAll(PDO::FETCH_ASSOC);
$current_date = date('Y-m-d');

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->SMTPSecure = 'ssl';
$mail->Host = 'smtp.gmail.com';
$mail->Port = 465;
$mail->Username = 'landtales.website@gmail.com';
$mail->Password = 'secret';
$mail->IsHTML(true);
$mail->From = 'landtales.website@gmail.com';
$mail->FromName = 'Landtales';
$mail->Subject = 'Cela fait un petit moment que nous ne vous avons pas vu';
$mail->CharSet = 'UTF-8';

$linkhtml = "https://landtales.freeddns.org/login.php";

$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="div-pleine-page">
    <div class="container text-center">
        <div class="row">
            <div class="d-flex flex-column align-items-right justify-content-center erdiv">
                <div class="text-center mr-0 textee">
                    <h3>Bonjour</h3>
                    <p>Il semblerait que cela fait un petit moment que vous ne vous êtes pas connecté sur Landtales.</p>
                    <p>Nous vous incitons grandement à revenir à revenir nous voir afin que vous puissiez découvrir toutes les nouveautés sur le site.</p>
                    <p>De nouveaux voyages extraordinaires vous attendent, des quiz plus dur les uns que les autres ou bien encore vos amis qui sont sans doutes sans nouvelles de vous depuis un petit moment.</p>
                    <p>Bien cordialement,</p>
                    <p>Landtales</p>
                </div>
                <div class="text-center mt-3 btnediv">
                <a href="{$linkhtml}">Accéder à Landtales</a>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
HTML;

foreach ($users as $user) {
    $pseudo = $user['pseudo'];
    $email = $user['email'];
    $last_notification_date = $user['last_notification_date'];

    if ($last_notification_date === null || strtotime($current_date) - strtotime($last_notification_date) >= 7 * 24 * 3600) {
        $personalizedHtml = str_replace('{PSEUDO}', $pseudo, $html);
        $mail->Body = $personalizedHtml;
        $mail->addBCC($email);

        // Mettre à jour la date de la dernière notification dans la BDD
        $updateLastNotificationDate = $bdd->prepare('UPDATE client SET last_notification_date = :current_date WHERE email = :email');
        $updateLastNotificationDate->bindParam(':current_date', $current_date);
        $updateLastNotificationDate->bindParam(':email', $email);
        $updateLastNotificationDate->execute();
    }
}

// Envoyer l'e-mail
if ($mail->send()) {
    echo "Emails envoyés avec succès.";
} else {
    echo "Erreur lors de l'envoi des emails : " . $mail->ErrorInfo;
}

exit;
