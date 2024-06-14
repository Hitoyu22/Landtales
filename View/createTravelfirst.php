<?php
session_start();
require "Structure/Functions/function.php";

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];
    checkUserRole();

    $pageTitle = "Création d'un voyage | Partie 1";

    require "Structure/Bdd/config.php";

    $errors = [];

    if (isset($_POST['create'])) {
        $title = htmlspecialchars($_POST['title']);
        $summary = htmlspecialchars($_POST['summary']);

        $newTravel = $bdd->prepare("INSERT INTO travel (title, travel_date, idclient, travel_status, summary, idtheme) VALUES (?, NOW(), ?, ?, ?, ?)");
        $newTravel->execute([$title, $userId, 0, $summary, 1]);

        $lastId = $bdd->lastInsertId();

        $bannerMaxSize = 2 * 1024 * 1024;
        $bannerMaxWidth = 1920;
        $bannerMaxHeight = 1080;

        $baseDir = $_SERVER['DOCUMENT_ROOT'] . '/Ressources/Travel/' . $lastId;

        if (isset($_FILES['banner']) && !empty($_FILES['banner']['tmp_name'])) {
            $bannerInfo = getimagesize($_FILES['banner']['tmp_name']);
            $bannerFileSize = $_FILES['banner']['size'];

            if ($bannerFileSize > $bannerMaxSize) {
                $errors[] = "La taille de l'image de bannière est trop grande.";
            } elseif ($bannerInfo[0] > $bannerMaxWidth || $bannerInfo[1] > $bannerMaxHeight) {
                $errors[] = "Les dimensions de l'image de bannière sont trop grandes.";
            } else {
                $bannerPath = $baseDir . '/banner/';
                if (!file_exists($bannerPath)) {
                    if (!mkdir($bannerPath, 0777, true)) {
                        $errors[] = "Erreur lors de la création du dossier de bannière.";
                    }
                }

                $bannerFilename = $bannerPath . basename($_FILES['banner']['name']);
                if (!move_uploaded_file($_FILES['banner']['tmp_name'], $bannerFilename)) {
                    $errors[] = "Erreur lors du téléchargement de l'image de bannière.";
                } else {
                    $bannerUrl = "https://landtales.freeddns.org/Ressources/Travel/$lastId/banner/" . basename($_FILES['banner']['name']);
                    $updateBannerQuery = $bdd->prepare("UPDATE travel SET banner = ? WHERE id = ?");
                    $updateBannerQuery->execute([$bannerUrl, $lastId]);
                }
            }
        }

        $miniatureMaxSize = 1 * 1024 * 1024;

        if (isset($_FILES['miniature']) && !empty($_FILES['miniature']['tmp_name'])) {
            $miniatureFileSize = $_FILES['miniature']['size'];
            if ($miniatureFileSize > $miniatureMaxSize) {
                $errors[] = "La taille de l'image miniature est trop grande.";
            } else {
                $miniaturePath = $baseDir . '/miniature/';
                if (!file_exists($miniaturePath)) {
                    if (!mkdir($miniaturePath, 0777, true)) {
                        $errors[] = "Erreur lors de la création du dossier de miniature.";
                    }
                }

                $miniatureFilename = $miniaturePath . basename($_FILES['miniature']['name']);
                if (!move_uploaded_file($_FILES['miniature']['tmp_name'], $miniatureFilename)) {
                    $errors[] = "Erreur lors du téléchargement de l'image miniature.";
                } else {
                    $miniatureUrl = "https://landtales.freeddns.org/Ressources/Travel/$lastId/miniature/" . basename($_FILES['miniature']['name']);
                    $updateMiniatureQuery = $bdd->prepare("UPDATE travel SET miniature = ? WHERE id = ?");
                    $updateMiniatureQuery->execute([$miniatureUrl, $lastId]);
                }
            }
        }



        if (empty($errors)) {
            header("Location: createTravelsecond.php?id=$lastId");
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
<link rel="stylesheet" href="Design/Css/editorjs.css">
<link rel="stylesheet" href="Design/Css/style.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">

<?php require "Structure/Navbar/navbar.php"; ?>
<div class="wrapper">
    <?php require "Structure/Sidebar/sidebar.php"; ?>
    <div class="main mt-5">
        <div class="mx-5 mt-5">
            <h1 class="mx-0">Créer votre voyage</h1>
            <p class="mb-5">Chaque voyage commence toujours par un bon conteur. Votre aventure n'attend plus qu'à être écrite.</p>
            <form method="post" action="" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group mb-4">
                    <label for="title">Titre de votre voyage</label>
                    <input type="text" class="form-control" placeholder="Saisissez votre titre" name="title" required maxlength="128">
                </div>
                <div class="form-group mb-4">
                    <label for="summary">Résumé de votre voyage (Ne sera affiché que si mis en avant sur votre profil)</label>
                    <textarea class="form-control" rows="3" id="summary" placeholder="Saisissez votre résumé" name="summary" required maxlength="256"></textarea>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label" for="banner">Image de bannière (Taille maximale : 2Mo)</label><br>
                    <input type="file" name="banner" id="banner" class="mb-2 col-12 pl-0" accept="image/*" onchange="checkImageSize(this, 2)" required>
                    <img id="banner-preview" class="banner-preview col-12 pl-0 pr-0" style="display:none;">
                </div>
                <div class="form-group mb-4">
                    <label for="miniature">Image de miniature (Taille maximale : 1Mo)</label><br>
                    <input type="file" name="miniature" class="col-12 mb-2 pl-0" id="miniature" accept="image/*" onchange="checkImageSize(this, 1)" required>
                    <img id="miniature-preview" class="miniature-preview col-12 col-md-4 pl-0 pr-0" style="display:none;">
                </div>
                <button type="submit" name="create" class="btn-landtales mb-3">Continuer la création du voyage</button>
            </form>
        </div>
        <?php require "Structure/Footer/footer.php"; ?>
    </div>
</div>
<script>
    function checkImageSize(input, maxSizeInMB) {
        if (input.files && input.files[0]) {
            const fileSize = input.files[0].size;
            const maxSize = maxSizeInMB * 1024 * 1024;

            if (fileSize > maxSize) {
                alert("La taille de l'image est trop grande. Veuillez choisir un fichier de taille inférieure à " + maxSizeInMB + " Mo.");
                input.value = '';
                return false;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = input.id === 'banner' ? document.getElementById('banner-preview') : document.getElementById('miniature-preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function validateForm() {
        const bannerInput = document.getElementById("banner");
        const miniatureInput = document.getElementById("miniature");

        if (bannerInput.files.length > 0 && bannerInput.files[0].size > 2 * 1024 * 1024) {
            alert("La taille de la bannière est trop grande, merci de prendre une image de 2Mo maximum.");
            return false;
        }

        if (miniatureInput.files.length > 0 && miniatureInput.files[0].size > 1 * 1024 * 1024) {
            alert("La taille de la miniature est trop grande, merci de prendre une image de 1 Mo maximum.");
            return false;
        }

        return true;
    }
</script>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>
