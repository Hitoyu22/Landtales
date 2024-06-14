<?php
$logsFile = '../Admin/Structures/Logs/log.txt';

if (file_exists($logsFile)) {
    $latestLogs = 1;
    while (file_exists('Admin/Structures/Logs/log' . $latestLogs . '.txt')) {
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

    $newLogsFile = fopen($logsFile, 'w');
    fclose($newLogsFile);

    echo "Archivage des logs effectué avec succès.";
} else {
    echo "Le fichier de logs n'existe pas.";
}
?>
