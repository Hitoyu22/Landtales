<?php
session_start();

if (!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['idclient'];

require "Structure/Bdd/config.php";

// Vérifier si l'identifiant de l'ami est passé en paramètre GET
if(isset($_GET['id'])) {
    // Récupérer l'identifiant de l'ami
    $idFriend = $_GET['id'];


    $friendshipKey = $bdd->prepare("SELECT encrypt_key FROM friend WHERE ((idclient1 = ? AND idclient2 = ?) OR (idclient1 = ? AND idclient2 = ?))");
    $friendshipKey->execute([$idFriend, $userId, $userId, $idFriend]);
    $keyMessage = $friendshipKey->fetch();
    $key = $keyMessage['encrypt_key'];

    // Sélectionner les nouveaux messages depuis la base de données
    $messagesAll = $bdd->prepare("SELECT content, message_datetime, idclientfollowed, idclientfollower FROM message WHERE (idclientfollowed = ? AND idclientfollower = ?) OR (idclientfollowed = ? AND idclientfollower = ?) ORDER BY message_datetime ASC");
    $messagesAll->execute([$userId, $idFriend, $idFriend, $userId]);
    $messages = $messagesAll->fetchAll(PDO::FETCH_ASSOC);

    // Construire le HTML des messages
    $html = "";
    foreach ($messages as $message) {
        $data = base64_decode($message['content']);
        $ivLength = openssl_cipher_iv_length("AES-256-CBC");
        $iv = substr($data, 0, $ivLength);
        $encryptedContent = substr($data, $ivLength);
        $decryptedMessage = openssl_decrypt($encryptedContent, "AES-256-CBC", $key, 0, $iv);

        // Ajouter le message au HTML
        $html .= '<div class="d-flex justify-content-' . ($message['idclientfollower'] == $userId ? 'end' : 'start') . ' mb-2">';
        $html .= '<div class="' . ($message['idclientfollower'] == $userId ? 'bg-primary' : 'bg-success') . ' text-white rounded px-3 py-1" style="max-width: 70%;">';
        $html .= '<p class="mb-0">' . str_replace('&#039;', "'", $decryptedMessage) . '</p>';
        $html .= '</div>';
        $html .= '</div>';
    }

    echo $html;
} else {
    echo "Erreur: Identifiant de l'ami manquant.";
}
?>