<?php
session_start();

require "Structure/Functions/function.php";
require "Structure/Bdd/config.php";

if (!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['idclient'];
checkUserRole();

if (isset($_GET['id'])) {
    $idFriend = $_GET['id'];
} else {
    header("Location: myFriends.php");
    exit();
}

$friendshipCheck = $bdd->prepare("SELECT accepted FROM friend WHERE ((idclient1 = ? AND idclient2 = ? AND accepted = 2) OR (idclient1 = ? AND idclient2 = ? AND accepted = 2))");
$friendshipCheck->execute([$userId, $idFriend, $idFriend, $userId]);
$friendshipExists = $friendshipCheck->fetch();

if (!$friendshipExists) {
    header("Location: myFriends.php");
    exit();
}

$friendshipKey = $bdd->prepare("SELECT encrypt_key FROM friend WHERE ((idclient1 = ? AND idclient2 = ?) OR (idclient1 = ? AND idclient2 = ?))");
$friendshipKey->execute([$idFriend, $userId, $userId, $idFriend]);
$keyMessage = $friendshipKey->fetch();
$key = $keyMessage['encrypt_key'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $message = htmlspecialchars($_POST['message']);
    $cipher = "AES-256-CBC";
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
    $encryptedMessage = openssl_encrypt($message, $cipher, $key, 0, $iv);
    $messageEncrypted = base64_encode($iv . $encryptedMessage);  // Encode IV and encrypted message together in base64

    $newMessage = $bdd->prepare("INSERT INTO message (content, message_datetime, idclientfollowed, idclientfollower) VALUES (?, NOW(), ?, ?)");
    $newMessage->execute([$messageEncrypted, $idFriend, $userId]);

    header("Location: messages.php?id=$idFriend");
    exit();
}

$friendInfo = $bdd->prepare("SELECT firstname, lastname FROM client WHERE id = ?");
$friendInfo->execute([$idFriend]);
$friend = $friendInfo->fetch();
$friendName = $friend ? htmlspecialchars($friend['firstname'] . " " . $friend['lastname']) : "Utilisateur inconnu";
$pageTitle = "Mes messages avec " . $friendName;

require "Structure/Head/head.php";

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet">
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/message.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">
    <?php require "Structure/Sidebar/sidebar.php";?>
    <div class="main mt-5" style="overflow: hidden;">
        <div class="mt-5" style="height: 100%;">
            <div id="messageArea" class="message-area pb-0 px-2">
            </div>
            <form action="" method="post" class="mt-3 mx-5">
                <div class="input-group">
                    <input type="text" id="messageInput" name="message" placeholder="Saisissez votre message" class="form-control" required maxlength="256" autofocus>
                    <button class="btn btn-primary" type="submit">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
<script>
    let messageArea = document.getElementById("messageArea");

    function scrollToBottom() {
        messageArea.scrollTop = messageArea.scrollHeight;
    }


    window.onload = scrollToBottom;

    setInterval(function() {
        fetch("Includes/loadMessages.php?id=<?php echo $idFriend; ?>")
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur lors du chargement des messages');
                }
                return response.text();
            })
            .then(data => {
                let shouldScrollToBottom = messageArea.scrollTop + messageArea.clientHeight === messageArea.scrollHeight;
                messageArea.innerHTML = data;
                if (shouldScrollToBottom) {
                    scrollToBottom();
                }
            })
            .catch(error => {
                console.error(error);
            });
    }, 500);
</script>
</body>
</html>
