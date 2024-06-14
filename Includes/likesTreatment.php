<?php
session_start();


if (!isset($_SESSION['idclient'])) {
    echo json_encode(array('success' => false, 'message' => 'Utilisateur non connecté.'));
    exit();
}


if (!isset($_GET['travelId']) || !isset($_GET['userId'])) {
    echo json_encode(array('success' => false, 'message' => 'Données manquantes.'));
    exit();
}


error_reporting(E_ALL);
ini_set('display_errors', 1);


$travelId = $_GET['travelId'];
$userId = $_GET['userId'];


require "Structure/Bdd/config.php";


$checkLikeQuery = $bdd->prepare("SELECT idtravel,idclient FROM travel_like WHERE idclient = ? AND idtravel = ?");
$checkLikeQuery->execute([$userId, $travelId]);
$existingLike = $checkLikeQuery->fetch(PDO::FETCH_ASSOC);

if ($existingLike) {

    $deleteLikeQuery = $bdd->prepare("DELETE FROM travel_like WHERE idclient = ? AND idtravel = ?");
    $deleteLikeQuery->execute([$userId, $travelId]);
    echo json_encode(array('success' => true, 'message' => 'Like supprimé avec succès!', 'isLiked' => false));
} else {

    $addLikeQuery = $bdd->prepare("INSERT INTO travel_like (idclient, idtravel) VALUES (?, ?)");
    $addLikeQuery->execute([$userId, $travelId]);
    echo json_encode(array('success' => true, 'message' => 'Like ajouté avec succès!', 'isLiked' => true));
}

?>