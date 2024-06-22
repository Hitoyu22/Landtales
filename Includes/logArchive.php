<?php
$logsFile = '../Admin/Structures/Logs/log.txt';

if (file_exists($logsFile)) {
    $latestLogs = 1;
    while (file_exists('../Admin/Structures/Logs/log' . $latestLogs . '.txt')) {
        $latestLogs++;
    }

    for ($i = $latestLogs - 1; $i >= 1; $i--) {
        $currentFileName = '../Admin/Structures/Logs/log' . $i . '.txt';
        $newFileName = '../Admin/Structures/Logs/log' . ($i + 1) . '.txt';

        if (file_exists($currentFileName)) {
            rename($currentFileName, $newFileName);
        }
    }

    rename($logsFile, '../Admin/Structures/Logs/log1.txt');

    // Création du nouveau fichier de log
    $newLogsFile = fopen($logsFile, 'w');
    if ($newLogsFile) {
        // Définir les permissions du fichier à 644
        chmod($logsFile, 0644);
        fclose($newLogsFile);
        echo "Archivage des logs effectué avec succès.";
    } else {
        echo "Erreur lors de la création du nouveau fichier de log.";
    }
} else {
    echo "Le fichier de logs n'existe pas.";
}
?>
