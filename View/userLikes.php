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


$userLikes= $bdd->prepare("SELECT idtravel FROM travel_like WHERE idclient = ?");
$userLikes->execute([$userId]);
$likes = $userLikes->fetchAll();

$pageTitle = "Les voyages aimés";

$logPath = "Admin/Structures/Logs/log.txt";
$pageAction = "Mes voyages aimés";
$pageId = 7;
$logType = "Visite";

logActivity($userId, $pseudo, $pageId, $logType, $logPath);

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
    <div class="main mt-5">
        <div class="container mt-5">
            <h2 class="mb-4">Vos voyages aimés</h2>
            <div class="row">
            <?php
            foreach ($likes as $like) {

                $travelByLike = $bdd->prepare("SELECT id,miniature,title,idclient FROM travel WHERE id = ? ");
                $travelByLike->execute([$like['idtravel']]);
                $travel = $travelByLike->fetch();

                    $idtravel = $travel['id'];
                    $miniature = $travel['miniature'];
                    $title = $travel['title'];
                    $creator = $travel['idclient'];

                    $creatorName = $bdd->prepare("SELECT pseudo FROM client WHERE id = ?");
                    $creatorName->execute([$creator]);
                    $userCreator = $creatorName->fetch(); // This fetches the next row from the result set as an array
                    $creatorTravel = $userCreator['pseudo'];

                    $viewCountQuery = $bdd->prepare('SELECT COUNT(idtravel) AS view_count FROM travel_view WHERE idtravel = ?');
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

            <?php } ?>
            </div>

        </div>
    </div>
</div>


<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>