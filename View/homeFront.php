<?php
session_start();
$pageTitle = "Landtales";
require "Structure/Functions/function.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];
    checkUserRole();

    require "Structure/Bdd/config.php";

    //Insertion de la ligne de log en bdd et dans le fichier
    $logPath = "Admin/Structures/Logs/log.txt";
    $pageAction = "Accès à la page d'accueil Landtales";
    $pageId = 1;
    $logType = "Visite";

    logActivity($userId, $pseudo, $pageId, $logType, $logPath);




    $userInfo = $bdd->prepare('SELECT pseudo FROM client WHERE id = ?');
    $userInfo->execute(array($userId));
    $userExists = $userInfo->fetch();

    $mainTravelSearch = $bdd->prepare("
    SELECT t.idclient, t.banner, t.title, t.id, c.pseudo AS pseudo
    FROM travel t
    INNER JOIN client c ON t.idclient = c.id
    WHERE t.visibility = 1
    ORDER BY RAND()
    LIMIT 1");
    $mainTravelSearch->execute();
    $mainTravel = $mainTravelSearch->fetch();


    //Affichage de 4 voyages supplémentaire :
    $othersTravelSearch = $bdd->prepare("SELECT id,miniature,title FROM travel WHERE visibility = 1 AND id != ? ORDER BY RAND() LIMIT 4");
    $othersTravelSearch->execute([$mainTravel['id']]);
    $othersTravel = $othersTravelSearch->fetchAll();

    $quizSearch = $bdd->prepare("SELECT id,title,quiz_picture FROM quiz ORDER BY RAND() LIMIT 4");
    $quizSearch->execute();
    $othersquiz = $quizSearch->fetchAll();


    require "Structure/Head/head.php";
} else {

    header("Location: login.php");
    exit();
}

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}

?>

    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="Design/Css/style.css">
    <link rel="stylesheet" href="Design/Css/home-front.css">
</head>



<body class="hidden" data-bs-theme="<?php echo $theme; ?>">



<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">


    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main  mt-5">

        <div class="container">
            <div class="row">
                <div class="best-travel col-md-12 col-sm-12">
                    <h1 class="mt-5 ml-5">Bonjour <?php echo $userExists['pseudo']; ?>, bienvenue sur Landtales</h1>

                    <h2 class="mb-5 mt-5">Le voyage du moment</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="picture-best-travel ratio d-flex flex-column justify-content-end">

                                <div class="picture-best-travel">
                                    <img src="<?php echo html_entity_decode($mainTravel['banner']); ?>" alt="Description de l'image" class="travel-img">
                                </div>
                                <div class="row align-items-end mb-3 ml-0 mr-0 best-travel-contain ">
                                    <div class="col-md-12 d-lg-block d-xl-block d-xl-block d-none">
                                        <div class="best-travel-text col-md-6">
                                            <h2 class="text-white"><?php echo html_entity_decode($mainTravel['title']); ?></h2>
                                            <h5 class="text-white">Par <?php echo html_entity_decode($mainTravel['pseudo']); ?></h5>
                                        </div>
                                        <div class="d-flex justify-content-end col-md-12 mb-3">
                                            <button onclick="window.location.href='travel.php?id=<?php echo $mainTravel['id']; ?>'" class="btn-landtales btn  align-self-end">Accéder au voyage ></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid position-relative d-lg-none d-xl-none mt-5 margin-top-more">
                        <div class="best-travel-text text-center mb-3">
                            <h2><?php echo html_entity_decode($mainTravel['title']); ?></h2>
                            <h5>Par <?php echo html_entity_decode($mainTravel['pseudo']); ?></h5>
                        </div>

                        <div class="d-flex justify-content-center">
                            <button onclick="window.location.href='travel.php?id=<?php echo $mainTravel['id']; ?>'" class="btn-landtales btn  align-self-end">Accéder au voyage ></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row travel">
                <div class="other-travel">
                    <h2 class="mb-5 mt-5">Découvrez les autres voyages</h2>
                    <div class="row list-travel">
                        <?php foreach ($othersTravel as $othertravel) {?>
                            <a href="travel.php?id=<?php echo $othertravel['id']; ?>" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                                <div class="quiz-miniature miniature-img">
                                    <img src="<?php echo html_entity_decode($othertravel['miniature']); ?>" alt="Miniature du voyage" class="travel-img">
                                </div>
                                <h4><?php echo html_entity_decode($othertravel['title']); ?></h4>
                            </a>
                        <?php }?>
                    </div>
                    <div class="row mt-3 mb-3">
                        <a href="travelLobby.php" title="Voir tous les voyages" tabindex="0" class="d-flex">
                            <span class="material-symbols-outlined">apps</span>
                            <p class="more-content-btn"> Voir tous les voyages</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="shop col-md-12 col-sm-12">
                    <div class="col-md-12 col-sm-12 ratio">
                        <div class="container-fluid position-relative shop-picture" style="background-image: url(Design/Pictures/shopPicture.jpg)">

                            <div class="shop-content d-none d-lg-block pt-5">
                                <div class="shop-text mb-3 text-white text-center pt-5">
                                    <h2>En quête de trésor ?</h2>
                                    <h5>Découvrez nos customisations de profil dans notre boutique</h5>
                                </div>
                                <div class="text-center">
                                    <button onclick="window.location.href='customisation.php'" class="btn-landtales btn btn-primary">Accéder à la boutique ></button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center align-items-center">

                            </div>
                        </div>
                    </div>

                    <div class="container-fluid position-relative d-lg-none d-xl-none margin-top-more">
                        <div class="best-travel-text text-center mb-3">
                            <h2 >En quête de trésor ?</h2>
                            <h5>Découvrez nos customisations de profil dans notre boutique</h5>
                        </div>
                        <div class="d-flex justify-content-center">
                            <button onclick="window.location.href='customisation.php'" class="btn-landtales btn btn-primary">Accéder à la boutique ></button>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row travel">
                <div class="other-travel">
                    <h2 class="mb-5 mt-5">Découvrez les autres quiz</h2>
                    <div class="row list-travel">
                        <?php foreach ($othersquiz as $otherquiz) {?>
                            <a href="homeQuiz.php?id=<?php echo $otherquiz['id'] ?>" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                                <div class="quiz-miniature miniature-img">
                                    <img src="<?php echo html_entity_decode($otherquiz['quiz_picture']); ?>" alt="Miniature du voyage" class="travel-img">
                                </div>
                                <h4><?php echo html_entity_decode($otherquiz['title']); ?></h4>
                            </a>
                        <?php }?>
                    </div>
                    <div class="row mt-3 mb-3">
                        <a href="quizLobby.php" title="Voir tous les voyages" tabindex="0" class="d-flex">
                            <span class="material-symbols-outlined">apps</span>
                            <p class="more-content-btn">Voir tous les quiz</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row create-travel">
                <div class="col-md-12 col-sm-12 create-travel-text mb-5 mt-5">
                    <h2>Envie de nous faire voyager ?</h2>
                    <p>Laissez libre cours à votre imagination et créez le voyage de vos rêves !<br>
                        Partagez des mondes fantastiques, des aventures épiques et des personnages extraordinaires.<br>
                        Que vous soyez un écrivain passionné, un rêveur invétéré ou simplement en quête d'inspiration,<br>
                        c'est votre chance de donner vie à des univers extraordinaires.</p>
                    <button onclick="window.location.href='createTravelfirst.php'" class="btn-landtales">Créer un voyage</button>
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
