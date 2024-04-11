<?php
session_start();

$pageTitle = "Paramètres - Vos voyages";

require "Structure/Functions/function.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    require "Structure/Bdd/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['signal'])){
            $report = htmlspecialchars($_POST['report']);
            $error = var_dump($report); // Ajout du var_dump pour vérifier la valeur
            $ticketType = "Signalement d'un utilisateur";

            $reportInfo = $bdd->prepare('INSERT INTO ticket (id, summary, creation_date, ticket_type) VALUES (?, ?, NOW(), ?)');
            $reportInfo->execute(array($userId, $report, $ticketType));

            header("Location: {$_SERVER['REQUEST_URI']}");
        }
    }
    


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
        <div class="container mt-5">
            <h1 class="mx-0">Paramètres</h1>
            <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center bg-transparent" style="z-index: 100;">
                <div class="container-fluid">
                    <ul class="navbar-nav" style="margin: 0 auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="profileSettings.php">Général</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileCustom.php">Modifier le profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileTravel.php">Vos voyages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileConfidentiality.php">Confidentialité</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileReporting.php"><u>Signalement</u></a>
                        </li>
                    </ul>
                </div>
            </nav>

            <form method="post" action="">
                <div class="form-group mb-5">
                    <h5><label for="report">Vous avez rencontrez un problème lors de votre voyage ? Informez nous dès maintenant afin que l’on puisse régler le problème.</label></h5>
                    <textarea class="form-control" id="report" rows="10" name="report" placeholder="Veuillez nous expliquer le problème que vous avez rencontré..."></textarea>
                </div>

                <button type="submit" id="publish" class="btn-landtales mb-5" name="signal">Envoyer un signalement</button>
            </form>

        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>

    </div>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>