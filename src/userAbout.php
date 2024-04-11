<?php
session_start();

$pageTitle = "Paramètres - Vos voyages";


require "Structure/Functions/function.php";

if(isset($_GET['id'])) {
    $userId = $_GET['id'];
} else {
    if (isset($_SESSION['idclient'])) {
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
    $userDescription = isset($user['summary']) ? $user['summary'] : "";
    $userFacebook = isset($user['facebook']) ? $user['facebook'] : "";
    $userInstagram = isset($user['insta']) ? $user['insta'] : "";
    $userTwitter = isset($user['twitter']) ? $user['twitter'] : "";
    $userYouTube = isset($user['youtube']) ? $user['youtube'] : "";
    $userGitHub = isset($user['github']) ? $user['github'] : "";


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
                            <div id="multicouche">
                            <div class="profile-container" id="couche1">
                                <img src="<?php echo $userIconPath; ?>" alt="Image de profil" class="profile-img" >
                            </div>
                                <img src="http://localhost/src/Design/Pictures/Lovepik_com-401355212-avatar-box.png" style
                                     alt="Image de profil" class="customisation" id="couche2">
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


            <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center bg-transparent" style="z-index: 100;"> <!-- Définissez un z-index inférieur -->
                <div class="container-fluid">
                    <ul class="navbar-nav" style="margin: 0 auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="userProfil.php?id=<?php echo $userId; ?>">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="userTravel.php?id=<?php echo $userId; ?>">Voyages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="userAbout.php?id=<?php echo $userId; ?>"><u>A propos</u></a>
                        </li>

                    </ul>
                </div>
            </nav>

                    <div class="col-12 mb-5">
                        <div class="row justify-content-lg-between justify-content-center">
                            <div class="col-lg-10 col-12 mb-3">
                                <?php echo $userDescription; ?>
                            </div>
                            <div class="col-lg-2 col-12 text-lg-right text-center">
                                <div class="d-lg-none">
                                    <?php
                                    $iconSize = 'icon-xl';
                                    if (!empty($userFacebook)) {
                                        echo '<a href="' . $userFacebook . '" target="_blank" class="me-3"><i class="lni lni-facebook-original ' . $iconSize . '"></i></a>';
                                    }

                                    if (!empty($userInstagram)) {
                                        echo '<a href="' . $userInstagram . '" target="_blank" class="me-3"><i class="lni lni-instagram ' . $iconSize . '"></i></a>';
                                    }

                                    if (!empty($userTwitter)) {
                                        echo '<a href="' . $userTwitter . '" target="_blank" class="me-3"><i class="lni lni-twitter-original ' . $iconSize . '"></i></a>';
                                    }

                                    if (!empty($userYouTube)) {
                                        echo '<a href="' . $userYouTube . '" target="_blank" class="me-3"><i class="lni lni-youtube ' . $iconSize . '"></i></a>';
                                    }

                                    if (!empty($userGitHub)) {
                                        echo '<a href="' . $userGitHub . '" target="_blank"><i class="lni lni-github-original ' . $iconSize . '"></i></a>';
                                    }
                                    ?>
                                </div>
                                <div class="d-none d-lg-block">
                                    <?php
                                    if (!empty($userFacebook)) {
                                        echo '<p><a href="' . $userFacebook . '" target="_blank"><i class="lni lni-facebook-original"></i> Facebook</a></p>';
                                    }

                                    if (!empty($userInstagram)) {
                                        echo '<p><a href="' . $userInstagram . '" target="_blank"><i class="lni lni-instagram"></i> Instagram</a></p>';
                                    }

                                    if (!empty($userTwitter)) {
                                        echo '<p><a href="' . $userTwitter . '" target="_blank"><i class="lni lni-twitter-original"></i> Twitter</a></p>';
                                    }

                                    if (!empty($userYouTube)) {
                                        echo '<p><a href="' . $userYouTube . '" target="_blank"><i class="lni lni-youtube"></i> YouTube</a></p>';
                                    }

                                    if (!empty($userGitHub)) {
                                        echo '<p><a href="' . $userGitHub . '" target="_blank"><i class="lni lni-github-original"></i> GitHub</a></p>';
                                    }
                                    ?>
                                </div>
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
