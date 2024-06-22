<?php
session_start();
require "Structure/Bdd/config.php";

if (isset($_GET['idclient']) AND !empty($_GET['idclient']) AND isset($_GET['account_verification_key']) AND !empty($_GET['account_verification_key'])){

    $getId = $_GET['idclient'];
    $getCle = $_GET['account_verification_key'];
    $recupUser = $bdd->prepare('SELECT id, account_verificated FROM client WHERE id = ? AND account_verification_key = ?');
    $recupUser->execute(array($getId, $getCle));
    if ($recupUser->rowCount() > 0){
        $userInfo = $recupUser->fetch();
        if($userInfo['account_verificated'] != 1) {
            $updateConfirmation = $bdd->prepare('UPDATE client SET account_verificated = ? WHERE id = ?');
            $updateConfirmation->execute(array(1, $getId));

            $userFolder = "Ressources/User/" . $getId;
            if (!is_dir($userFolder)) {
                mkdir($userFolder, 0777, true);
            }




            header('Location: accountConfirm.php');
        } else{
            header('Location: login.php');
        }



    }else{
        header("Location: Error/Erreur404.php");
    }


}else{
    header("Location: Error/Erreur404.php");
}

?>