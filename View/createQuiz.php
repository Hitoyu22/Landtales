<?php
session_start();
require "Structure/Functions/function.php";


if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];
    $rank = $_SESSION['rank'];

    $pageTitle = "Création d'un quiz";


    require "Structure/Bdd/config.php";



    require "Structure/Head/head.php";

} else {

    header("Location: login.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $difficulty_level = isset($_POST['difficulty_level']) ? $_POST['difficulty_level'] : null;
    $potential_gain = isset($_POST['potential_gain']) ? $_POST['potential_gain'] : 0;
    $universe = isset($_POST['universe']) ? $_POST['universe'] : null;
    $summary = isset($_POST['summary']) ? $_POST['summary'] : null;

    if ($title && $difficulty_level && $universe && $summary) {
        // Insérer les informations de base du quiz sans l'image pour obtenir l'ID
        $stmt = $bdd->prepare("INSERT INTO quiz (title, difficulty_level, potential_gain, universe, summary) VALUES (:title, :difficulty_level, :potential_gain, :universe, :summary)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':difficulty_level', $difficulty_level);
        $stmt->bindParam(':potential_gain', $potential_gain);
        $stmt->bindParam(':universe', $universe);
        $stmt->bindParam(':summary', $summary);
        $stmt->execute();

        $lastIdQuiz = $bdd->lastInsertId();

        // Chemin de base pour les images du quiz
        $quizImagesPath = "Ressources/Quiz/$lastIdQuiz";
        if (!file_exists($quizImagesPath)) {
            mkdir($quizImagesPath, 0777, true);
        }

        // Gérer l'upload de l'image du quiz
        $quizImagePath = "";  // Initialisation vide pour gérer le cas où aucun fichier n'est uploadé
        if (isset($_FILES['quiz_image']) && $_FILES['quiz_image']['error'] == 0) {
            $quizImagePath = $quizImagesPath . '/' . basename($_FILES['quiz_image']['name']);
            move_uploaded_file($_FILES['quiz_image']['tmp_name'], $quizImagePath);

            $urlQuizImage = 'https://landtales.freeddns.org/' . $quizImagePath;

            // Mettre à jour le chemin de l'image dans la base de données
            $stmt = $bdd->prepare("UPDATE quiz SET quiz_picture = :quiz_picture WHERE id = :id");
            $stmt->bindParam(':quiz_picture', $urlQuizImage);
            $stmt->bindParam(':id', $lastIdQuiz);
            $stmt->execute();
        }

        // Gérer les images des questions
        for ($i = 1; $i <= 10; $i++) {
            $questionImagePath = ""; // Initialiser le chemin de l'image de la question
            if (isset($_FILES['image_question' . $i]) && $_FILES['image_question' . $i]['error'] == 0) {
                $questionImagePath = $quizImagesPath . '/' . basename($_FILES['image_question' . $i]['name']);
                move_uploaded_file($_FILES['image_question' . $i]['tmp_name'], $questionImagePath);
            }

            $question = isset($_POST["question$i"]) ? htmlspecialchars($_POST["question$i"]) : "";
            $urlQuestionImage = 'https://landtales.freeddns.org/' . $questionImagePath;

            if (!empty($question)) {
                $stmt = $bdd->prepare("INSERT INTO question (question, idquiz, question_picture) VALUES (:question, :idquiz, :question_picture)");
                $stmt->bindParam(':question', $question);
                $stmt->bindParam(':idquiz', $lastIdQuiz);
                $stmt->bindParam(':question_picture', $urlQuestionImage);
                $stmt->execute();
                $idQuestion = $bdd->lastInsertId();

                for ($j = 1; $j <= 4; $j++) {
                    $answer = isset($_POST["answer" . $i . "_" . $j]) ? htmlspecialchars($_POST["answer" . $i . "_" . $j]) : "";
                    $correct = isset($_POST["correct" . $i . "_" . $j]) ? 1 : 0;

                    $stmt = $bdd->prepare("INSERT INTO answer (idquestion, answer, correct) VALUES (:idquestion, :answer, :correct)");
                    $stmt->bindParam(':idquestion', $idQuestion);
                    $stmt->bindParam(':answer', $answer);
                    $stmt->bindParam(':correct', $correct);
                    $stmt->execute();
                }
            }
        }


        $logPath = "Admin/Structures/Logs/log.txt";
        $pageAction = "Création d'un quiz";
        $pageId = 4;
        $logType = "Création de quiz";

        logActivity($userId, $pseudo, $pageId, $logType, $logPath);

        header("Location: quizCreationConfirmation.php?id=$lastIdQuiz");
        exit;
    } else {
        // Handle the case where required form fields are missing
        echo "Tous les champs obligatoires doivent être remplis.";
    }
}






$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}

?>
<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/editorjs.css">
<link rel="stylesheet" href="Design/Css/style.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">


    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main mt-5">
        <div class="mx-5 mt-5">
            <h1 >Création d'un quiz</h1>

            <form  name="form" action=""  method="POST" enctype="multipart/form-data">

                <div class="container text-center">
                    <div class="row">

                        <div class="col-md-4 d-flex flex-column mb-3">
                            <div class="p-2 text-start">
                                <label  for="title">Titre</label>
                            </div>

                            <div class="p-2">
                                <input id="title" name="title" placeholder="Saisissez le title de votre quiz"  type="text" class="form-control" required autocomplete="off" maxlength="128">
                            </div>

                            <div class="p-2 text-start">
                                <label for="universe">Univers</label>
                            </div>

                            <div class="p-2">
                                <input id="universe" name="universe" placeholder="Saisissez l'universe où se déroule votre quiz"  type="text" class="form-control" required autocomplete="off" maxlength="64">
                            </div>

                        </div>

                        <div class="col-md-4 d-flex flex-column mb-3">

                            <div class="p-2 text-start">
                                <label  for="difficulty_level">Difficulté</label>
                            </div>

                            <div class="p-2 text-start">
                                <input id="difficulty_level1" type="radio" name="difficulty_level" value=1 checked>
                                <label for="difficulty_level1">★</label>
                            </div>

                            <div class="p-2 text-start">
                                <input id="difficulty_level2" type="radio" name="difficulty_level" value=2>
                                <label for="difficulty_level2">★ ★</label>
                            </div>

                            <div class="p-2 text-start">
                                <input id="difficulty_level3" type="radio" name="difficulty_level" value=3>
                                <label for="difficulty_level3">★ ★ ★</label>
                            </div>

                            <div class="p-2 text-start">
                                <input id="difficulty_level4" type="radio" name="difficulty_level" value=4>
                                <label for="difficulty_level4">★ ★ ★ ★</label>
                            </div>

                            <div class="p-2 text-start">
                                <input id="difficulty_level5" type="radio" name="difficulty_level" value=5>
                                <label for="difficulty_level5">★ ★ ★ ★ ★</label>
                            </div>

                        </div>

                        <div class="col-md-4 d-flex flex-column mb-3">

                            <?php if($rank == 3){?>
                                <div class="p-2 text-start">
                                    <label  for="potential_gain">Nombre de pièces maximales obtenables</label>
                                </div>

                                <div class="p-2">
                                    <input id="potential_gain" name="potential_gain" placeholder="Saisissez une valeur numérique"  type="text" class="form-control" required>
                                </div>

                            <?php }?>



                            <div class="p-2 text-start">
                                <label for="quiz_image">Saisissez la miniature</label>
                            </div>
                            <div class="p-2">
                                <input type="file" id="quiz_image" name="quiz_image" class="form-control" required>
                            </div>

                        </div>



                    </div>

                    <div class="row">

                        <div class="col-12">
                            <textarea id="summary" name="summary" placeholder="Saisissez la Description du quiz (obligatoire)" class="form-control" rows="15" cols="33" required></textarea>
                        </div>

                    </div>

                    <div id="question1" class="questionContainer col-12 d-flex flex-column mb-3">
                        <div class="p-2 text-start">
                            <label for="question1">Question 1</label>
                            <input id="question1" name="question1" placeholder="Saisissez la question (obligatoire)" type="text" class="form-control" required autocomplete="off">
                        </div>
                        <div class="p-2 text-start">
                            <label for="image_question1">Saisissez l'image pour la question</label>
                            <input type="file" id="image_question1" name="image_question1" class="form-control" required autocomplete="off">
                        </div>
                        <div class="p-2 text-start">
                            <label for="question1">Saisissez les différentes propositions et cochez la ou les bonnes réponses à votre question</label>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12 d-flex flex-row mb-3">
                                <input type="checkbox" id="correct1_1" name="correct1_1" value="1">
                                <input type="text" id="answer1_1" name="answer1_1" placeholder="Saisissez la première réponse (obligatoire)" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-6 col-12 d-flex flex-row mb-3">
                                <input type="checkbox" id="correct1_2" name="correct1_2" value="1">
                                <input type="text" id="answer1_2" name="answer1_2" placeholder="Saisissez la deuxième réponse (obligatoire)" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-6 col-12 d-flex flex-row mb-3">
                                <input type="checkbox" id="correct1_3" name="correct1_3" value="1">
                                <input type="text" id="answer1_3" name="answer1_3" placeholder="Saisissez la troisième réponse (facultative)" class="form-control" autocomplete="off">
                            </div>
                            <div class="col-md-6 col-12 d-flex flex-row mb-3">
                                <input type="checkbox" id="correct1_4" name="correct1_4" value="1">
                                <input type="text" id="answer1_4" name="answer1_4" placeholder="Saisissez la quatrième réponse (facultative)" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger remove-question" style="display: none;">Supprimer</button>
                    </div>

                    <div id="newQuestionsContainer"></div>
                    <button id="addQuestionBtn" type="button" class="btn btn-primary" onclick="addQuestion()">Ajouter une question</button>



                    <div class="row">

                        <div class="text-center mt-3 btnediv">
                            <button id="quizbtn" type="submit" class="btn btne" style="width: auto;">Créer le quiz</button>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <?php require "Structure/Footer/footer.php";?>
    </div>
</div>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
<script src="Structure/Functions/quiz.js"></script>

</body>
</html>
</body>
</html>
