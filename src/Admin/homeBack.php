<?php
session_start();
$pageTitle = "Landtales | Administrateur";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    require "../Structure/Bdd/config.php";

    $userInfo = $bdd->prepare('SELECT * FROM client WHERE id = ?');
    $userInfo->execute(array($userId));
    $userExists = $userInfo->fetch();



    require "Structures/Head/headAdmin.php";
} else {

    header("Location: login.php");
    exit();
}


?>   <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <link rel="stylesheet" href="../Design/Css/style.css">
    <link rel="stylesheet" href="Design/Css/home-admin.css">
</head>

<body data-bs-theme="light">
<?php require "Structures/Navbar/navbarAdmin.php";?>
<div class="wrapper">

    <?php require "Structures/Sidebar/sidebarAdmin.php";?>

    <div class="main mt-5">
        <div class="text-center mt-5">
            <h1>Bienvenue dans la gestion gestion des univers</h1>
        </div>
        <div class="container admin-page">
            <div class="row">
                <a href="#" tabindex="0" title="Gestion de la base de données" class="col-md-6 big-square bento color-fond">
                    <i class="fa-solid fa-database icon-page"></i>
                    <h3 class="page-title">Gestion de la base de données</h3>
                </a>

                <div class="col-md-6 bento-1">
                    <div class="row bento-2">
                        <a href="#" tabindex="0" title="Logs du site" class="col-md-6 little-square bento color-fond">
                            <i class="fa-solid fa-eye icon-page"></i>
                            <h3 class="page-title">Logs du site</h3>
                        </a>
                        <a href="#" tabindex="0" title="Captcha" class="col-md-6 little-square bento color-fond">
                            <i class="fa-solid fa-shield-halved icon-page"></i>
                            <h3 class="page-title">Captcha</h3>
                        </a>
                    </div>
                    <a href="#" tabindex="0" title="Gestion de la customisation" class="rectangle bento color-fond">
                        <i class="fa-solid fa-cube icon-page"></i>
                        <h3 class="page-title">Gestion de la customisation</h3>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 bento-3">
                    <a href="#" tabindex="0" title="Modification de votre profil administrateur" class="rectangle bento-4 color-fond">
                        <i class="fa-solid fa-user icon-page"></i>
                        <h3 class="page-title">Modification de votre profil administrateur</h3>
                    </a>
                    <a href="newsletter.php" tabindex="0" title="Gestion de la Newsletter" class="rectangle bento-4 color-fond">
                        <i class="fa-solid fa-envelope icon-page"></i>
                        <h3 class="page-title">Gestion de la Newsletter</h3>
                    </a>
                </div>
                <a href="#" tabindex="0" title="Gestion des tickets" class="col-md-6 big-square color-fond">
                    <i class="fa-solid fa-clipboard-list icon-page"></i>
                    <h3 class="page-title">Gestion des tickets</h3>
                </a>
            </div>
        </div>

    </div>
</div>
<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>
</body>

</html>