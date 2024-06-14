<?php

if (isset($_GET['file']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $filename = $_GET['file'];
    $file_path = "Admin/Structures/Ticket/$id/$filename";

    if (file_exists($file_path)) {
        $file_mime = mime_content_type($file_path);

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $file_mime);
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        ob_clean();
        flush();
        readfile($file_path);
        exit;
    } else {
        echo "Désolé, le fichier '$filename' pour le ticket numéro '$id' n'existe pas.";
    }
} else {
    echo "Aucun fichier spécifié ou ID de ticket fourni.";
}
?>
