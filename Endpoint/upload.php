<?php
// Autoriser les requêtes CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Récupérer l'ID du voyage à partir des données supplémentaires envoyées par EditorJS
$voyageId = isset($_POST['articleId']) ? $_POST['articleId'] : null;

// Initialiser le tableau de sortie
$output = array();

// Ajouter l'ID du voyage au tableau de sortie pour débogage
$output["voyageId"] = $voyageId;

// Vérifier si un fichier a été envoyé
if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
    // Récupérer le nom du fichier
    $fileName = $_FILES['image']['name'];

    // Vérifier si l'ID du voyage est disponible
    if ($voyageId !== null) {
        // Spécifier le chemin vers le dossier de destination
        $targetDir = "../Ressources/Travel/";

        // Construire le chemin complet du dossier du voyage
        $voyageDir = $targetDir . $voyageId . '/';

        // Vérifier si le dossier du voyage existe, sinon le créer
        if (!file_exists($voyageDir)) {
            mkdir($voyageDir, 0777, true);
        }

        // Déplacer le fichier vers le dossier de destination correspondant à l'ID du voyage
        if (move_uploaded_file($_FILES['image']['tmp_name'], $voyageDir . $fileName)) {
            // Définir le succès et l'URL du fichier dans le tableau de sortie
            $output["success"] = 1;
            $output["file"]["url"] = "https://landtales.freeddns.org/Ressources/Travel/" . $voyageId . "/" . $fileName;
        } else {
            // Si une erreur s'est produite lors du déplacement du fichier, renvoyer une erreur
            $output["error"] = "Erreur lors du déplacement du fichier vers le dossier de destination.";
        }
    } else {
        // Si l'ID du voyage n'est pas disponible, renvoyer une erreur
        $output["error"] = "ID du voyage non spécifié.";
    }
} else {
    // Si aucun fichier n'a été envoyé ou s'il y a eu une erreur lors de l'envoi, renvoyer une erreur
    $output["error"] = "Aucun fichier n'a été téléchargé ou une erreur est survenue lors de l'envoi.";
}

// Afficher le tableau de sortie encodé en JSON
print json_encode($output);
?>
