<?php
    session_start();

    $pageTitle = "Profil - Les voyages";


    require "Structure/Functions/function.php";


    if(isset($_GET['id'])) {
        $userId = $_GET['id'];
        checkUserRole();
    } else {

        if(isset($_SESSION['idclient'])) {

            $userId = $_SESSION['idclient'];
        } else {

            header("Location: login.php");
            exit();
        }
    }
        require "Structure/Bdd/config.php";

        $userInfo = $bdd->prepare('SELECT banner,profil_picture,summary,idcustomisation,idrank, visibility, firstname,lastname,pseudo,permaBan FROM client WHERE id = ?');
        $userInfo->execute([$userId]);
        $user = $userInfo->fetch();

        if ($user && $user['idrank'] == 2 || $user['permaBan'] == 1) {
            header("Location: userTravel.php");
            exit;
        }
        if ($user === false) {
            header("Location: userTravel.php");
            exit;
        }

        if (isset($_GET['id']) && isset($_SESSION['idclient']) && $user['visibility'] == 2 && $_GET['id'] != $_SESSION['idclient']) {
            header("Location: userTravel.php");
            exit();
        }



        $userBannerPath = isset($user['banner']) ? $user['banner'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
        $userIconPath = isset($user['profil_picture']) ? $user['profil_picture'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
        $customUser = isset($user['idcustomisation']) ? $user['idcustomisation'] : "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST['popular'])){

            if (isset($_POST['popular'])){
                $travelByPopularity = $bdd->prepare('
                        SELECT t.title, t.miniature,t.id, COUNT(tv.idtravel) AS view_count
                        FROM travel t
                        LEFT JOIN travel_view tv ON t.id = tv.idtravel
                        WHERE t.idclient = ? AND t.travel_status = 1 AND t.visibility = 1
                        GROUP BY t.id
                        ORDER BY view_count DESC
                    ');
                $travelByPopularity->execute([$userId]);
                $travels = $travelByPopularity->fetchAll();
            }
        }
        if (isset($_POST['young'])){
            $travelYoung = $bdd->prepare('
                    SELECT t.title, t.miniature,t.id, COUNT(tv.idtravel) AS view_count
                    FROM travel t
                    LEFT JOIN travel_view tv ON t.id = tv.idtravel
                    WHERE t.idclient = ? AND t.travel_status = 1 AND t.visibility = 1
                    GROUP BY t.id
                    ORDER BY t.travel_date DESC
                ');
            $travelYoung->execute([$userId]);
            $travels = $travelYoung->fetchAll();
        }

        if (isset($_POST['old'])){
            $travelOld = $bdd->prepare('
                    SELECT t.title, t.miniature,t.id, COUNT(tv.idtravel) AS view_count
                    FROM travel t
                    LEFT JOIN travel_view tv ON t.id = tv.idtravel
                    WHERE t.idclient = ? AND t.travel_status = 1 AND t.visibility = 1
                    GROUP BY t.id
                    ORDER BY t.travel_date ASC
                ');
                        $travelOld->execute([$userId]);
            $travels = $travelOld->fetchAll();
        }




    } else {
        $travelAll = $bdd->prepare('
                SELECT t.title, t.miniature,t.id, COUNT(tv.idtravel) AS view_count
                FROM travel t
                LEFT JOIN travel_view tv ON t.id = tv.idtravel
                WHERE t.idclient = ? AND t.travel_status = 1 AND t.visibility = 1
                GROUP BY t.id
            ');
        $travelAll->execute([$userId]);
        $travels = $travelAll->fetchAll();

    }

require "friendRequest.php";

    $customImageUrl = "";
    if (!empty($customUser)) {
        $customInfo = $bdd->prepare('SELECT picture FROM customisation WHERE id = ?');
        $customInfo->execute([$customUser]);
        $custom = $customInfo->fetch();
        $customImageUrl = $custom['picture'];
    }

        $numberOfFollowersQuery = $bdd->prepare('SELECT COUNT(idclientfollowed) AS num_followers FROM follower WHERE idclientfollowed = ?');
        $numberOfFollowersQuery->execute([$userId]);
        $numberOfFollowers = $numberOfFollowersQuery->fetchColumn();




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
                            <?php if ($userId != $_SESSION['idclient']): // Vérifier si le profil n'appartient pas à l'utilisateur connecté ?>
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
                                    <a class="nav-link" href="userProfil.php?id=<?php echo $userId; ?>">Accueil</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="userTravel.php?id=<?php echo $userId; ?>"><u>Voyages</u></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="userAbout.php?id=<?php echo $userId; ?>">A propos</a>
                                </li>

                            </ul>
                        </div>
                    </nav>



                        <div class="container mb-5">
                            <div class="row mb-5">
                                <form method="post" action="">
                                <button type="submit" class="btn-landtales" name="popular">Les plus populaires</button>
                                <button type="submit" class="btn-landtales" name="young">Les plus récents</button>
                                <button type="submit" class="btn-landtales"  name="old">Les plus anciens</button>
                                </form>
                            </div>

                            <div class="col-12">
                                <div class="row">
                                <?php
                                foreach ($travels as $travel) {
                                    $idtravel = $travel['id'];
                                    $miniature = $travel['miniature'];
                                    $title = $travel['title'];
                                    $viewNumber = $travel['view_count'];

                                    ?>
                                    <div class="col-md-3 mb-4 travel-container">
                                        <a href="travel.php?id=<?php echo $idtravel; ?>" class="text-decoration-none" title="<?php echo $title; ?>">
                                            <div class="card img-content-2 img-content card-square">
                                                <img class="card-img-top" src="<?php echo $miniature; ?>">
                                            </div>
                                            <h5><?php echo $title; ?></h5>
                                            <p><?php echo ($viewNumber > 1 ? $viewNumber . " vues" : $viewNumber . " vue") ?></p>
                                        </a>
                                    </div>

                               <?php } ?>
                            </div>
                            </div>

                        </div>



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
