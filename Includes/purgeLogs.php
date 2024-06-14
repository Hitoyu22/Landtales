<?php

include "../Structure/Bdd/config.php";

try {

    $deleteLogs = $bdd->prepare("DELETE FROM log WHERE log_datetime <= NOW() - INTERVAL 2 WEEK");
    $deleteLogs->execute();

    echo "Les enregistrements plus vieux ou égaux à deux semaines ont été supprimés avec succès.";
} catch (PDOException $e) {
    echo "Erreur de connexion ou d'exécution : " . $e->getMessage();
}

exit();

