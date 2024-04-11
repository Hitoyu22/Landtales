<?php

$targetDir = "../Ressources/Newsletter/";

$output = array();

if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {

    $fileName = $_FILES['image']['name'];

    $newsletterId = isset($_POST['newsletterId']) ? $_POST['newsletterId'] : null;

    if ($newsletterId !== null) {

        $newsletterDir = $targetDir . $newsletterId . '/';


        if (!file_exists($newsletterDir)) {
            mkdir($newsletterDir, 0777, true);
        }


        if (move_uploaded_file($_FILES['image']['tmp_name'], $newsletterDir . $fileName)) {

            $output["success"] = 1;
            $output["file"]["url"] = "http://localhost/src/Ressources/" . $newsletterDir . $fileName;
        }
    } else {

        $output["error"] = "ID du voyage non spécifié.";
    }
} else {

    $output["error"] = "Aucun fichier n'a été téléchargé ou une erreur est survenue lors de l'envoi.";
}


print json_encode($output);
?>
