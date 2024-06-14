<?php

$pageTitle = "Voyage sauvegardé";

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
                    <h1>Votre voyage a bien été sauvegardé.</h1>
                    <h5>Vous pouvez reprendre à tout moment la création de votre voyage depuis votre profil.</h5>
                </div>
                <div class="text-center mt-3 btnediv">
                    <button type="button" class="btn btn-light btne" onclick="window.location.href='profileTravel.php';" role="button">Voir le voyage</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>