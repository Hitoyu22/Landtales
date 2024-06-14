<?php

// Fichier pour le téléchargement du fichier de logs.

if (isset($_GET['file'])) {
    $filename = urldecode($_GET['file']);
    $filepath = "Admin/Structures/Logs/" . $filename;

    if (file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        ob_clean();
        flush();

        readfile($filepath);
        exit;
    } else {
        echo "Désolé, le fichier n'existe pas.";
    }
} else {
    echo "Aucun fichier spécifié.";
}
?>
