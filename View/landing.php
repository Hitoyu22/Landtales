<?php

require "Structure/Functions/function.php";

$pageTitle = "Bienvenue sur Landtales";
require "Structure/Head/head.php";
?>
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/bootstrap.min.css">

</head>

<body class="back-dark text-white mt-0" data-bs-spy="scroll" data-bs-target="#navScroll">

<main>
    <div class="w-100 overflow-hidden position-relative back-clear text-white">
        <div class="position-absolute w-100 h-100 back-clear star opacity-75 top-0 start-0"></div>
        <div class="container py-vh-4 position-relative mt-5 px-vw-5 text-center">
            <div class="row d-flex align-items-center justify-content-center py-vh-5">
                <div class="col-12 col-xl-10">
                    <h1 class=" display-huge text-center">Landtales</h1>
                    <h2 class=" mt-3 mb-3 lh-1">Il n'a jamais été aussi facile de voyager</h2>
                </div>
                <div class="col-12 col-xl-8">
                    <p class="lead">Explorer l'infini des mondes imaginaires à travers les mots de nos voyageurs érudits. Bienvenue dans notre univers fantastique où chaque récit est une porte ouverte vers l'extraordinaire !</p>
                </div>
                <div class="col-12 text-center">
                    <a href="register.php" class="btn btn-xl btn-light mx-3">Rejoindre Landtales</a>
                    <a href="login.php" class="btn btn-xl btn-light mx-3">Se connecter</a>
                </div>
            </div>
        </div>

    </div>

    <div class="w-100 position-relative back-dark text-white bg-cover d-flex align-items-center">
        <div class="container-fluid px-vw-5">
            <div class="position-absolute w-100 h-50 back-dark bottom-0 start-0"></div>
            <div class="row d-flex align-items-center position-relative justify-content-center px-0 g-5">
                <div class="col-12 col-lg-6">
                    <img src="Design/Pictures/Landing/landing1.jpg" width="2280" height="1732" alt="abstract image" class="img-fluid position-relative rounded-5 shadow" >
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <img src="Design/Pictures/Landing/landing3.jpg" width="1116" height="1578" alt="abstract image" class="img-fluid position-relative rounded-5 shadow" >
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <img src="Design/Pictures/Landing/landing2.jpg" width="1116" height="1578" alt="abstract image" class="img-fluid position-relative rounded-5 shadow" >
                </div>

            </div>
        </div>
    </div>
    <div class="back-clear star">
        <div class="container px-vw-5 py-vh-5">
            <div class="row d-flex align-items-center">
                <div class="col-12 col-lg-7 text-lg-end" >
                    <span class="h5 text-secondary fw-lighter">Qui sommes-nous ?</span>
                    <h2 class="display-4">Landtales est un site de création de voyages imaginaires qui laisse à ses utilisateurs la liberté de voyager dans des mondes sortant de leur propre imagination.</h2>
                </div>
                <div class="col-12 col-lg-5" >
                    <img class="img-fluid rounded-5 mb-n5 shadow" src="Design/Pictures/Landing/landing4.jpg" width="512" height="512" alt="a nice person" loading="lazy" >
                </div>
            </div>
        </div>
    </div>

    <div class="back-dark py-vh-3">
        <div class="container back-dark px-vw-5 py-vh-3 rounded-5 shadow">

            <div class="row gx-5">
                <div class="col-12 col-md-6">
                    <div class="card bg-transparent mb-5" >
                        <div class="back-clear shadow rounded-5 p-0">
                            <img src="Design/Pictures/Landing/landing5.jpg" width="582" height="327" alt="abstract image" class="img-fluid rounded-5 no-bottom-radius" loading="lazy">
                            <div class="p-5">
                                <h2 class="fw-lighter text-white">Les voyages</h2>
                                <p class="pb-4 text-secondary">Incontournable du site, vous pouvez écrire ou bien découvrir des voyages encore plus incroyables les uns que les autres.  Utiliser notre super outil de rédaction de voyage afin de nous laisser sans voix.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-transparent" >
                        <div class="back-clear shadow rounded-5 p-0">
                            <img src="Design/Pictures/Landing/landing6.jpg" width="582" height="442" alt="abstract image" class="img-fluid rounded-5 no-bottom-radius" loading="lazy">
                            <div class="p-5">
                                <h2 class="fw-lighter text-white">Les quiz</h2>
                                <p class="pb-4 text-secondary">Quoi de mieux qu'un peu de challenge afin de tester votre culture. Créer ou bien faites les quiz de la communauté. </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="p-5 pt-0 mt-5" >
                        <span class="h5 text-secondary fw-lighter">Les activités sur Landtales</span>
                        <h2 class="display-4">Il y a tellement de chos à faire que vous ne penserez qu'à revenir.</h2>
                    </div>
                    <div class="card bg-transparent mb-5 mt-5" >
                        <div class="back-clear shadow rounded-5 p-0">
                            <img src="Design/Pictures/Landing/landing7.jpg" width="582" height="390" alt="abstract image" class="img-fluid rounded-5 no-bottom-radius" loading="lazy">
                            <div class="p-5">
                                <h2 class="fw-lighter text-white">La boutique de customisation</h2>
                                <p class="pb-4 text-secondary">Quoi de mieux qu'une belle customisation de profil afin de pouvoir impressioner ses amis. Acheter des customisations grâces aux pièces obtenues dans certains quiz gratuitement.</p>
                            </div>
                        </div>
                    </div>

                    <div class="card bg-transparent" >
                        <div class="back-clear shadow rounded-5 p-0">
                            <img src="Design/Pictures/Landing/landing8.jpg" width="582" height="327" alt="abstract image" class="img-fluid rounded-5 no-bottom-radius" loading="lazy">
                            <div class="p-5">
                                <h2 class="fw-lighter text-white">Les amitiés</h2>
                                <p class="pb-4 text-secondary">Même si Landtales et amitié ne riment pas, les amitiés qui se forment grâce à notre site sont vraiment formidables ! Discuter avec vos amis grâce à notre messagerie privée sécurisée.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <div class="back-clear star position-relative">
        <div class="container px-vw-5 py-vh-5">
            <div class="row d-flex align-items-center">

                <div class="col-12 col-lg-7">
                    <img class="img-fluid rounded-5 mb-n5 shadow" src="Design/Pictures/Landing/landing9.jpg" width="512" height="512" alt="a nice person" loading="lazy" >
                    <img class="img-fluid rounded-5 ms-5 mb-n5 shadow" src="Design/Pictures/Landing/landing10.jpg" width="512" height="512" alt="another nice person" loading="lazy" >
                </div>
                <div class="col-12 col-lg-5 back-clear rounded-5 py-5" >
                    <h2 class="display-4">Ecrivez une légende, explorez l'infini de l'imaginaire : Votre univers ! Votre plume ! Notre communauté !</h2>
                </div>
            </div>
        </div>
    </div>



    </div>
    <div class="container-fluid px-vw-5 position-relative" >
        <div class="position-absolute w-100 h-50 back-dark top-0 start-0"></div>

    </div>
    <div class="back-dark py-vh-5">
        <div class="container px-vw-5">
            <div class="row d-flex gx-5 align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="rounded-5 back-clear p-5 shadow" >
                        <div class="fs-1">



                        </div>
                        <p class="lead">"Si j'avais appris l'existence de Landtales plus tôt, je l'aurai utilisé immédiatement pour écrire mes histoires"</p>
                        <div class="d-flex justify-content-start align-items-center border-top border-secondary pt-3">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/77/F%C3%A9lix_Nadar_1820-1910_portraits_Jules_Verne.jpg/1200px-F%C3%A9lix_Nadar_1820-1910_portraits_Jules_Verne.jpg" width="96" height="96" class="rounded-circle me-3" alt="a nice person" >
                            <div>
                                <span class="h6 fw-5">Jules Vernes</span><br>
                                <small>Créateur du <i>Tour du monde en quatre-vingts jours</i></small>
                            </div>
                        </div>
                    </div>
                    <div class="rounded-5 back-clear p-5 shadow mt-5" >

                        <p class="lead">"Les voyages merveilleux des utilsateurs m'ont donnés encore plus d'idées pour mes prochains films. Je sais trouver l'inspiration grâce à Landtales"</p>
                        <div class="d-flex justify-content-start align-items-center border-top border-secondary pt-3">
                            <img src="https://d27csu38upkiqd.cloudfront.net/eyJidWNrZXQiOiJmZGMtc2l0ZXB1YmxpYy1tZWRpYS1wcm9kIiwia2V5IjoidXBsb2Fkc1wvMjAyNFwvMDRcL2x1Y2FzX2hlYWRzaG90X2NvbG9yXzMwMHBwaV9zaXRlLTEuanBnIiwiZWRpdHMiOnsicmVzaXplIjp7IndpZHRoIjoxMDI4LCJmaXQiOiJjb3ZlciJ9fX0=" width="96" height="96" class="rounded-circle me-3" alt="a nice person" loading="lazy">
                            <div>
                                <span class="h6 fw-5">Georges Lucas</span><br>
                                <small>Réalisateur de <i>Star Wars</i></small>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-12 col-lg-6">
                    <div class="p-5 pt-0">
                        <h2 class="display-4">Que pensent les voyageurs de Landtales ?</h2>
                    </div>
                    <div class="rounded-5 back-clear p-5 shadow mt-5">
                        <div class="fs-1">

                        </div>
                        <p class="lead">"Landtales est un oasis pour l'industrie du Jeu-vidéo. Les histoires captivantes des utilisateurs m'inspirent pour créer de nouveaux mondes et aventures. Merci Landtales !"</p>
                        <div class="d-flex justify-content-start align-items-center border-top pt-3">
                            <img src="Design/Pictures/Landing/landing11.jpg" width="96" height="96" class="rounded-circle me-3" alt="a nice person" loading="lazy">
                            <div>
                                <span class="h6 fw-5">Shigeru Miyamoto</span><br>
                                <small>Créateur de <i>Super Mario Bros</i>, <i>The Legend of Zelda</i> et d'autres licences à succès</small>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-12 text-center mt-5">
                <a href="register.php" class="btn btn-xl btn-light mx-3">Rejoindre Landtales</a>
                <a href="login.php" class="btn btn-xl btn-light mx-3">Se connecter</a>
            </div>
        </div>

    </div>

</main>

<script src="Structure/Functions/bootstrap.js"></script>
</body>
</html>
