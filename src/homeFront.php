<?php
session_start();
$pageTitle = "Landtales";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    require "Structure/Bdd/config.php";

    $userInfo = $bdd->prepare('SELECT * FROM client WHERE id = ?');
    $userInfo->execute(array($userId));
    $userExists = $userInfo->fetch();



    require "Structure/Head/head.php";
} else {

    header("Location: login.php");
    exit();
}


?>

    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="Design/Css/style.css">
    <link rel="stylesheet" href="Design/Css/home-front.css">
</head>

<body data-bs-theme="light">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">


    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main  mt-5">

        <div class="container">
            <div class="row">
                <div class="best-travel col-md-12 col-sm-12">

                    <h2 class="text-center mb-5 mt-5"> Bonjour <?php echo $userExists['pseudo']; ?> Le voyage du moment</h2>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="picture-best-travel ratio d-flex flex-column justify-content-end">
                                <div class="row align-items-end mb-3 ml-0 mr-0 best-travel-contain ">
                                    <div class="col-md-12 d-lg-block d-xl-block d-xl-block d-none">
                                        <div class="best-travel-text col-md-6">
                                            <h2>Voyage au sein de...</h2>
                                            <h5>Par ...</h5>
                                        </div>
                                        <div class="d-flex justify-content-end col-md-12 mb-3">
                                            <a href="#" class="btn-landtales btn  align-self-end">Accéder au voyage ></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid position-relative d-lg-none d-xl-none">
                        <div class="best-travel-text text-center mb-3">
                            <h2 >Voyage au sein de...</h2>
                            <h5>Par ...</h5>
                        </div>

                        <div class="d-flex justify-content-center">
                            <a href="#" class="btn-landtales btn btn-primary">Accéder au voyage ></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row travel">
                <div class="other-travel">
                    <h2 class="mb-5 mt-5">Découvrez les autres voyages</h2>
                    <div class="row list-travel">
                        <a href="#" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                            <div class="quiz-miniature miniature-img" style="background-image: url(https://i.pinimg.com/564x/b0/bd/8f/b0bd8f92b3dba2414d19bfa424e337a5.jpg)"></div>
                            <h4>Titre du voyage</h4>
                        </a>
                        <a href="#" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                            <div class="quiz-miniature miniature-img" style="background-image: url(https://i.pinimg.com/564x/c3/5e/5d/c35e5dbe507161b567145962050ac9b1.jpg)"></div>
                            <h4>Titre du voyage</h4>
                        </a>
                        <a href="#" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                            <div class="quiz-miniature miniature-img" style="background-image: url(https://i.pinimg.com/736x/2f/c8/e9/2fc8e914d2c82d32dceeed45bb51cce8.jpg)"></div>
                            <h4>Titre du voyage</h4>
                        </a>
                        <a href="#" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                            <div class="quiz-miniature miniature-img" style="background-image: url(https://i.pinimg.com/564x/85/ce/74/85ce746e5caf291710b4b10fa2ae5d18.jpg)"></div>
                            <h4>Titre du voyage</h4>
                        </a>
                    </div>
                    <div class="row mt-3 mb-3">
                        <a href="#" title="Voir tous les voyages" tabindex="0" class="d-flex">
                            <i class="fa-solid fa-grip-vertical icon-see-more"></i>
                            <p class="more-content-btn">Voir tous les voyages</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="shop col-md-12 col-sm-12">
                    <div class="col-md-12 col-sm-12 ratio">
                        <div class="container-fluid position-relative shop-picture" style="background-image: url(https://wallpapers.com/images/hd/pirate-ship-painted-art-cvinlsm92msxk198.jpg)">
                            <!-- Texte et bouton à masquer -->
                            <div class="shop-content d-none d-lg-block">
                                <div class="shop-text mb-3 text-white text-center">
                                    <h2>En quête de trésor ?</h2>
                                    <h5>Découvrez nos customisations de profil dans notre boutique</h5>
                                </div>
                                <div class="text-center">
                                    <a href="#" class="btn-landtales btn btn-primary">Accéder à la boutique ></a>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center align-items-center">
                                <!-- Image de fond -->
                            </div>
                        </div>
                    </div>
                    <!-- Deuxième texte et bouton -->
                    <div class="container-fluid position-relative d-lg-none d-xl-none">
                        <div class="best-travel-text text-center mb-3">
                            <h2 >En quête de trésor ?</h2>
                            <h5>Découvrez nos customisations de profil dans notre boutique</h5>
                        </div>
                        <div class="d-flex justify-content-center">
                            <a href="#" class="btn-landtales btn btn-primary">Accéder à la boutique ></a>
                        </div>
                    </div>
                </div>
            </div>



            <div class="row travel">
                <div class="other-travel">
                    <h2 class="mb-5 mt-5">Découvrez les autres voyages</h2>
                    <div class="row list-travel">
                        <a href="#" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                            <div class="quiz-miniature miniature-img" style="background-image: url(https://i.pinimg.com/564x/b0/bd/8f/b0bd8f92b3dba2414d19bfa424e337a5.jpg)"></div>
                            <h4>Titre du quiz</h4>
                        </a>
                        <a href="#" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                            <div class="quiz-miniature miniature-img" style="background-image: url(https://i.pinimg.com/564x/c3/5e/5d/c35e5dbe507161b567145962050ac9b1.jpg)"></div>
                            <h4>Titre du quiz</h4>
                        </a>
                        <a href="#" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                            <div class="quiz-miniature miniature-img" style="background-image: url(https://i.pinimg.com/736x/2f/c8/e9/2fc8e914d2c82d32dceeed45bb51cce8.jpg)"></div>
                            <h4>Titre du quiz</h4>
                        </a>
                        <a href="#" title="Titre du voyage" class="col-md-3 col-sm-12 ">
                            <div class="quiz-miniature miniature-img" style="background-image: url(https://i.pinimg.com/564x/85/ce/74/85ce746e5caf291710b4b10fa2ae5d18.jpg)"></div>
                            <h4>Titre du quiz</h4>
                        </a>
                    </div>
                    <div class="row mt-3 mb-3">
                        <a href="#" title="Voir tous les voyages" tabindex="0" class="d-flex">
                            <i class="fa-solid fa-grip-vertical icon-see-more"></i>
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
                    <a href="createTravelfirst.php" class="btn-landtales">Créer un voyage</a>
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
