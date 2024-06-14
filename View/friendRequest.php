<?php


require "Structure/Bdd/config.php";

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    checkUserRole();
} else {
    if (isset($_SESSION['idclient'])) {
        $userId = $_SESSION['idclient'];
    } else {
        header("Location: login.php");
        exit();
    }
}

// Gestion du système d'abonnement
$isFollowing = false;
if (isset($_SESSION['idclient']) && $userId != $_SESSION['idclient']) {
    $checkFollow = $bdd->prepare('SELECT idclientfollowed FROM follower WHERE idclientfollower = ? AND idclientfollowed = ?');
    $checkFollow->execute([$_SESSION['idclient'], $userId]);
    $isFollowing = $checkFollow->fetch() !== false;
}

$isFollowedByCurrentUser = false;
if (isset($_SESSION['idclient']) && $userId != $_SESSION['idclient']) {
    $checkFollowBack = $bdd->prepare('SELECT idclientfollowed FROM follower WHERE idclientfollower = ? AND idclientfollowed = ?');
    $checkFollowBack->execute([$userId, $_SESSION['idclient']]);
    $isFollowedByCurrentUser = $checkFollowBack->fetch() !== false;
}

if (isset($_POST['follow'])) {
    $followerId = $_SESSION['idclient'];
    $followedId = $userId;

    $addFollow = $bdd->prepare('INSERT INTO follower (idclientfollower, idclientfollowed) VALUES (?, ?)');
    $addFollow->execute([$followerId, $followedId]);

    header("Location: {$_SERVER['REQUEST_URI']}");

} elseif (isset($_POST['unfollow'])) {
    $followerId = $_SESSION['idclient'];
    $followedId = $userId;

    $deleteFollow = $bdd->prepare('DELETE FROM follower WHERE idclientfollower = ? AND idclientfollowed = ?');
    $deleteFollow->execute([$followerId, $followedId]);

    header("Location: {$_SERVER['REQUEST_URI']}");
}

// Gestion de l'annulation de la demande d'amitié
if (isset($_POST['cancelFriendRequest'])) {
    $cancelFriendRequest = $bdd->prepare('DELETE FROM friend WHERE (idclient1 = ? AND idclient2 = ? AND accepted = 1) OR (idclient1 = ? AND idclient2 = ? AND accepted = 1)');
    $cancelFriendRequest->execute([$_SESSION['idclient'], $userId, $userId, $_SESSION['idclient']]);

    header("Location: {$_SERVER['REQUEST_URI']}");
    exit;
}

// Système de demande d'ami
if (isset($_POST['addFriend'])) {
    $requesterId = $_SESSION['idclient'];
    $requestedId = $userId;

    $checkRequest = $bdd->prepare('SELECT accepted FROM friend WHERE idclient1 = ? AND idclient2 = ?');
    $checkRequest->execute([$requesterId, $requestedId]);
    if ($checkRequest->fetch() === false) {
        $addFriend = $bdd->prepare('INSERT INTO friend (idclient1, idclient2, accepted) VALUES (?, ?, 1)');
        $addFriend->execute([$requesterId, $requestedId]);

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit;
    }
}

$friendRequestReceived = false;
$checkFriendRequestReceived = $bdd->prepare('SELECT accepted FROM friend WHERE idclient1 = ? AND idclient2 = ? AND accepted = 1');
$checkFriendRequestReceived->execute([$userId, $_SESSION['idclient']]);
if ($checkFriendRequestReceived->fetch()) {
    $friendRequestReceived = true;
}

// Vérification si une demande d'amitié a été envoyée
$friendRequestSent = false;
$checkFriendRequestSent = $bdd->prepare('SELECT accepted FROM friend WHERE idclient1 = ? AND idclient2 = ? AND accepted = 1');
$checkFriendRequestSent->execute([$_SESSION['idclient'], $userId]);
if ($checkFriendRequestSent->fetch()) {
    $friendRequestSent = true;
}

// Vérification si les utilisateurs sont déjà amis
$alreadyFriends = false;
$checkIfAlreadyFriends = $bdd->prepare('SELECT accepted FROM friend WHERE ((idclient1 = ? AND idclient2 = ?) OR (idclient1 = ? AND idclient2 = ?)) AND accepted = 2');
$checkIfAlreadyFriends->execute([$_SESSION['idclient'], $userId, $userId, $_SESSION['idclient']]);
if ($checkIfAlreadyFriends->fetch()) {
    $alreadyFriends = true;
}

// Accepter la demande d'amitié
if (isset($_POST['acceptFriendRequest'])) {
    $key = bin2hex(random_bytes(16));
    $acceptFriendRequest = $bdd->prepare('UPDATE friend SET accepted = 2, encrypt_key = ? WHERE idclient1 = ? AND idclient2 = ? AND accepted = 1');
    $acceptFriendRequest->execute([$key,$userId, $_SESSION['idclient']]);

    header("Location: {$_SERVER['REQUEST_URI']}");
    exit;
}

// Initialisation de la variable
$friendRequestPending = false;

//Affichage de la demande en attente
if (isset($_SESSION['idclient']) && $userId != $_SESSION['idclient']) {
    $checkPendingRequest = $bdd->prepare('SELECT accepted FROM friend WHERE (idclient1 = ? AND idclient2 = ? AND accepted = 1) OR (idclient1 = ? AND idclient2 = ? AND accepted = 1)');
    $checkPendingRequest->execute([$_SESSION['idclient'], $userId, $userId, $_SESSION['idclient']]);
    if ($checkPendingRequest->fetch()) {
        $friendRequestPending = true;
    }
}