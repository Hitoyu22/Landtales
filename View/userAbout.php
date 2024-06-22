<?php
session_start();

$pageTitle = "Profil - A propos";


require "Structure/Functions/function.php";

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    checkUserRole();
} else {
    if (isset($_SESSION['idclient'])) {
        $userId = $_SESSION['idclient'];
    } else {
        header("Location: login.php");
        exit();
    }
}

require "Structure/Bdd/config.php";

$userInfo = $bdd->prepare('SELECT banner, profil_picture, summary, facebook, insta, twitter, youtube, github, idcustomisation, idrank, drawing, visibility, firstname, lastname, pseudo, permaBan FROM client WHERE id = ?');
$userInfo->execute([$userId]);
$user = $userInfo->fetch();

if ($user && $user['idrank'] == 2 || $user['permaBan'] == 1) {
    header("Location: userAbout.php");
    exit();
}

if ($user === false) {
    header("Location: userAbout.php");
    exit();
}

if (isset($_GET['id']) && isset($_SESSION['idclient']) && $user['visibility'] == 2 && $_GET['id'] != $_SESSION['idclient']) {
    header("Location: userAbout.php");
    exit();
}


require "friendRequest.php";

    $userBannerPath = isset($user['banner']) ? $user['banner'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
    $userIconPath = isset($user['profil_picture']) ? $user['profil_picture'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
    $userDescription = isset($user['summary']) ? $user['summary'] : "";
    $userFacebook = isset($user['facebook']) ? $user['facebook'] : "";
    $userInstagram = isset($user['insta']) ? $user['insta'] : "";
    $userTwitter = isset($user['twitter']) ? $user['twitter'] : "";
    $userYouTube = isset($user['youtube']) ? $user['youtube'] : "";
    $userGitHub = isset($user['github']) ? $user['github'] : "";
    $customUser = isset($user['idcustomisation']) ? $user['idcustomisation'] : "";
    $drawingPath = isset($user['drawing']) ? $user['drawing'] : "";



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


            <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center bg-transparent" style="z-index: 100;">
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
                                <div>
                                    <?php echo $userDescription; ?>
                                </div>

                                <div>
                                    <?php
                                    if (isset($drawingPath) && $drawingPath != NULL){
                                        echo "<img alt='Dessin du voyageur' src='$drawingPath' >";
                                    }
                                    ?>
                                </div>

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
