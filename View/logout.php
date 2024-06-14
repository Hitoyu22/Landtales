<?php
session_start();

require "Structure/Bdd/config.php";
require "Structure/Functions/function.php";

$userId = $_SESSION['idclient'];
$pseudo = $_SESSION['pseudo'];

$logPath = "Admin/Structures/Logs/log.txt";
$pageAction = "Déconnexion de Landtales";
$pageId = 1001;
$logType = "Déconnexion";

logActivity($userId, $pseudo, $pageId, $logType, $logPath);



$_SESSION = array();
session_destroy();
header('Location: logoutConfirmation.php');
?>