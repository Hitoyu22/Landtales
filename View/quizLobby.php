<?php
session_start();

require "Structure/Functions/function.php";

if (!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}

require "Structure/Bdd/config.php";
checkUserRole();
$userId = $_SESSION['idclient'];
$pseudo = $_SESSION['pseudo'];


$allQuiz= $bdd->prepare("SELECT id,quiz_picture,title FROM quiz");
$allQuiz->execute();
$everyQuiz = $allQuiz->fetchAll();

$pageTitle = "Tous les voyages de Landtales";

$logPath = "Admin/Structures/Logs/log.txt";
$pageAction = "Découvrir les quiz";
$pageId = 5;
$logType = "Visite";

logActivity($userId, $pseudo, $pageId, $logType, $logPath);

require "Structure/Head/head.php";

quizSupp();
searchNothing();


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
            <h2 class="mb-4">Découvrez les quiz de Landtales</h2>
            <div class="row">
                <?php
                foreach ($everyQuiz as $quiz) {

                    $idQuiz = $quiz['id'];
                    $miniature = $quiz['quiz_picture'];
                    $title = $quiz['title'];

                    $quizDone = $bdd->prepare('SELECT COUNT(*) AS quizDone FROM client_answer WHERE idquiz = ?');
                    $quizDone->execute([$idQuiz]);
                    $numberDone = $quizDone->fetch();
                    ?>

                    <div class="col-md-3 mb-4 travel-container">
                        <a href="homeQuiz.php?id=<?php echo $idQuiz; ?>" class="text-decoration-none" title="<?php echo $title; ?>">
                            <div class="card img-content-2 img-content card-square">
                                <img class="card-img-top" src="<?php echo $miniature; ?>">
                            </div>
                            <h5><?php echo $title; ?></h5>
                            <p>Fait <?php echo $numberDone['quizDone']; ?> fois</p>
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