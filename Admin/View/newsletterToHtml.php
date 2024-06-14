<?php
session_start();

require "Structure/Functions/function.php";
require "Includes/PHPMailerAutoload.php";

if (isset($_SESSION['idclient'])) {
    checkAdminRole();

    $pageTitle = "Création d'une newsletter";

    require "Structure/Bdd/config.php";

    if (isset($_GET['id']) && !empty($_GET['id'])) {

        $newsletterId = $_GET['id'];

        $newsletterInfo = $bdd->prepare("SELECT content, client_greeting, title FROM newsletter WHERE id = ?");
        $newsletterInfo->execute([$newsletterId]);
        $newsletterData = $newsletterInfo->fetch(PDO::FETCH_ASSOC);

        if (isset($newsletterData['content'])) {
            $jsonData = json_decode($newsletterData['content'], true);
        } else {
            $jsonData = null;
        }
        if (isset($newsletterData['client_greeting'])) {
            $helloUser = $newsletterData['client_greeting'];
        }
        if (isset($newsletterData['title'])) {
            $subjectNewsletter = $newsletterData['title'];
        } else {
            $subjectNewsletter = "Une nouvelle newsletter Landtales"; // Ou une autre valeur par défaut
        }

        if ($jsonData !== null) {
            $htmlData = convertEditorJsToHtml($jsonData);

            $insertHtmlData = $bdd->prepare("UPDATE newsletter SET html = ? WHERE id = ?");
            $insertHtmlData->execute([$htmlData, $newsletterId]);

            $newsletterEmailAllowed = $bdd->prepare("SELECT email FROM client WHERE news_letter_accepted = ?");
            $newsletterEmailAllowed->execute([1]);
            $newsletterEmails = $newsletterEmailAllowed->fetchAll(PDO::FETCH_COLUMN);

            switch ($helloUser) {
                case 1:
                    $greeting = "Bonjour";
                    break;
                case 2:
                    $greeting = "Salut";
                    break;
                case 3:
                    $greeting = "Hey";
                    break;
                case 4:
                    $greeting = "Yo";
                    break;
                case 5:
                    $greeting = "Hello";
                    break;
                default:
                    $greeting = "Bonjour";
            }

            $html = '<!doctype html><html lang="fr"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet"><style>body { font-family: \'Poppins\', sans-serif; }img {width: 100%;}</style></head><body>';
            $endHtml = '</body></html>';

            $greetingHtml = '<h1>' . $greeting . '</h1>';
            $completeHtml = $html . $greetingHtml . $htmlData . $endHtml;


            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'ssl';
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 465;
            $mail->Username = 'landtales.website@gmail.com';
            $mail->Password = 'secret';
            $mail->IsHTML(true);
            $mail->From = "landtales.website@gmail.com";
            $mail->FromName = 'Landtales : Newsletter';
            $mail->Subject = $subjectNewsletter;
            $mail->Body = $completeHtml;
            $mail->CharSet = 'UTF-8';

            // Ajouter tous les destinataires
            foreach ($newsletterEmails as $email) {
                $mail->addBCC($email);
            }

            // Envoyer l'e-mail
            if ($mail->send()) {
                // Mettre à jour le statut de la newsletter
                $insertHtmlData = $bdd->prepare("UPDATE newsletter SET newsletter_status = ? WHERE id = ?");
                $insertHtmlData->execute([1, $newsletterId]);

                header("Location: newsletterSuccess.php");
            } else {
                echo "Erreur lors de l'envoi du message : " . $mail->ErrorInfo;
            }
        } else {
            header("Location: newsletterError.php");
        }
    }
} else {
    header("Location: ../login.php");
}
?>
