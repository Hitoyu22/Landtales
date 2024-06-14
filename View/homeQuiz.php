<?php
session_start();
require "Structure/Functions/function.php";

if (!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}

    $userId = $_SESSION['idclient'];
    checkUserRole();
    $quizId = isset($_GET['id']) ? $_GET['id'] : null;
    require "Structure/Bdd/config.php";

$quizInfo = [];
$buttonText = "Faire le quiz";
$buttonLink = "quizGame.php?id=$quizId&question=1";
$buttonDisabled = "";

if ($quizId) {
    $getQuizInfoQuery = $bdd->prepare("SELECT quiz_picture,title,difficulty_level,potential_gain,universe,summary FROM quiz WHERE id = ?");
    $getQuizInfoQuery->execute([$quizId]);
    $quizInfo = $getQuizInfoQuery->fetch(PDO::FETCH_ASSOC);

    if (!$quizInfo) {
        header("Location: quizLobby.php");
        exit();
    }


    $checkAnswerQuery = $bdd->prepare("SELECT coin_added, question_responded FROM client_answer WHERE idquiz = ? AND idclient = ?");
    $checkAnswerQuery->execute([$quizId, $userId]);
    $answerInfo = $checkAnswerQuery->fetch(PDO::FETCH_ASSOC);

    if ($answerInfo) {
        if ($answerInfo['coin_added'] == 1) {
            $buttonText = "Vous avez déjà fait ce quiz";
            $buttonDisabled = "disabled";
        } else {
            $nextQuestion = $answerInfo['question_responded'] + 1;
            $buttonText = "Reprendre le quiz";
            $buttonLink = "quizGame.php?id=$quizId&question=$nextQuestion";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'delete_quiz') {
    if ($_SESSION["rank"] === 3) {
        $id_quiz = $_GET['id'];

        deleteQuiz($bdd, $id_quiz);

        header("Location: quizLobby.php?delete=success");
    }
}



$pageTitle = $quizInfo['title'];

require "Structure/Head/head.php";
$theme = $_COOKIE['theme'] ?? 'light';
?>
<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/quiz.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">

<?php require "Structure/Navbar/navbar.php"; ?>
<div class="wrapper">
    <?php require "Structure/Sidebar/sidebar.php"; ?>
    <div class="main mt-5">
        <div class="mx-5 mt-5">
            <div class="container text-center">
                <div class="row" id="quizdiv">
                    <div class="col-md px-0 img1">
                        <img class="img-quiz" src="<?php echo htmlspecialchars($quizInfo['quiz_picture']); ?>">

                    </div>
                    <div class="col-md">
                        <div class="column">
                            <div class="col-md d-flex flex-column text-start" >
                                <h1 id="quiztitle" class="ml-0"><?php echo html_entity_decode($quizInfo['title']); ?></h1>
                                <h3 class="mainquiz">Difficulté : <?php for ($i = 0; $i < $quizInfo['difficulty_level']; $i++){ echo "★"; } ?></h3>
                                <h3 class="mainquiz">Pièces maximales obtenables : <?php echo html_entity_decode($quizInfo['potential_gain']); ?></h3>
                                <h3 class="mainquiz">Univers : <?php echo html_entity_decode($quizInfo['universe']); ?></h3>
                            </div>
                            <div class="col-md text-center mt-3 " id="btnquizdiv">
                                <button type="button" class="btn btn-light btnequiz mb-5" <?php echo $buttonDisabled; ?> onclick="window.location.href='<?php echo $buttonLink; ?>'" role="button"><?php echo $buttonText; ?></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row text-start">
                    <p id="desc"><?php echo html_entity_decode($quizInfo['summary']); ?></p>
                </div>

                <?php
                if ($_SESSION["rank"] === 3) {
                    echo '
            <button type="button" name="delete_quiz" class="btn btn-danger" onclick="confirmDelete()">Supprimer le quiz</button>
            ';
                }
                ?>



            </div>
        </div>
        <?php require "Structure/Footer/footer.php"; ?>
    </div>
</div>
<script>
    function confirmDelete() {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?')) {
            var form = document.createElement('form');
            form.method = 'post';
            form.action = '';
            form.style.display = 'none';

            var inputAction = document.createElement('input');
            inputAction.type = 'hidden';
            inputAction.name = 'action';
            inputAction.value = 'delete_quiz';

            form.appendChild(inputAction);

            document.body.appendChild(form);

            form.submit();
        }
    }
</script>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>