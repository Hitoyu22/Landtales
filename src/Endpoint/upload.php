<?php
// Spécifiez le chemin vers le dossier de destination
$targetDir = "../Ressources/Travel/";

// Initialisez le tableau de sortie
$output = array();

// Vérifiez si un fichier a été envoyé
if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
    // Récupérer le nom du fichier
    $fileName = $_FILES['image']['name'];

    // Récupérer l'ID du voyage à partir des données supplémentaires envoyées par EditorJS
    $voyageId = isset($_POST['articleId']) ? $_POST['articleId'] : null;

    // Vérifier si l'ID du voyage est disponible
    if ($voyageId !== null) {
        // Construire le chemin complet du dossier du voyage
        $voyageDir = $targetDir . $voyageId . '/';

        // Vérifier si le dossier du voyage existe, sinon le créer
        if (!file_exists($voyageDir)) {
            mkdir($voyageDir, 0777, true);
        }

        // Déplacer le fichier vers le dossier de destination correspondant à l'ID du voyage
        if (move_uploaded_file($_FILES['image']['tmp_name'], $voyageDir . $fileName)) {
            // Définissez le succès et l'URL du fichier dans le tableau de sortie
            $output["success"] = 1;
            $output["file"]["url"] = "http://localhost/src/Ressources/" . $voyageDir . $fileName;
        }
    } else {
        // Si l'ID du voyage n'est pas disponible, renvoyer une erreur
        $output["error"] = "ID du voyage non spécifié.";
    }
} else {
    // Si aucun fichier n'a été envoyé ou s'il y a eu une erreur lors de l'envoi, renvoyer une erreur
    $output["error"] = "Aucun fichier n'a été téléchargé ou une erreur est survenue lors de l'envoi.";
}

// Affichez le tableau de sortie encodé en JSON
print json_encode($output);
?>
