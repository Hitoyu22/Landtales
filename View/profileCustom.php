<?php
session_start();

$pageTitle = "Paramètres - Customisation";

require "Structure/Functions/function.php";
require "Structure/Functions/alerts.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkUserRole();
    require "Structure/Bdd/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_FILES['iconProfil']) || isset($_FILES['bannerProfil'])) {

            $profileUploadDir = 'Ressources/User/'.$userId.'/icon';
            $bannerUploadDir = 'Ressources/User/'.$userId.'/banner';

            if (!file_exists($profileUploadDir)) {
                mkdir($profileUploadDir, 0777, true);
            }
            if (!file_exists($bannerUploadDir)) {
                mkdir($bannerUploadDir, 0777, true);
            }

            // Vérifier si une nouvelle image de profil a été téléchargée
            if (isset($_FILES['iconProfil']) && is_uploaded_file($_FILES['iconProfil']['tmp_name'])) {
                // Supprimer l'ancienne image
                $files = glob($profileUploadDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                // Télécharger et mettre à jour la nouvelle image
                $fileName = $_FILES['iconProfil']['name'];
                $filePath = $profileUploadDir . '/' . $fileName;
                move_uploaded_file($_FILES['iconProfil']['tmp_name'], $filePath);
                $updateIcon = $bdd->prepare("UPDATE client SET profil_picture = ? WHERE id = ?");
                $updateIcon->execute(['https://landtales.freeddns.org/' . $filePath, $userId]);
            }

            // Vérifier si une nouvelle bannière a été téléchargée
            if (isset($_FILES['bannerProfil']) && is_uploaded_file($_FILES['bannerProfil']['tmp_name'])) {
                // Supprimer l'ancienne bannière
                $files = glob($bannerUploadDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }

                // Télécharger et mettre à jour la nouvelle bannière
                $fileName = $_FILES['bannerProfil']['name'];
                $filePath = $bannerUploadDir . '/' . $fileName;
                move_uploaded_file($_FILES['bannerProfil']['tmp_name'], $filePath);
                $updateBanner = $bdd->prepare("UPDATE client SET banner = ? WHERE id = ?");
                $updateBanner->execute(['https://landtales.freeddns.org/' . $filePath, $userId]);
            }
        }

        if (isset($_POST['description'])){
            $description = htmlspecialchars($_POST['description']);
        } else{
            $description = "";
        }

        $customisationId = isset($_POST['customisation']) && !empty($_POST['customisation']) ? $_POST['customisation'] : NULL;

        $facebook = isset($_POST['facebook']) ? htmlspecialchars($_POST['facebook']) : "";
        $instagram = isset($_POST['instagram']) ? htmlspecialchars($_POST['instagram']) : "";
        $youtube = isset($_POST['youtube']) ? htmlspecialchars($_POST['youtube']) : "";
        $twitter = isset($_POST['twitter']) ? htmlspecialchars($_POST['twitter']) : "";
        $github = isset($_POST['github']) ? htmlspecialchars($_POST['github']) : "";

        $updateUserProfil = $bdd->prepare("UPDATE client SET summary = ?, facebook = ?, insta = ?, youtube = ?, twitter = ?, github = ?, idcustomisation = ? WHERE id = ?");
        $updateUserProfil->execute([$description, $facebook, $instagram, $youtube, $twitter, $github, $customisationId, $userId]);

        header("Location: profileCustom.php?change=success");
        exit();
    }

    $userInfo = $bdd->prepare('SELECT idcustomisation FROM client WHERE id = ?');
    $userInfo->execute([$userId]);
    $user = $userInfo->fetch();
    $currentCustomisationId = $user['idcustomisation'];

    $custom = $bdd->prepare("SELECT c.id, c.picture_name FROM customisation c JOIN client_customisation cc ON c.id = cc.idcustomisation WHERE cc.idclient = ?");
    $custom->execute([$userId]);
    $customisations = $custom->fetchAll();

    $userInfo = $bdd->prepare('SELECT banner,profil_picture,summary,facebook,insta,twitter,youtube,github FROM client WHERE id = ?');
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

