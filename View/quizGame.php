<?php
session_start();
require "Structure/Functions/function.php";

if (!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['idclient'];
checkUserRole();
require "Structure/Bdd/config.php";

$quizId = isset($_GET['id']) ? $_GET['id'] : null;
$questionIndex = isset($_GET['question']) ? (int)$_GET['question'] : 1;

$quizInfo = $bdd->prepare("SELECT title FROM quiz WHERE id = ?");
$quizInfo->execute([$quizId]);
$quizData = $quizInfo->fetch();

if (!$quizData) {
    header("Location: quizLobby.php");
    exit();
}

$checkCompletion = $bdd->prepare("SELECT coin_added, question_responded FROM client_answer WHERE idclient = ? AND idquiz = ?");
$checkCompletion->execute([$userId, $quizId]);
$completion = $checkCompletion->fetch();

if ($completion) {
    if ($completion['coin_added'] == 1) {
        header("Location: results.php?id=$quizId");
        exit();
    }
    $respondedQuestions = (int) $completion['question_responded'];
    if ($questionIndex <= $respondedQuestions) {
        $nextQuestionIndex = $respondedQuestions + 1;
        header("Location: {$_SERVER['PHP_SELF']}?id=$quizId&question=$nextQuestionIndex");
        exit();
    }
}

$getQuestionInfoQuery = $bdd->prepare("SELECT question,question_picture,id FROM question WHERE idquiz = :quizId LIMIT 1 OFFSET :offset");
$getQuestionInfoQuery->bindValue(':quizId', $quizId, PDO::PARAM_INT);
$getQuestionInfoQuery->bindValue(':offset', $questionIndex - 1, PDO::PARAM_INT);
$getQuestionInfoQuery->execute();
$questionInfo = $getQuestionInfoQuery->fetch(PDO::FETCH_ASSOC);

if (!$questionInfo) {
    header("Location: results.php?id=$quizId");
    exit();
}

$idquestion = isset($questionInfo['id']) ? $questionInfo['id'] : null;

$getCorrectAnswersQuery = $bdd->prepare("SELECT id FROM answer WHERE idquestion = :idquestion AND correct = 1");
$getCorrectAnswersQuery->bindValue(':idquestion', $idquestion, PDO::PARAM_INT);
$getCorrectAnswersQuery->execute();
$correctAnswerIds = $getCorrectAnswersQuery->fetchAll(PDO::FETCH_COLUMN, 0);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nextQuestion'])) {
    $userAnswers = $_POST['answers'] ?? [];
    $isCorrect = !array_diff($correctAnswerIds, $userAnswers) && count($userAnswers) == count($correctAnswerIds);

    $checkEntry = $bdd->prepare("SELECT score FROM client_answer WHERE idclient = ? AND idquiz = ?");
    $checkEntry->execute([$userId, $quizId]);
    $entry = $checkEntry->fetch(PDO::FETCH_ASSOC);

    if ($entry) {
        if ($isCorrect) {
            $updateScore = $bdd->prepare("UPDATE client_answer SET score = score + 1 WHERE idclient = ? AND idquiz = ?");
            $updateScore->execute([$userId, $quizId]);
        }
    } else {
        $initialScore = $isCorrect ? 1 : 0;
        $createEntry = $bdd->prepare("INSERT INTO client_answer (idclient, idquiz, score) VALUES (?, ?, ?)");
        $createEntry->execute([$userId, $quizId, $initialScore]);
    }

    $updateResponded = $bdd->prepare("UPDATE client_answer SET question_responded = ? WHERE idclient = ? AND idquiz = ?");
    $updateResponded->execute([$questionIndex, $userId, $quizId]);
    $questionIndex++;
    header("Location: quizGame.php?id=$quizId&question=$questionIndex");
    exit();
}

$pageTitle = "Question d'un quiz";
require "Structure/Head/head.php";
$theme = $_COOKIE['theme'] ?? 'light';
?>
<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
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
                    <div class="text-start">
                        <h1 id="gamequiztitle" class="ml-0"><?php echo htmlspecialchars($quizData['title']); ?></h1>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <h2 id="quiztext">Question <?php echo $questionIndex; ?> : <?php echo html_entity_decode($questionInfo['question']); ?></h2>
                    </div>
                </div>
                <?php if (!empty($questionInfo['question_picture'])): ?>
                    <div class="row " id="quizimg">
                        <img class="img-quiz px-0" src="<?php echo htmlspecialchars($questionInfo['question_picture']); ?>">
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-12">
                        <form method="POST" action="" class="row">
                            <?php
                            $answerQuestion = $bdd->prepare("SELECT id, answer FROM answer WHERE idquestion = ?");
                            $answerQuestion->execute([$idquestion]);
                            $answers = $answerQuestion->fetchAll(PDO::FETCH_ASSOC);

                            if (!empty($answers)) {
                                foreach ($answers as $answer) {
                                    if (!empty($answer['answer'])) { ?>
                                        <div class="form-check col-md-6 col-12 mb-2">
                                            <input class="btn-check" type="checkbox" autocomplete="off" name="answers[]" value="<?php echo htmlspecialchars($answer['id']); ?>" id="answer<?php echo htmlspecialchars($answer['id']); ?>">
                                            <label class="btn btn-outline-secondary w-100" for="answer<?php echo htmlspecialchars($answer['id']); ?>">
                                                <?php echo html_entity_decode($answer['answer']); ?>
                                            </label>
                                        </div>
                                    <?php }
                                }
                            } else { ?>
                                <p>Aucune réponse disponible pour cette question.</p>
                            <?php } ?>
                            <div class="col-12 text-end">
                                <button type="submit" name="nextQuestion" class="btn btn-light btnequizgame" id="quizbtn">Question Suivante</button>
                            </div>
                        </form>
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
