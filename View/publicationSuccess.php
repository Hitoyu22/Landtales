<?php

    if ($_SERVER["REQUEST_METHOD"] == "GET") {

        if (isset($_GET['id']) && !empty($_GET['id'])){

            $idVoyage = $_GET['id'];


        }
    } else {
        header("Location: homeFront.php");
    }

$pageTitle = "Voyage publié";

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
                    <h1>Votre experience a été partagé a travers l'univers avec succès</h1>
                    <h5>Partez vous aussi découvrir les voyages d'autres explorateurs !</h5>
                    <h5>Sinon vous pouvez toujours regarder ce que l'univers retiens de vous</h5>
                </div>
                <div class="text-center mt-3 btnediv">
                    <button type="button" class="btn btn-light btne" onclick="window.location.href='travel.php?id=<?php echo $idVoyage; ?>';" role="button">Voir le voyage</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>