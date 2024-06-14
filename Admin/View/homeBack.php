<?php
require "Structure/Functions/function.php";
session_start();
$pageTitle = "Landtales | Administrateur";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];

    checkAdminRole();
    require "Structure/Bdd/config.php";




    require "Admin/Structures/Head/headAdmin.php";
} else {

    header("Location: login.php");
    exit();
}

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>   <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <link rel="stylesheet" href="../Design/Css/style.css">
    <link rel="stylesheet" href="Design/Css/home-admin.css">
</head>

<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php";?>
<div class="wrapper">

    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php";?>

    <div class="main mt-5">
        <div class="text-center mt-5">
            <h1 class="mx-5">Bienvenue dans la gestion des univers</h1>
        </div>
        <div class="container admin-page">
            <div class="row">
                <a href="databaseGestion.php" tabindex="0" title="Gestion de la base de données" class="col-md-6 big-square bento color-fond">
                    <i class="lni lni-database icon-page"></i>
                    <h3 class="page-title">Gestion de la base de données</h3>
                </a>

                <div class="col-md-6 bento-1">
                    <div class="row bento-2">
                        <a href="logs.php" tabindex="0" title="Logs du site" class="col-md-6 little-square bento color-fond">
                            <i class="lni lni-eye icon-page"></i>
                            <h3 class="page-title">Logs du site</h3>
                        </a>
                        <a href="captcha.php" tabindex="0" title="Captcha" class="col-md-6 little-square bento color-fond">
                            <i class="lni lni-protection icon-page"></i>
                            <h3 class="page-title">Captcha</h3>
                        </a>
                    </div>
                    <a href="custom.php" tabindex="0" title="Gestion de la customisation" class="rectangle bento color-fond">
                        <i class="lni lni-laravel icon-page"></i>
                        <h3 class="page-title">Gestion de la customisation</h3>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 bento-1">
                    <a href="theme.php" tabindex="0" title="Thèmes des voyages" class="rectangle bento color-fond">
                        <i class="lni lni-pencil-alt icon-page"></i>
                        <h3 class="page-title">Thèmes des voyages</h3>
                    </a>
                    <div class="row bento-2">
                        <a href="userRight.php" tabindex="0" title="Gestion des droits des utilisateurs" class="col-md-6 little-square bento color-fond">
                            <i class="lni lni-user icon-page"></i>
                            <h3 class="page-title">Gestion des droits des utilisateurs</h3>
                        </a>
                        <a href="newsletter.php" tabindex="0" title="Gestion de la Newsletter" class="col-md-6 little-square bento color-fond">
                            <i class="lni lni-envelope icon-page"></i>
                            <h3 class="page-title">Gestion de la Newsletter</h3>
                        </a>
                    </div>

                </div>
                <a href="ticketLobby.php" tabindex="0" title="Gestion des tickets" class="col-md-6 big-square color-fond">
                    <i class="lni lni-ticket-alt icon-page"></i>
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