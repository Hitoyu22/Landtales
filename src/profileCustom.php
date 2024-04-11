<?php
session_start();

$pageTitle = "Paramètres - Vos voyages";


require "Structure/Functions/function.php";


if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    require "Structure/Bdd/config.php";


    if ($_SERVER["REQUEST_METHOD"] == "POST") {


        if(isset($_FILES['iconProfil']) || isset($_FILES['bannerProfil'])) {

            $profileUploadDir = 'Ressources/User/'.$userId.'/icon';
            $bannerUploadDir = 'Ressources/User/'.$userId.'/banner';


            if (!file_exists($profileUploadDir)) {
                mkdir($profileUploadDir, 0777, true);
            }
            if (!file_exists($bannerUploadDir)) {
                mkdir($bannerUploadDir, 0777, true);
            }

            if(isset($_FILES['iconProfil'])) {
                $files = glob($profileUploadDir . '/*');
                foreach($files as $file) {
                    if(is_file($file)) {
                        unlink($file);
                    }
                }
            }
            if(isset($_FILES['bannerProfil'])) {
                $files = glob($bannerUploadDir . '/*');
                foreach($files as $file) {
                    if(is_file($file)) {
                        unlink($file);
                    }
                }
            }


            if(isset($_FILES['iconProfil']) && $_FILES['iconProfil']['error'] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['iconProfil']['name'];
                $filePath = $profileUploadDir . '/' . $fileName;
                move_uploaded_file($_FILES['iconProfil']['tmp_name'], $filePath);
                // Stocker le chemin complet dans la base de données
                $updateIcon = $bdd->prepare("UPDATE client SET profil_picture = ? WHERE id = ?");
                $updateIcon->execute(['http://localhost/src/' . $filePath, $userId]);
            }


            if(isset($_FILES['bannerProfil']) && $_FILES['bannerProfil']['error'] === UPLOAD_ERR_OK) {
                $fileName = $_FILES['bannerProfil']['name'];
                $filePath = $bannerUploadDir . '/' . $fileName;
                move_uploaded_file($_FILES['bannerProfil']['tmp_name'], $filePath);

                $updateBanner = $bdd->prepare("UPDATE client SET banner = ? WHERE id = ?");
                $updateBanner->execute(['http://localhost/src/' . $filePath, $userId]);
            }
        }


        if (isset($_POST['description'])){
            $description = htmlspecialchars($_POST['description']);
        } else{
            $description = "";
        }

        $facebook = isset($_POST['facebook']) ? htmlspecialchars($_POST['facebook']) : "";
        $instagram = isset($_POST['instagram']) ? htmlspecialchars($_POST['instagram']) : "";
        $youtube = isset($_POST['youtube']) ? htmlspecialchars($_POST['youtube']) : "";
        $twitter = isset($_POST['twitter']) ? htmlspecialchars($_POST['twitter']) : "";
        $github = isset($_POST['github']) ? htmlspecialchars($_POST['github']) : "";


        $updateUserProfil = $bdd->prepare("UPDATE client SET summary = ?, facebook = ?, insta = ?, youtube = ?, twitter = ?, github = ? WHERE id = ?");
        $updateUserProfil->execute([$description, $facebook, $instagram, $youtube, $twitter, $github, $userId]);


        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }


    $userInfo = $bdd->prepare('SELECT * FROM client WHERE id = ?');
    $userInfo->execute([$userId]);
    $user = $userInfo->fetch();


    $userBannerPath = isset($user['banner']) ? $user['banner'] : "";
    $userIconPath = isset($user['profil_picture']) ? $user['profil_picture'] : "";
    $userDescription = isset($user['summary']) ? $user['summary'] : "";
    $userFacebook = isset($user['facebook']) ? $user['facebook'] : "";
    $userInstagram = isset($user['insta']) ? $user['insta'] : "";
    $userTwitter = isset($user['twitter']) ? $user['twitter'] : "";
    $userYouTube = isset($user['youtube']) ? $user['youtube'] : "";
    $userGitHub = isset($user['github']) ? $user['github'] : "";


    require "Structure/Head/head.php";
} else {

    header("Location: login.php");
    exit();
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-front.css">
</head>

<body data-bs-theme="light">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">


    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main  mt-5">

        <div class="container mt-5">
            <h1 class="mx-0">Paramètres</h1>

            <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center bg-transparent" style="z-index: 100;"> <!-- Définissez un z-index inférieur -->
                <div class="container-fluid">
                    <ul class="navbar-nav" style="margin: 0 auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="profileSettings.php">Général</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileCustom.php"><u>Modifier le profil</u></a>
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

            <div class="container mb-5">
                <form action="" method="post" enctype="multipart/form-data">
                    <h3>Modifier votre photo de profil et bannière</h3>
                    <div class="form-group mb-2">
                        <label for="iconProfil" class="col-12">Photo de profil (Max 1 Mo)</label>
                        <input alt="Changement de votre photo de profil" type="file" id="iconProfil" name="iconProfil" class="form-control-file col-12">
                    </div>
                    <div class="form-group">
                        <div class="form-group mb-2">
                            <label for="bannerProfil" class="col-12">Bannière (Max 2 Mo)</label>
                            <input alt="Changement de votre bannière de profil" type="file" id="bannerProfil" name="bannerProfil" class="form-control-file col-12">
                        </div>
                    </div>


                    <h3>Modifier votre customisation de profil</h3>

                    <div class="overflow-x-auto mb-5">
                        <div class="d-flex flex-nowrap">
                            <div class="d-flex flex-nowrap">
                                <div class="col-md-2 col-6">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <div class="customization-square mb-3">
                                                <img alt="Customisation" class="customization-square-img" src="https://cdn.futura-sciences.com/cdn-cgi/image/width=1024,quality=50,format=auto/sources/images/dossier/773/01-intro-773.jpg">
                                            </div>
                                            <input type="checkbox" class="custom-checkbox" id="custom-choice-1" name="custom_choice[]" value="1" style="display: none;"> <!-- Ajout de style pour masquer la case à cocher -->
                                            <label for="custom-choice-1" class="btn btn-primary custom-btn">Choisir</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 col-6">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <div class="customization-square mb-3">
                                                <img alt="Customisation" class="customization-square-img" src="https://cdn.futura-sciences.com/cdn-cgi/image/width=1024,quality=50,format=auto/sources/images/dossier/773/01-intro-773.jpg">
                                            </div>
                                            <input type="checkbox" class="custom-checkbox" id="custom-choice-2" name="custom_choice[]" value="2" style="display: none;"> <!-- Ajout de style pour masquer la case à cocher -->
                                            <label for="custom-choice-2" class="btn btn-primary custom-btn">Choisir</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-12">
                        <h3><label for="descriptionProfil">Modifier la description de votre profil</label></h3>
                        <textarea class="form-control" id="descriptionProfil" rows="3" name="description" placeholder="Saisissez la description de votre profil"><?php echo $userDescription; ?></textarea>
                    </div>

                    <h3>Modifier les liens vers vos réseaux sociaux</h3>

                    <div class="mb-5">
                        <div class="form-group col-12 mb-2">
                            <label for="facebook" class="col-12 d-flex align-items-center">
                                <i class="lni lni-facebook-original mx-2"></i>
                                <input class="form-control" id="facebook" name="facebook" value="<?php echo $userFacebook; ?>" placeholder="Saisissez votre URL vers Facebook">
                            </label>
                        </div>

                        <div class="form-group col-12 mb-2">
                            <label for="instagram" class="col-12 d-flex align-items-center">
                                <i class="lni lni-instagram mx-2"></i>
                                <input class="form-control" id="instagram" value="<?php echo $userInstagram; ?>" name="instagram" placeholder="Saisissez votre URL vers Instagram">
                            </label>
                        </div>

                        <div class="form-group col-12 mb-2">
                            <label for="twitter" class="col-12 d-flex align-items-center">
                                <i class="lni lni-twitter-original mx-2"></i>
                                <input class="form-control" id="twitter" value="<?php echo $userTwitter; ?>" name="twitter" placeholder="Saisissez votre URL vers Twitter">
                            </label>
                        </div>

                        <div class="form-group col-12 mb-2">
                            <label for="youtube" class="col-12 d-flex align-items-center ">
                                <i class="lni lni-youtube mx-2"></i>
                                <input class="form-control" id="youtube" value="<?php echo $userYouTube; ?>" name="youtube" placeholder="Saisissez votre URL vers Youtube">
                            </label>
                        </div>

                        <div class="form-group col-12 mb-2">
                            <label for="github" class="col-12 d-flex align-items-center">
                                <i class="lni lni-github-original mx-2"></i>
                                <input class="form-control" id="github" name="github" value="<?php echo $userGitHub; ?>" placeholder="Saisissez votre URL vers Github">
                            </label>
                        </div>
                    </div>

                    <button type="submit" id="publish" class="btn-landtales mb-5" name="publish">Mettre à jour mes informations</button>

                </form>

            </div>

        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const customCheckboxes = document.querySelectorAll('.custom-checkbox');

        customCheckboxes.forEach(checkbox => {
            // Sélectionner le bouton associé à la case à cocher
            const button = checkbox.nextElementSibling;

            // Gérer l'événement de clic sur le bouton
            button.addEventListener('click', function() {
                // Vérifier si la case à cocher est déjà cochée
                const isChecked = checkbox.checked;

                // Mettre à jour l'état de la case à cocher lorsque le bouton est cliqué
                checkbox.checked = !isChecked;

                // Mettre à jour le texte du bouton en fonction de l'état de la case à cocher
                if (checkbox.checked) {
                    button.textContent = 'Choisi ✅';
                } else {
                    button.textContent = 'Choisir';
                }
            });

            // Vérifier si la case à cocher est déjà cochée au chargement de la page
            if (checkbox.checked) {
                button.textContent = 'Choisi ✅';
            }
        });
    });
</script>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>

</html>