dataChange();

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

    <div class="main mt-5">
        <div class="container mt-5">
            <h1 class="mx-0">Paramètres</h1>

            <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center bg-transparent" style="z-index: 100;">
                <div class="container-fluid">
                    <ul class="navbar-nav" style="margin: 0 auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="profileSettings.php">Général</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileCustom.php"><u>Modifier le profil</u></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileDrawing.php">Votre dessin</a>
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
                        <input type="file" id="iconProfil" name="iconProfil" class="form-control-file col-12" onchange="validateImage(event, 'iconPreview', 1)" accept="image/jpeg, image/jpg">
                        <img id="iconPreview" src="<?php echo $userIconPath; ?>" data-initial-src="<?php echo $userIconPath; ?>" alt="Aperçu de la photo de profil" class="rounded-circle mt-2" style="width: 100px; height: 100px;">
                    </div>

                    <div class="form-group mb-2">
                        <label for="bannerProfil" class="col-12">Bannière (Max 2 Mo)</label>
                        <input type="file" id="bannerProfil" name="bannerProfil" class="form-control-file col-12" onchange="validateImage(event, 'bannerPreview', 2)" accept="image/jpeg, image/jpg">
                        <img id="bannerPreview" src="<?php echo $userBannerPath; ?>" data-initial-src="<?php echo $userBannerPath; ?>" alt="Aperçu de la bannière de profil" class="mt-2" style="width: 100%; max-height: 200px;">
                    </div>

                    <div class="form-group">
                        <label for="customSelect">Choisir une customisation:</label>
                        <select class="form-control" id="customSelect" name="customisation">
                            <option value="">Aucun</option>
                            <?php foreach ($customisations as $customisation): ?>
                                <option value="<?= htmlspecialchars($customisation['id']); ?>" <?php echo $currentCustomisationId == $customisation['id'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($customisation['picture_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group col-12">
                        <h3><label for="descriptionProfil">Modifier la description de votre profil</label></h3>
                        <textarea class="form-control" id="descriptionProfil" rows="6" name="description" placeholder="Saisissez la description de votre profil" maxlength="350"><?php echo $userDescription; ?></textarea>
                    </div>

                    <h3>Modifier les liens vers vos réseaux sociaux</h3>

                    <div class="mb-5">
                        <div class="form-group col-12 mb-2">
                            <label for="facebook" class="col-12 d-flex align-items-center">
                                <i class="lni lni-facebook-original mx-2"></i>
                                <input class="form-control" id="facebook" name="facebook" value="<?php echo $userFacebook; ?>" placeholder="Saisissez votre URL vers Facebook" maxlength="100">
                            </label>
                        </div>

                        <div class="form-group col-12 mb-2">
                            <label for="instagram" class="col-12 d-flex align-items-center">
                                <i class="lni lni-instagram mx-2"></i>
                                <input class="form-control" id="instagram" value="<?php echo $userInstagram; ?>" name="instagram" placeholder="Saisissez votre URL vers Instagram" maxlength="100">
                            </label>
                        </div>

                        <div class="form-group col-12 mb-2">
                            <label for="twitter" class="col-12 d-flex align-items-center">
                                <i class="lni lni-twitter-original mx-2"></i>
                                <input class="form-control" id="twitter" value="<?php echo $userTwitter; ?>" name="twitter" placeholder="Saisissez votre URL vers Twitter" maxlength="100">
                            </label>
                        </div>

                        <div class="form-group col-12 mb-2">
                            <label for="youtube" class="col-12 d-flex align-items-center ">
                                <i class="lni lni-youtube mx-2"></i>
                                <input class="form-control" id="youtube" value="<?php echo $userYouTube; ?>" name="youtube" placeholder="Saisissez votre URL vers Youtube" maxlength="100">
                            </label>
                        </div>

                        <div class="form-group col-12 mb-2">
                            <label for="github" class="col-12 d-flex align-items-center">
                                <i class="lni lni-github-original mx-2"></i>
                                <input class="form-control" id="github" name="github" value="<?php echo $userGitHub; ?>" placeholder="Saisissez votre URL vers Github" maxlength="100">
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
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>

</html>
