<?php
session_start();

$pageTitle = "Paramètres - Vos voyages";


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

    $userInfo = $bdd->prepare('SELECT * FROM client WHERE id = ?');
    $userInfo->execute([$userId]);
    $user = $userInfo->fetch();




    $userBannerPath = isset($user['banner']) ? $user['banner'] : "";
    $userIconPath = isset($user['profil_picture']) ? $user['profil_picture'] : "";

    $numberOfTravelsQuery = $bdd->prepare('SELECT COUNT(*) AS num_travels FROM travel WHERE idclient = ? AND travel_status = 1 AND visibility = 1');
    $numberOfTravelsQuery->execute([$userId]);
    $numberOfTravelsResult = $numberOfTravelsQuery->fetch();
    $numTravels = $numberOfTravelsResult['num_travels'];


    $travelFirst = $bdd->prepare('SELECT * FROM travel WHERE idclient = ? AND travel_status = 1 AND visibility = 1 ORDER BY (SELECT COUNT(*) FROM travel_view WHERE travel_view.idtravel = travel.id) DESC LIMIT 1');
    $travelFirst->execute([$userId]);
    $travelMain = $travelFirst->fetch();

if ($travelMain) {
        $titleTravelMain = $travelMain['title'];
        $miniatureTravelMain = $travelMain['miniature'];
        $summaryTravelMain = $travelMain['summary'];
        $travelIdMain = $travelMain['id'];

        $likeCount = $bdd->prepare('SELECT COUNT(*) AS like_count FROM travel_like WHERE idtravel = ?');
        $likeCount->execute([$travelMain['id']]);
        $likeCountResult = $likeCount->fetch();
        $numberOfLikes = $likeCountResult['like_count'];

        $viewCount = $bdd->prepare('SELECT COUNT(*) AS like_count FROM travel_view WHERE idtravel = ?');
        $viewCount->execute([$travelMain['id']]);
        $viewCountResult = $viewCount->fetch();
        $numberOfView = $viewCountResult['like_count'];

        $otherTravels = $bdd->prepare('SELECT * FROM travel WHERE idclient = ? AND id != ? AND travel_status = 1 AND visibility = 1 ORDER BY RAND() LIMIT 4');
        $otherTravels->execute([$userId, $travelIdMain]);
    }





    require "Structure/Head/head.php";

?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-front.css">
</head>

<body data-bs-theme="light">

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
                            <div class="profile-container">
                                <img src="<?php echo $userIconPath; ?>" alt="Image de profil" class="profile-img">
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mt-2"><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h2>
                            <p>@<?php echo $user['pseudo']; ?></p>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary">Suivre</button>
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
                                        <a href="travel.php?id=<?php echo $id; ?>" class="text-decoration-none text-dark" title="<?php echo $title; ?>">
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
                        echo '<div class="mb-5 pb-5"><p>' . $user['pseudo'] . 'n\'a pas encore créé de voyage.</p></div>';
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
