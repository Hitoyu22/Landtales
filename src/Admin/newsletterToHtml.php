<?php
session_start();

require "../Structure/Functions/function.php";
require "../Includes/PHPMailerAutoload.php";

if (isset($_SESSION['idclient'])) {

    $pageTitle = "Création d'une newsletter";

    require "../Structure/Bdd/config.php";

    if (isset($_GET['id']) && !empty($_GET['id'])) {

        $newsletterId = $_GET['id'];

        $newsletterInfo = $bdd->prepare("SELECT * FROM newsletter WHERE idnews_letter = ?");
        $newsletterInfo->execute([$newsletterId]);
        $newsletterData = $newsletterInfo->fetch(PDO::FETCH_ASSOC);

        if (isset($newsletterData['text'])) {
            $jsonData = json_decode($newsletterData['text'], true);
        } else {
            $jsonData = null;
        }
        if (isset($newsletterData['helloUser'])) {
            $helloUser = $newsletterData['helloUser'];
        }
        if (isset($newsletterData['title'])) {
            $subjectNewsletter = $newsletterData['title'];
        } else {
            $subjectNewsletter = ""; // Ou une autre valeur par défaut
        }

        if ($jsonData !== null) {
            $htmlData = convertEditorJsToHtml($jsonData);

            $insertHtmlData = $bdd->prepare("UPDATE newsletter SET html = ? WHERE idnews_letter = ?");
            $insertHtmlData->execute([$htmlData, $newsletterId]);

            $newsletterEmailAllowed = $bdd->prepare("SELECT email,pseudo,nom,prenom FROM user WHERE accepte_news_letter = ?");
            $newsletterEmailAllowed->execute([1]);
            $newsletterEmails = $newsletterEmailAllowed->fetchAll(PDO::FETCH_ASSOC);

            foreach ($newsletterEmails as $user) {
                $to = $user['email'];
                $from = 'landtales.website@gmail.com';
                $name = 'Landtales : Newsletter';
                $subj = $subjectNewsletter;
                $msg = $htmlData;
                $error = smtpmailer($to, $from, $name, $subj, $msg);

                    $insertHtmlData = $bdd->prepare("UPDATE newsletter SET statut = ? WHERE idnews_letter = ?");
                    $insertHtmlData->execute([1, $newsletterId]);

                header("Location: newsletterSuccess.php");



            }
        } else {
            header("Location: newsletterError.php");
        }
    }
} else {
    header("Location: ../login.php");
}

?>
