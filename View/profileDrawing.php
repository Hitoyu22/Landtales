<?php
session_start();

$pageTitle = "Paramètres - Votre dessin";

require "Structure/Functions/function.php";

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkUserRole();
    require "Structure/Bdd/config.php";

    // Récupérer l'URL de l'image depuis la base de données
    $getImageUrlQuery = $bdd->prepare("SELECT drawing FROM client WHERE id = ?");
    $getImageUrlQuery->execute([$userId]);
    $imageUrlData = $getImageUrlQuery->fetch(PDO::FETCH_ASSOC);
    $imageUrl = isset($imageUrlData['drawing']) ? $imageUrlData['drawing'] : '';

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['image'])) {
        $destinationFolder = "Ressources/User/{$userId}/Drawing/";
        $fileName = "dessin_utilisateur_{$userId}.png";
        $filePath = $destinationFolder . $fileName;

        if (!file_exists($destinationFolder)) {
            mkdir($destinationFolder, 0777, true);
        }

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $imageData = $_POST['image'];
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = base64_decode($imageData);

        if (file_put_contents($filePath, $imageData)) {
            $imageUrl = "https://landtales.freeddns.org/{$filePath}";

            $updateImageUrl = $bdd->prepare("UPDATE client SET drawing = ? WHERE id = ?");
            $updateImageUrl->execute([$imageUrl, $userId]);

            header("Location: profileDrawing.php?change=success");
            exit();
        }
    }


    require "Structure/Head/head.php";
} else {
    header("Location: login.php");
    exit();
}

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-front.css">
</head>

<body class="hidden" data-bs-theme="<?php echo $theme; ?>">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">
    <?php require "Structure/Sidebar/sidebar.php";?>
    <div class="main  mt-5">
        <div class="container mt-5">
            <h1 class="mx-0">Votre dessin</h1>
            <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center bg-transparent" style="z-index: 100;"> <!-- Définissez un z-index inférieur -->
                <div class="container-fluid">
                    <ul class="navbar-nav" style="margin: 0 auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="profileSettings.php">Général</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileCustom.php">Modifier le profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileDrawing.php"><u>Votre dessin</u></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileTravel.php">Vos voyages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileConfidentiality.php">Confidentialité</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileReporting.php">Signalement</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div>
                <p>Afin que votre profil se démarque sur Landtales, nous vous mettons au défi de faire le plus beau dessin. Ce dernier sera affiché sur votre profil.</p>
            </div>

            <div class="drawing-container">
                <div class="current-image-container">
                    <?php if (isset($imageUrl) && $imageUrl != NULL): ?>
                        <img src="<?= $imageUrl ?>" class="current-image" alt="Votre dessin">
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center mb-5">
                <button id="startDrawingBtn" class="start-drawing-btn btn-landtales mx-auto">Commencer à dessiner</button>
            </div>

            <div class="canvas-container text-center" style="display: none;">
                <canvas id="canvas" class="canvas-container" width="400" height="400" style="border: 1px solid black;"></canvas>
            </div>

            <div class="controls-container text-center mb-5" style="display: none;">
                <label for="colorPicker">Couleur :</label>
                <input type="color" id="colorPicker" value="#000000">

                <label for="brushSize">Taille du crayon :</label>
                <input type="range" id="brushSize" min="1" max="50" value="5">

                <button id="clearBtn">Effacer</button>
                <button id="downloadBtn" >Télécharger l'image</button>
                <button id="saveBtn">Sauvegarder</button>
            </div>
        </div>

        <?php require "Structure/Footer/footer.php";?>
    </div>
</div>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
<script src="Structure/Functions/drawing.js"></script>


