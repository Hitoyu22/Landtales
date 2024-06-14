<?php

$pageTitle = "La newsletter est envoyée";

require "Admin/Structures/Head/headAdmin.php";
?>



<link rel="stylesheet" href="../../Design/Css/confirmation.css">

</head>
<body>
<div class="div-pleine-page image-overlay">
    <div class="logoe">
        <img src="../../Design/Pictures/Logo-Blanc.png" alt="Logo de Landtales" class="logoei">
    </div>
    <div class="container text-center">
        <div class="row">
            <div class="d-flex flex-column align-items-right justify-content-center erdiv">
                <div class="text-center mr-0 textee">
                    <h1>La newsletter a bien été envoyée</h1>
                    <h5>Appuyez sur le bouton ci-dessous pour retourner à la page de la Newsletter.</h5>
                </div>
                <div class="text-center mt-3 btnediv">
                    <button type="button" class="btn btn-light btne" onclick="window.location.href='newsletter.php';" role="button">Retourner à la page des newsletters</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>