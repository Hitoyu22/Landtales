<?php
session_start();
require "Structure/Functions/function.php";

if(!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}
checkUserRole();

$pageTitle = "Mes amis";

require "Structure/Bdd/config.php";
$userId = $_SESSION['idclient'];
$pseudo = $_SESSION['pseudo'];

//Insertion de la ligne de log en bdd et dans le fichier
$logPath = "Admin/Structures/Logs/log.txt";
$pageAction = "Mes amis";
$pageId = 6;
$logType = "Visite";

logActivity($userId, $pseudo, $pageId, $logType, $logPath);

    // Récupérer les amis
    $friendsQuery = $bdd->prepare("
        SELECT client.id, client.pseudo, client.firstname, client.lastname, client.profil_picture, client.mood
        FROM friend
        JOIN client ON client.id = CASE 
            WHEN friend.idclient1 = ? THEN friend.idclient2 
            ELSE friend.idclient1 
        END
        WHERE (friend.idclient1 = ? OR friend.idclient2 = ?) AND friend.accepted = 2
    ");
    $friendsQuery->execute([$userId, $userId, $userId]);
    $friends = $friendsQuery->fetchAll();


    $receivedRequestsQuery = $bdd->prepare("SELECT id,pseudo FROM friend JOIN client ON client.id = friend.idclient1 WHERE idclient2 = ? AND accepted = 1");
    $receivedRequestsQuery->execute([$userId]);
    $receivedRequests = $receivedRequestsQuery->fetchAll();


    $sentRequestsQuery = $bdd->prepare("SELECT id,pseudo FROM friend JOIN client ON client.id = friend.idclient2 WHERE idclient1 = ? AND accepted = 1");
    $sentRequestsQuery->execute([$userId]);
    $sentRequests = $sentRequestsQuery->fetchAll();


    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        if (isset($_POST['accept'])) {
            $requesterId = $_POST['requesterId'];  // L'ID de l'utilisateur qui a envoyé la demande d'ami
            $key = bin2hex(random_bytes(16));
            $acceptQuery = $bdd->prepare("UPDATE friend SET accepted = 2, encrypt_key = ? WHERE idclient1 = ? AND idclient2 = ?");
            $acceptQuery->execute([$key, $requesterId, $userId]);
        } elseif (isset($_POST['decline'])) {
            $requesterId = $_POST['requesterId'];  // L'ID de l'utilisateur qui a envoyé la demande d'ami
            // Refuser la demande d'ami
            $declineQuery = $bdd->prepare("DELETE FROM friend WHERE idclient1 = ? AND idclient2 = ? AND accepted = 1");
            $declineQuery->execute([$requesterId, $userId]);
        }

        if (isset($_POST['delete'])){
            $idFriend = $_POST['friends'];

            $deleteMessage = $bdd->prepare("DELETE FROM message WHERE (idclientfollowed = ? AND idclientfollower = ?) OR (idclientfollower = ? AND idclientfollowed = ?)");
            $deleteMessage->execute([$idFriend, $userId, $userId, $idFriend]);

            $deleteFriendship = $bdd->prepare("DELETE  FROM friend WHERE (idclient1 = ? AND idclient2 = ?) OR (idclient1 = ? AND idclient2 = ?) ");
            $deleteFriendship->execute([$idFriend,$userId,$userId,$idFriend]);
        }

        header("Location: myFriends.php");
    }


require "Structure/Head/head.php";

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>
<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">

</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">
    <?php require "Structure/Sidebar/sidebar.php";?>
    <div class="main mt-5">
        <div class="container mt-5">
            <div class="row">
                <?php if (!empty($receivedRequests)): ?>
                    <div class="col-12 col-lg-6">
                        <h4>Demandes d'amis reçues</h4>
                        <ul class="list-group">
                            <?php foreach ($receivedRequests as $request): ?>
                                <li class="list-group-item">
                                    <a href="userProfil.php?id=<?php echo $request['id']; ?>" title="Accéder au profil de l'utilisateur"><?php echo $request['pseudo']; ?></a>
                                    <form action="" method="post" class="float-right">
                                        <input type="hidden" name="requesterId" value="<?php echo $request['id']; ?>">
                                        <button type="submit" name="accept" class="btn btn-success btn-sm">Accepter</button>
                                        <button type="submit" name="decline" class="btn btn-danger btn-sm">Refuser</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sentRequests)): ?>
                    <div class="col-12 col-lg-6">
                        <h4>Demandes d'amis envoyées</h4>
                        <ul class="list-group">
                            <?php foreach ($sentRequests as $request): ?>
                                <li class="list-group-item">
                                    <a href="userProfil.php?id=<?php echo $request['id']; ?>" title="Accéder au profil de l'utilisateur"><?php echo $request['pseudo']; ?></a>
                                    - En attente
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <h2 class="mb-3">Mes Amis</h2>

                    <?php if (!empty($friends)): ?>
                        <div class="row">
                            <?php foreach ($friends as $friend):

                                $friendIconPath = isset($friend['profil_picture']) ? $friend['profil_picture'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";


                                ?>
                                <div class="col-12 mb-3">

                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="messages.php?id=<?php echo $friend['id']; ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="profile-img-container">
                                                <img src="<?php echo $friendIconPath ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                            </div>
                                            <div class="ml-3">
                                                <p><?php echo $friend['pseudo']; ?></p>
                                                <p class="text-muted"><?php echo $friend['mood']; ?></p>
                                            </div>
                                        </div>
                                        </a>
                                        <form action="" method="post">
                                            <input type="hidden" value="<?php echo $friend['id']; ?>" name="friends">
                                            <button type="submit" name="delete" class="btn btn-landtales">Ne plus être ami</button>
                                        </form>

                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="mb-5">Vous n'avez pas ami ? Prends un Curly !</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>
