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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST['popular'])){

            $travelByPopularity = $bdd->prepare('SELECT * FROM travel WHERE idclient = ? AND travel_status = 1 AND visibility = 1 ORDER BY (SELECT COUNT(*) FROM travel_view WHERE travel_view.idtravel = travel.id) DESC');
            $travelByPopularity->execute([$userId]);
            $travels = $travelByPopularity->fetchAll();
        }
        if (isset($_POST['young'])){
            $travelYoung = $bdd->prepare('SELECT * FROM travel WHERE idclient = ? AND travel_status = 1 AND visibility = 1 ORDER BY travel_date DESC');
            $travelYoung->execute([$userId]);
            $travels = $travelYoung->fetchAll();
        }
        if (isset($_POST['old'])){
            $travelOld = $bdd->prepare('SELECT * FROM travel WHERE idclient = ? AND travel_status = 1 AND visibility = 1 ORDER BY travel_date ASC');
            $travelOld->execute([$userId]);
            $travels = $travelOld->fetchAll();
        }


    } else {
        $travelAll = $bdd->prepare('SELECT * FROM travel WHERE idclient = ? AND travel_status = 1 AND visibility = 1');
        $travelAll->execute([$userId]);
        $travels = $travelAll->fetchAll();
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

                                    $viewCountQuery = $bdd->prepare('SELECT COUNT(*) AS view_count FROM travel_view WHERE idtravel = ?');
                                    $viewCountQuery->execute([$idtravel]);
                                    $viewCountResult = $viewCountQuery->fetch();
                                    $viewNumber = $viewCountResult['view_count'];

                                    ?>
                                    <div class="col-md-3 mb-4 travel-container">
                                        <a href="travel.php?id=<?php echo $idtravel; ?>" class="text-decoration-none text-dark" title="<?php echo $title; ?>">
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
