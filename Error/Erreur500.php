<?php

$pageTitle = "Erreur 500";

require "Structure/Head/head.php";
?>

<link rel="stylesheet" href="../Design/Css/bootstrap.css">
<link rel="stylesheet" href="../Design/Css/erreur404.css">
</head>
<body>
<div class="div-pleine-page image-overlay">
    <div class="logoe">
        <img src="../Design/Pictures/Logo-Blanc.png" alt="Logo de Landtales" class="logoei">
    </div>
    <div class="container text-center">
        <div class="row">
            <div class="col-md-7 col-sm-12 d-flex flex-column align-items-right justify-content-center erdiv">
                <div class="text-end mr-0 textee">
                    <h1>Erreur 500</h1>
                    <h3>Le serveur n'a pas pu traité la situation.</h3>
                    <h3>Nous sommes sur le coup.</h3>
                    <h5>Tout sera revenu bientôt dans l'ordre.</h5>
                </div>
                <div class="text-end mt-3 btnediv">
                    <button type="button" class="btn btn-light btne" onclick="goBack();" role="button">Revenir sur ses pas</button>


                </div>
            </div>
            <div class="col-md-5 col-sm-12 d-flex justify-content-center align-items-center">
                <img src="../Design/Pictures/Astronaute.png" alt="Image d'un Astronaute" class ="astro">
            </div>
        </div>
    </div>
</div>
<script>
    function goBack() {
        window.history.back();
    }
</script>
</body>
</html>

