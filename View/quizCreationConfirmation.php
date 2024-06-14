<?php

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['id']) && !empty($_GET['id'])){

        $idQuiz = $_GET['id'];


    }
} else {
    header("Location: homeFront.php");
}

$pageTitle = "Déconnexion";

require "Structure/Head/head.php";
?>
    <link rel="stylesheet" href="Design/Css/confirmation.css">
</head>
<body>
    <div class="div-pleine-page image-overlay">
        <div class="logoe">
            <img src="Design/Pictures/Logo-Blanc.png" alt="Logo de Landtales" class="logoei">
        </div>
        <div class="container text-center">
            <div class="row">
                <div class="d-flex flex-column align-items-right justify-content-center erdiv">
                    <div class="text-center mr-0 textee">
                        <h1>Votre quiz à bien été crée</h1>
                        <h5>Puisse l'univers guider chaque individu traversant les épreuves de votre quiz avec clarté et facilité</h5>
                    </div>
                    <div class="text-center mt-3 btnediv">
                        <button type="button" class="btn btn-light btne" onclick="window.location.href='quizGame.php?id=<?php echo $idQuiz; ?>';" role="button">Faites votre quiz</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
