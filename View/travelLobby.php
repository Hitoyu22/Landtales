<?php
session_start();

require "Structure/Functions/function.php";
require "Structure/Functions/travelAlgo.php";
require "Structure/Functions/alerts.php";



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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['reset'])) {
        // Réinitialisation des paramètres POST
        unset($_POST['popular']);
        unset($_POST['young']);
        unset($_POST['old']);
        unset($_POST['theme']);
    }

    function fetchTravels($bdd, $orderBy) {
        $query = "
        SELECT t.id, t.miniature, t.title, t.idclient, c.pseudo AS creator_name, COUNT(tv.idtravel) AS view_count
        FROM travel t
        LEFT JOIN client c ON t.idclient = c.id
        LEFT JOIN travel_view tv ON t.id = tv.idtravel
        WHERE t.travel_status = 1 AND t.visibility = 1
        GROUP BY t.id, t.miniature, t.title, t.idclient, c.pseudo
        ORDER BY $orderBy
    ";

        $stmt = $bdd->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    if (isset($_POST['popular'])) {
        $travels = fetchTravels($bdd, 'view_count DESC');
    } elseif (isset($_POST['young'])) {
        $travels = fetchTravels($bdd, 't.travel_date DESC');
    } elseif (isset($_POST['old'])) {
        $travels = fetchTravels($bdd, 't.travel_date ASC');
    } else if (isset($_POST['theme']) && !empty($_POST['theme'])) {
            $query = "
            SELECT t.id, t.miniature, t.title, t.idclient, c.pseudo AS creator_name, COUNT(tv.idtravel) AS view_count
            FROM travel t
            LEFT JOIN client c ON t.idclient = c.id
            LEFT JOIN travel_view tv ON t.id = tv.idtravel
            WHERE t.travel_status = 1 AND t.visibility = 1
            AND t.idtheme = ?
            GROUP BY t.id, t.miniature, t.title, t.idclient, c.pseudo
            ORDER BY t.travel_date ASC
        ";
        $travelThemeList = $bdd->prepare($query);
        $travelThemeList->execute([$_POST['theme']]);
        $travels = $travelThemeList->fetchAll();
    } else {
        $travelAlgo = recommendationAlgorithm($bdd, $userId);

        $str = "";
        if (empty($travelAlgo)) {
            $str = "('')";
        } else {
            $str = "(".implode(', ', $travelAlgo).")";
        }

        $query = "
                SELECT t.id, t.miniature, t.title, t.idclient, c.pseudo AS creator_name, COUNT(tv.idtravel) AS view_count
                FROM travel t
                LEFT JOIN client c ON t.idclient = c.id
                LEFT JOIN travel_view tv ON t.id = tv.idtravel
                WHERE t.id IN {$str}
                GROUP BY t.id, t.miniature, t.title, t.idclient, c.pseudo
            ";
        $travels = $bdd->prepare($query);
        $travels->execute();
        $travels = $travels->fetchAll();

    }
    } else {
    $travelAlgo = recommendationAlgorithm($bdd, $userId);

    $str = "";
    if (empty($travelAlgo)) {
        $str = "('')";
    } else {
        $str = "(".implode(', ', $travelAlgo).")";
    }

    $query = "
            SELECT t.id, t.miniature, t.title, t.idclient, c.pseudo AS creator_name, COUNT(tv.idtravel) AS view_count
            FROM travel t
            LEFT JOIN client c ON t.idclient = c.id
            LEFT JOIN travel_view tv ON t.id = tv.idtravel
            WHERE t.id IN {$str}
            GROUP BY t.id, t.miniature, t.title, t.idclient, c.pseudo
        ";
    $travels = $bdd->prepare($query);
    $travels->execute();
    $travels = $travels->fetchAll();
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
                        <button type="submit" class="btn-landtales" name="old">Les plus anciens</button>
                        <?php if (isset($_POST['popular']) || isset($_POST['young']) || isset($_POST['old']) || isset($_POST['theme'])) : ?>
                            <button type="submit" class="btn-landtales" name="reset">Réinitialiser</button>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6 col-12">
                        <select class="form-control w-100" id="genre" name="theme" onchange="this.form.submit()">
                            <option value="" <?php echo (!isset($_POST['theme']) || empty($_POST['theme'])) ? 'selected' : ''; ?>>Choisir un thème</option>
                            <?php foreach ($travelThemeValue as $theme) : ?>
                                <option value="<?php echo $theme['id']; ?>" <?php echo (isset($_POST['theme']) && $_POST['theme'] == $theme['id']) ? 'selected' : ''; ?>>
                                    <?php echo $theme['theme_name']; ?>
                                </option>
                            <?php endforeach; ?>
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
                        $creator = $travel['creator_name'];
                        $viewNumber = $travel['view_count'];
                        ?>

                        <div class="col-md-3 mb-4 travel-container">
                            <a href="travel.php?id=<?php echo $idtravel; ?>" class="text-decoration-none" title="<?php echo $title; ?>">
                                <div class="card img-content-2 img-content card-square">
                                    <img class="card-img-top" src="<?php echo $miniature; ?>">
                                </div>
                                <h5><?php echo $title; ?></h5>
                                <p class="mb-0">Par <?php echo $creator; ?></p>
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
