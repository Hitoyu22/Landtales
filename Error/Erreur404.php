<?php

$pageTitle = "Erreur 404";

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
                    <h1>Erreur 404</h1>
                    <h3>Oups... Vous semblez vous être perdu.</h3>
                    <h3>Vous devriez retourner sur vos pas.</h3>
                    <h5>Après tout, chaque voyage mène à Landtales.</h5>
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


