<?php
session_start();


if (!isset($_SESSION['idclient'])){
    header("Location: login.php");
}


$pageTitle = "Profil - Accueil";

require "Structure/Functions/function.php";

if(isset($_GET['id'])) {
    $userId = $_GET['id'];
} else {

    if(isset($_SESSION['idclient'])) {

        $userId = $_SESSION['idclient'];
    } else {

        header("Location: login.php");
        exit();
    }
}
    require "Structure/Bdd/config.php";
    $pseudo = $_SESSION['pseudo'];

    $userInfo = $bdd->prepare('SELECT banner,profil_picture,idcustomisation, visibility, firstname, lastname,pseudo,idrank,permaBan FROM client WHERE id = ?');
    $userInfo->execute([$userId]);
    $user = $userInfo->fetch();

    if ($user && $user['idrank'] == 2 || $user['permaBan'] == 1) {
        header("Location: userProfil.php");
        exit;
    } else if ($user === false) {
        header("Location: userProfil.php");
        exit;
    } else if (isset($_GET['id']) && isset($_SESSION['idclient']) && $user['visibility'] == 2 && $_GET['id'] != $_SESSION['idclient']) {
        header("Location: userProfil.php");
        exit();
    }



    $userBannerPath = isset($user['banner']) ? $user['banner'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
    $userIconPath = isset($user['profil_picture']) ? $user['profil_picture'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
    $customUser = isset($user['idcustomisation']) ? $user['idcustomisation'] : "";

    $numberOfTravelsQuery = $bdd->prepare('SELECT COUNT(idclient) AS num_travels FROM travel WHERE idclient = ? AND travel_status = 1 AND visibility = 1');
    $numberOfTravelsQuery->execute([$userId]);
    $numberOfTravelsResult = $numberOfTravelsQuery->fetch();
    $numTravels = $numberOfTravelsResult['num_travels'];


$travelFirst = $bdd->prepare('
    SELECT t.title, t.id, t.summary, t.miniature,
           (SELECT COUNT(idtravel) FROM travel_like tl WHERE tl.idtravel = t.id) AS like_count,
           (SELECT COUNT(idtravel) FROM travel_view tv WHERE tv.idtravel = t.id) AS view_count
    FROM travel t
    WHERE t.idclient = ? AND t.travel_status = 1 AND t.visibility = 1
    ORDER BY (SELECT COUNT(idtravel) FROM travel_view tv WHERE tv.idtravel = t.id) DESC
    LIMIT 1
');
$travelFirst->execute([$userId]);
$travelMain = $travelFirst->fetch();

if ($travelMain) {
    $titleTravelMain = $travelMain['title'];
    $miniatureTravelMain = $travelMain['miniature'];
    $summaryTravelMain = $travelMain['summary'];
    $travelIdMain = $travelMain['id'];
    $numberOfLikes = $travelMain['like_count'];
    $numberOfView = $travelMain['view_count'];

    // Requête pour obtenir d'autres voyages aléatoires
    $otherTravels = $bdd->prepare('
        SELECT title, id, miniature
        FROM travel
        WHERE idclient = ? AND id != ? AND travel_status = 1 AND visibility = 1
        ORDER BY RAND()
        LIMIT 4
    ');
    $otherTravels->execute([$userId, $travelIdMain]);
}


require "friendRequest.php";



    $numberOfFollowersQuery = $bdd->prepare('SELECT COUNT(idclientfollowed) AS num_followers FROM follower WHERE idclientfollowed = ?');
    $numberOfFollowersQuery->execute([$userId]);
    $numberOfFollowers = $numberOfFollowersQuery->fetchColumn();




$customImageUrl = "";
    if (!empty($customUser)) {
        $customInfo = $bdd->prepare('SELECT picture FROM customisation WHERE id = ?');
        $customInfo->execute([$customUser]);
        $custom = $customInfo->fetch();
        $customImageUrl = $custom['picture'];
    }

    $userPseudo = $user['pseudo'];
    //Insertion de la ligne de log en bdd et dans le fichier
    $logPath = "Admin/Structures/Logs/log.txt";
    $pageAction = "Voir le profil d'un utilisateur";
    $pageId = 9;
    $logType = "Visite";

    logActivity($userId, $pseudo, $pageId, $logType, $logPath);

    require "Structure/Head/head.php";


$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-front.css">
</head>

<body class="hidden" data-bs-theme="<?php echo $theme; ?>">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">


    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main  mt-5 ">

        <div id="banner-user" class="col-12 mt-4">
            <img alt="Bannière de l'utilisateur" src="<?php echo $userBannerPath; ?>" class="banner-user-img">
        </div>

        <div class="container">
            <div>

                <div>
                    <div class="row align-items-center top-data">
                        <div class="col-auto">
                            <div id="multicouche" class="profile-container">
                                <img src="<?php echo $userIconPath; ?>" alt="Image de profil" class="profile-img">
                                <?php if (!empty($customImageUrl)): ?>
                                    <img src="<?php echo $customImageUrl; ?>" alt="Customisation" class="custom-img w-100">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mt-4"><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h2>
                            <p>@<?php echo $user['pseudo']; ?></p>
                            <p><?php echo ($numberOfFollowers == 0 || $numberOfFollowers == 1) ? $numberOfFollowers . ' abonné' : $numberOfFollowers . ' abonnés'; ?></p>

                        </div>
                        <div class="col-auto">
                            <?php if ($userId != $_SESSION['idclient']): ?>
                                <form action="" method="post" style="display:inline;">
                                    <?php if ($isFollowing): ?>
                                        <button type="submit" class="btn btn-warning" name="unfollow">Se désabonner</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-primary" name="follow">S'abonner</button>
                                    <?php endif; ?>

                                    <?php if ($alreadyFriends): ?>
                                        <button type="button" class="btn btn-success" disabled>Ami</button>
                                    <?php elseif ($friendRequestReceived): ?>
                                        <button type="submit" class="btn btn-primary" name="acceptFriendRequest">Accepter l'amitié</button>
                                    <?php elseif ($friendRequestSent): ?>
                                        <button type="button" class="btn btn-secondary" disabled>Demande envoyée</button>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-info" name="addFriend">Demander en ami</button>
                                    <?php endif; ?>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>


                    <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center bg-transparent mb-5" style="z-index: 100;"> <!-- Définissez un z-index inférieur -->
                        <div class="container-fluid">
                            <ul class="navbar-nav" style="margin: 0 auto;">
                                <li class="nav-item">
                                    <a class="nav-link" href="userProfil.php?id=<?php echo $userId; ?>"><u>Accueil</u></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="userTravel.php?id=<?php echo $userId; ?>">Voyages</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="userAbout.php?id=<?php echo $userId; ?>">A propos</a>
                                </li>

                            </ul>
                        </div>
                    </nav>
                    <?php if($numTravels > 0){ ?>
                        <div class="container mb-5">
                            <h2 class="mb-4">Le voyage le plus populaire</h2>
                            <a href="travel.php?id=<?php echo $travelMain['id']; ?>" title="<?php echo $travelMain['title']; ?>">
                                <div class="row mb-5">
                                    <div class="col-12 col-md-4 miniature-main-travel">
                                        <img class="miniature-main-travel-img" alt="Miniature du voyage le plus populaire" src="<?php echo $travelMain['miniature']; ?>">
                                    </div>
                                    <div class="col-12 col-md-8">
                                        <h4><?php echo $travelMain['title']; ?></h4>
                                        <p><?php echo $travelMain['summary']; ?></p>
                                        <p><?php echo $numberOfView . (($numberOfView > 1) ? ' vues' : ' vue'); ?> - <?php echo $numberOfLikes . (($numberOfLikes > 1) ? ' likes' : ' like'); ?></p>
                                    </div>
                                </div>
                            </a>
                            <h2 class="mb-4">Les autres voyages de <?php echo $user['firstname'];?> </h2>
                            <div class="row">
                                <?php
                                while ($otherTravel = $otherTravels->fetch()) {
                                    $title = $otherTravel['title'];
                                    $id = $otherTravel['id'];
                                    $miniature = $otherTravel['miniature'];
                                    ?>
                                    <div class="col-md-3 mb-4 travel-container">
                                        <a href="travel.php?id=<?php echo $id; ?>" class="text-decoration-none" title="<?php echo $title; ?>">
                                            <div class="card img-content card-square">
                                                <img class="card-img-top" src="<?php echo $miniature; ?>">
                                            </div>
                                            <h5><?php echo $title; ?></h5>
                                        </a>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                    <?php } else {
                        echo '<div class="mb-5 pb-5"><p>' . $user['pseudo'] . ' n\'a pas encore créé de voyage.</p></div>';
                    } ?>


                </div>
            </div>
        </div>

        <?php require "Structure/Footer/footer.php";?>

    </div>
</div>


<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>

</html>
