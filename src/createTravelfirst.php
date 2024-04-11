<?php
session_start();

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];

    $pageTitle = "Création d'un voyage | Partie 1";

    require "Structure/Bdd/config.php";

    $errors = [];

    if(isset($_POST['create'])){
        $title = htmlspecialchars($_POST['title']);
        $summary = htmlspecialchars($_POST['summary']);

        //Création d'un nouveau voyage
        $newTravel = $bdd->prepare("INSERT INTO travel (title, travel_date, idclient, travel_status, summary) VALUES (?, NOW(), ?, ?,?)");
        $newTravel->execute([$title, $userId, 0, $summary]);

        //Récupération de l'id du voyage pour la création d'un dossier unique
        $lastId = $bdd->lastInsertId();

        //Restriction des images
        $bannerMaxSize = 2 * 1024 * 1024;
        $bannerMaxWidth = 1920;
        $bannerMaxHeight = 1080;

        //Création d'un dossier pour la bannière (création du dossier du voyage également)
        if(isset($_FILES['banner']) && !empty($_FILES['banner']['tmp_name'])) {
            $bannerInfo = getimagesize($_FILES['banner']['tmp_name']);
            $bannerFileSize = $_FILES['banner']['size'];

            if($bannerFileSize > $bannerMaxSize) {
                $errors[] = "La taille de l'image de bannière est trop grande.";
            } elseif ($bannerInfo[0] > $bannerMaxWidth || $bannerInfo[1] > $bannerMaxHeight) {
                $errors[] = "Les dimensions de l'image de bannière sont trop grandes.";
            } else {
                $bannerPath = "Ressources/Travel/$lastId/banner/";
                if (!file_exists($bannerPath)) {
                    mkdir($bannerPath, 0777, true);
                }

                //Déplacement de l'image vers le dossier
                $bannerFilename = $bannerPath . $_FILES['banner']['name'];
                move_uploaded_file($_FILES['banner']['tmp_name'], $bannerFilename);
                $bannerUrl = $bannerFilename;

                //Ajout de l'url de l'image dans la bdd
                $updateBannerQuery = $bdd->prepare("UPDATE travel SET banner = ? WHERE id = ?");
                $updateBannerQuery->execute([$bannerUrl, $lastId]);
            }
        }

        //Restriction miniature
        $miniatureMaxSize = 1 * 1024 * 1024;

        //Création d'un dossier pour la miniature
        if(isset($_FILES['miniature']) && !empty($_FILES['miniature']['tmp_name'])) {
            $miniatureFileSize = $_FILES['miniature']['size'];
            if($miniatureFileSize > $miniatureMaxSize) {
                $errors[] = "La taille de l'image miniature est trop grande.";
            } else {

                $miniaturePath = "Ressources/Travel/$lastId/miniature/";
                if (!file_exists($miniaturePath)) {
                    mkdir($miniaturePath, 0777, true);
                }

                //Déplacement de la miniature vers le dossier
                $miniatureFilename = $miniaturePath . $_FILES['miniature']['name'];
                move_uploaded_file($_FILES['miniature']['tmp_name'], $miniatureFilename);
                $miniatureUrl = $miniatureFilename;

                //Ajout de l'url de l'image dans la bdd
                $updateMiniatureQuery = $bdd->prepare("UPDATE travel SET miniature = ? WHERE id = ?");
                $updateMiniatureQuery->execute([$miniatureUrl, $lastId]);
            }
        }


        if(empty($errors)) {
            header("Location: createTravelsecond.php?id=$lastId");
            exit();
        }
    }

    require "Structure/Head/head.php";

} else {

    header("Location: login.php");
    exit();
}



?>
<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/editorjs.css">
<link rel="stylesheet" href="Design/Css/style.css">
</head>
<body data-bs-theme="light">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">


    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main mt-5">
        <div class="mx-5 mt-5">
            <h1 class="mx-0">Créer votre voyage</h1>
            <p class="mb-5">Chaque voyage commence toujours par un bon conteur. Votre aventure n'attend plus qu'à être écrite.</p>
            <form method="post" action="" enctype="multipart/form-data" onsubmit="return validateForm()">
                <div class="form-group mb-4">
                    <label for="title">Titre de votre voyage</label>
                    <input type="text" class="form-control" placeholder="Saisissez votre titre" name="title" required>
                </div>
                <div class="form-group mb-4">
                    <label for="summary">Résumé de votre voyage (Ne sera affiché que si mis en avant sur votre profil)</label>
                    <textarea class="form-control" rows="3" id="summary" placeholder="Saisissez votre résumé" name="summary" required></textarea>
                </div>
                <div class="form-group mb-4">
                    <label class="form-label" for="banner">Image de bannière (Taille maximale : 1Mo)</label><br>
                    <input type="file" name="banner" id="banner" class="mb-2 col-12 pl-0" accept="image/*" onchange="checkImageSize(this, 1)">
                    <img id="banner-preview" class="banner-preview col-12 pl-0 pr-0">
                </div>
                <div class="form-group mb-4">
                    <label for="miniature">Image de miniature (Taille maximale : 500Ko)</label><br>
                    <input type="file" name="miniature" class="col-12 mb-2 pl-0" id="miniature" accept="image/*" onchange="checkImageSize(this, 0.5)">
                    <img id="miniature-preview" class="miniature-preview col-12 col-md-4 pl-0 pr-0"> <!-- Ajoutez les classes col-12 col-md-6 ici -->
                </div>
                <button type="submit" name="create" class="btn-landtales mb-3">Continuer la création du voyage</button>
            </form>
        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>
</div>
<script>
    function showBannerPreview() {
        const bannerInput = document.getElementById("banner");
        const bannerPreview = document.getElementById("banner-preview");

        if (bannerInput.files && bannerInput.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                bannerPreview.src = e.target.result;
                bannerPreview.style.display = "block";
            };

            reader.readAsDataURL(bannerInput.files[0]);
        }
    }

    function showMiniaturePreview() {
        const miniatureInput = document.getElementById("miniature");
        const miniaturePreview = document.getElementById("miniature-preview");

        if (miniatureInput.files && miniatureInput.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                miniaturePreview.src = e.target.result;
                miniaturePreview.style.display = "block";
            };

            reader.readAsDataURL(miniatureInput.files[0]);
        }
    }
    function checkImageSize(input, maxSizeInMB) {
        if (input.files && input.files[0]) {
            const fileSize = input.files[0].size; // taille du fichier en octets
            const maxSize = maxSizeInMB * 1024 * 1024; // conversion en mégaoctets

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
