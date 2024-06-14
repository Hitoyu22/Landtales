<?php
session_start();

require "Structure/Functions/function.php";

if (!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}

require "Structure/Bdd/config.php";
$userId = $_SESSION['idclient'];
$pseudo = $_SESSION['pseudo'];
checkUserRole();

// Définition de la variable $travels
$travels = [];

// Traitement du formulaire POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['popular'])) {
        $query = 'SELECT id, miniature, title, idclient FROM travel WHERE travel_status = 1 AND visibility = 1 ORDER BY (SELECT COUNT(*) FROM travel_view WHERE travel_view.idtravel = travel.id) DESC';
        $travels = $bdd->query($query)->fetchAll();
    } elseif (isset($_POST['young'])) {
        $query = 'SELECT id, miniature, title, idclient FROM travel WHERE travel_status = 1 AND visibility = 1 ORDER BY travel_date DESC';
        $travels = $bdd->query($query)->fetchAll();
    } elseif (isset($_POST['old'])) {
        $query = 'SELECT id, miniature, title, idclient FROM travel WHERE travel_status = 1 AND visibility = 1 ORDER BY travel_date ASC';
        $travels = $bdd->query($query)->fetchAll();
    } elseif (isset($_POST['theme']) && !empty($_POST['theme'])) {
        $query = 'SELECT id, miniature, title, idclient FROM travel WHERE travel_status = 1 AND visibility = 1 AND idtheme = ? ORDER BY travel_date ASC';
        $travelThemeList = $bdd->prepare($query);
        $travelThemeList->execute([$_POST['theme']]);
        $travels = $travelThemeList->fetchAll();
    } else {
        $query = 'SELECT id, miniature, title, idclient FROM travel WHERE travel_status = 1 AND visibility = 1';
        $travelAll = $bdd->query($query);
        $travels = $travelAll->fetchAll();
    }
} else {
    // Si aucun POST n'a été envoyé, récupérez tous les voyages par défaut
    $query = 'SELECT id, miniature, title, idclient FROM travel WHERE travel_status = 1 AND visibility = 1';
    $travelAll = $bdd->query($query);
    $travels = $travelAll->fetchAll();
}

$travelTheme = $bdd->prepare("SELECT id, theme_name FROM travel_theme");
$travelTheme->execute();
$travelThemeValue = $travelTheme->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = "Tous les voyages de Landtales";

$logPath = "Admin/Structures/Logs/log.txt";
$pageAction = "Découvrir les voyages";
$pageId = 3;
$logType = "Visite";

logActivity($userId, $pseudo, $pageId, $logType, $logPath);

require "Structure/Head/head.php";

searchNothing();

$theme = 'light';
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
    <div class="main mt-5">
        <div class="container mt-5">
            <h2 class="mb-4">Découvrez les voyages de Landtales</h2>

            <div class="row mb-5">
                <form method="post" action="">
                    <div class="mb-3">
                        <button type="submit" class="btn-landtales" name="popular">Les plus populaires</button>
                        <button type="submit" class="btn-landtales" name="young">Les plus récents</button>
                        <button type="submit" class="btn-landtales"  name="old">Les plus anciens</button>
                    </div>

                    <div class="col-md-6 col-12">
                        <select class="form-control w-100" id="genre" name="theme" onchange="this.form.submit()">
                            <option value="" selected>Choisir un thème</option>
                            <?php foreach ($travelThemeValue as $theme){ ?>
                                <option value="<?php echo $theme['id']; ?>">
                                    <?php echo $theme['theme_name']; ?>
                                </option>
                            <?php }; ?>
                        </select>
                    </div>
                </form>
            </div>


            <div class="row">
                <?php if (!empty($travels)) {
                    foreach ($travels as $travel) {
                        $idtravel = $travel['id'];
                        $miniature = $travel['miniature'];
                        $title = $travel['title'];
                        $creator = $travel['idclient'];

                        $creatorName = $bdd->prepare("SELECT pseudo FROM client WHERE id = ?");
                        $creatorName->execute([$creator]);
                        $userCreator = $creatorName->fetch();
                        $creatorTravel = $userCreator['pseudo'];

                        $viewCountQuery = $bdd->prepare('SELECT COUNT(*) AS view_count FROM travel_view WHERE idtravel = ?');
                        $viewCountQuery->execute([$idtravel]);
                        $viewCountResult = $viewCountQuery->fetch();
                        $viewNumber = $viewCountResult['view_count'];?>

                        <div class="col-md-3 mb-4 travel-container">
                            <a href="travel.php?id=<?php echo $idtravel; ?>" class="text-decoration-none" title="<?php echo $title; ?>">
                                <div class="card img-content-2 img-content card-square">
                                    <img class="card-img-top" src="<?php echo $miniature; ?>">
                                </div>
                                <h5><?php echo $title; ?></h5>
                                <p class="mb-0">Par <?php echo $creatorTravel; ?></p>
                                <p><?php echo ($viewNumber > 1 ? $viewNumber . " vues" : $viewNumber . " vue") ?></p>
                            </a>
                        </div>
                    <?php }
                } else {
                    ?>
                    <div class="col-12 text-center">
                        <p>Aucun voyage n'a été trouvé.</p>
                        <button class="btn btn-primary" onclick="window.location.href='travelLobby.php'">Réinitialiser</button>
                    </div>
                    <?php
                } ?>
            </div>

        </div>
    </div>
</div>


<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>
