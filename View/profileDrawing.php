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
<script>


    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');

    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    let color = '#000000';
    let brushSize = 5;

    function draw(e) {
        if (!isDrawing) return;

        ctx.strokeStyle = color;
        ctx.lineWidth = brushSize;
        ctx.lineJoin = 'round';
        ctx.lineCap = 'round';

        let currentX, currentY;
        if (e.type === 'mousemove' || e.type === 'mousedown' || e.type === 'mouseup') {
            currentX = e.offsetX;
            currentY = e.offsetY;
        } else if (e.type === 'touchmove' || e.type === 'touchstart' || e.type === 'touchend') {
            currentX = e.touches[0].clientX - canvas.getBoundingClientRect().left;
            currentY = e.touches[0].clientY - canvas.getBoundingClientRect().top;
        }

        ctx.beginPath();
        ctx.moveTo(lastX, lastY);
        ctx.lineTo(currentX, currentY);
        ctx.stroke();

        lastX = currentX;
        lastY = currentY;
    }

    canvas.addEventListener('mousedown', (e) => {
        isDrawing = true;
        lastX = e.offsetX;
        lastY = e.offsetY;
    });
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', () => isDrawing = false);
    canvas.addEventListener('mouseout', () => isDrawing = false);

    canvas.addEventListener('touchstart', (e) => {
        isDrawing = true;
        lastX = e.touches[0].clientX - canvas.getBoundingClientRect().left;
        lastY = e.touches[0].clientY - canvas.getBoundingClientRect().top;
    });
    canvas.addEventListener('touchmove', draw);
    canvas.addEventListener('touchend', () => isDrawing = false);

    const colorPicker = document.getElementById('colorPicker');
    colorPicker.addEventListener('change', () => color = colorPicker.value);

    const brushSizeInput = document.getElementById('brushSize');
    brushSizeInput.addEventListener('input', () => brushSize = parseInt(brushSizeInput.value));

    const clearBtn = document.getElementById('clearBtn');
    clearBtn.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    const downloadBtn = document.getElementById('downloadBtn');
    downloadBtn.addEventListener('click', () => {
        const tempCanvas = document.createElement('canvas');
        const tempCtx = tempCanvas.getContext('2d');
        tempCanvas.width = canvas.width;
        tempCanvas.height = canvas.height;
        tempCtx.fillStyle = '#ffffff';
        tempCtx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
        tempCtx.drawImage(canvas, 0, 0);

        const dataURL = tempCanvas.toDataURL('image/jpeg', 1.0);

        const a = document.createElement('a');
        a.href = dataURL;
        a.download = 'mon_dessin.jpg';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.addEventListener('click', () => {
        const imageData = canvas.toDataURL('image/png');
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'image=' + encodeURIComponent(imageData),
        })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                window.location.reload();
            })
            .catch(error => console.error('Error:', error));
    });

    const startDrawingBtn = document.querySelector('.start-drawing-btn');
    const canvasContainer = document.querySelector('.canvas-container');
    const controlsContainer = document.querySelector('.controls-container');

    startDrawingBtn.addEventListener('click', () => {
        startDrawingBtn.style.display = 'none';
        canvasContainer.style.display = 'block';
        controlsContainer.style.display = 'block';
    });

    canvas.addEventListener('touchstart', (e) => {
        isDrawing = true;
        lastX = e.touches[0].clientX - canvas.getBoundingClientRect().left;
        lastY = e.touches[0].clientY - canvas.getBoundingClientRect().top;
        e.preventDefault();
    });
    canvas.addEventListener('touchmove', (e) => {
        draw(e);
        e.preventDefault();
    });
    canvas.addEventListener('touchend', () => {
        isDrawing = false;
        e.preventDefault();
    });
</script>

