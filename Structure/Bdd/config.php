<?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=landtales', 'landtales', 'Secret');
}
catch(Exception $e)
{
    die('Erreur de bdd:' . $e->getMessage());
}


?>


