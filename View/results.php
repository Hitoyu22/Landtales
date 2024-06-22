<?php
session_start();
require "Structure/Functions/function.php";

if (!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['idclient'];
$pseudo = $_SESSION['pseudo'];
checkUserRole();

require "Structure/Bdd/config.php";

$quizId = $_GET['id'] ?? null;
$quizInfo = [];
$potential_gain = 0;

if ($quizId) {
    $getQuizInfoQuery = $bdd->prepare("SELECT potential_gain FROM quiz WHERE id = ?");
    $getQuizInfoQuery->execute([$quizId]);
    $quizInfo = $getQuizInfoQuery->fetch(PDO::FETCH_ASSOC);

    if ($quizInfo && array_key_exists('potential_gain', $quizInfo)) {
        $potential_gain = $quizInfo['potential_gain'];
    }
}

$numberQuestionsQuery = $bdd->prepare("SELECT COUNT(idquiz) FROM question WHERE idquiz = ?");
$numberQuestionsQuery->execute([$quizId]);
$number = $numberQuestionsQuery->fetchColumn();

$resultsQuery = $bdd->prepare("SELECT score,coin_added FROM client_answer WHERE idquiz = ? AND idclient = ?");
$resultsQuery->execute([$quizId, $userId]);
$resultat = $resultsQuery->fetch(PDO::FETCH_ASSOC);

$coinWin = 0; // Définition de $coinWin à zéro par défaut

if ($resultat !== false) {
    $score = $resultat['score'];

    if (!$resultat['coin_added']) {
        // Calcul du pourcentage de bonnes réponses
        $percentage_correct = ($score / $number) * 100;

        // Calcul du nombre de pièces à attribuer
        $coinWin = intval(($percentage_correct / 100) * $potential_gain);

        // Assurez-vous que le nombre de pièces gagnées est au maximum le nombre maximum de pièces possible
        $coinWin = min($coinWin, $potential_gain);

        // Mise à jour des pièces de l'utilisateur
        $updateCoinUserQuery = $bdd->prepare("UPDATE client SET coin = coin + ? WHERE id = ?");
        $updateCoinUserQuery->execute([$coinWin, $userId]);

        // Marquer que les pièces ont été ajoutées
        $markCoinsAdded = $bdd->prepare("UPDATE client_answer SET coin_added = TRUE WHERE idquiz = ? AND idclient = ?");
        $markCoinsAdded->execute([$quizId, $userId]);
    }
} else {
    $score = 0; // ou gérer le cas d'absence de résultats d'une autre manière
}

$pageTitle = "Résultat du quiz";
require "Structure/Head/head.php";

$theme = $_COOKIE['theme'] ?? 'light'; // Utilisation de l'opérateur null coalesce
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet">
    <link rel="stylesheet" href="Design/Css/quiz.css">
    <link rel="stylesheet" href="Design/Css/style.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">
    <?php require "Structure/Sidebar/sidebar.php";?>

    <div class="main mt-5">
        <div class="mx-5 mt-5">
            <div class="container text-center">
                <div class="row">
                    <div class="col-md-6 text-start">
                        <h2 id="resulttitle">Voici votre résultat :</h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 d-flex flex-column mb-3 text-center justify-content-center align-items-center" id="quizresult">
                        <h1 id="resultscore" class="text-dark"><?php echo $score .'/'. $number; ?></h1>
                        <h3 id="resulttext" class="text-dark">Bonnes Réponses</h3>
                        <h3 id="resultcoins" class="text-dark">Pièces gagnées : <?php echo $coinWin; ?></h3>
                    </div>
                </div>

                <div class="col-md-12 text-center mt-3 btnediv">
                    <button type="button" class="btn btn-light btne mb-5" onclick="window.location.href='quizLobby.php'" role="button" id="quizbtn">Retour à la page des quiz</button>
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
