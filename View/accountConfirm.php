<?php
$pageTitle = "Compte vérifié";

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
                    <h1>Votre compte a bien été vérifié</h1>
                    <h5>Nous sommes très heureux de vous accueillir parmi nous.</h5>
                </div>
                <div class="text-center mt-3 btnediv">
                    <button type="button" class="btn btn-light btne" onclick="window.location.href='login.php';" role="button">Se connecter</button>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

