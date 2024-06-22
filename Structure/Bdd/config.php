<?php
try {
    $bdd = new PDO('mysql:host=landtales.freeddns.org;dbname=landtales', 'landtales', 'HbGurSuFEnhaqP99111G');
}
catch(Exception $e)
{
    die('Erreur de bdd:' . $e->getMessage());
}


?>


