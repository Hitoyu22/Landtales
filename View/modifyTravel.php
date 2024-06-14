<?php
session_start();
$pageTitle = "Modification de votre voyage";
require "Structure/Functions/function.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];
    checkUserRole();
    require "Structure/Bdd/config.php";

    if (isset($_GET['id'])) {
        $voyageId = $_GET['id'];

        $getVoyageInfoQuery = $bdd->prepare("SELECT title,banner,miniature,travel_status,summary,content,idclient,visibility,idtheme FROM travel WHERE id = ?");
        $getVoyageInfoQuery->execute([$voyageId]);
        $voyageInfo = $getVoyageInfoQuery->fetch(PDO::FETCH_ASSOC);

        if ($voyageInfo) {
            if (empty($voyageInfo['title'])){$title = "";} else {$title = $voyageInfo['title'];}
            if (empty($voyageInfo['banner'])){$bannerPath = "";} else {$bannerPath = $voyageInfo['banner'];}
            if (empty($voyageInfo['miniature'])){$miniaturePath = "";} else {$miniaturePath = $voyageInfo['miniature'];}
            if (empty($voyageInfo['travel_status'])){$travelstatus = "";} else {$travelstatus = $voyageInfo['travel_status'];}
            if (empty($voyageInfo['summary'])){$summary = "";} else {$summary = $voyageInfo['summary'];}
            if (array_key_exists('content', $voyageInfo)) {
                $text = !empty($voyageInfo['content']) ? $voyageInfo['content'] : '{"time": 1717016088409,"blocks": [{"id": "TKq8_6dfsQ","data": {"text": "Le voyage est momentanément indisponible.","alignment": "left"},"type": "paragraph"},{"id": "JV3VxTYCmo","data": {"text": "Merci de réessayer ultérieurement.","alignment": "left"},"type": "paragraph"}],"version": "2.29.1"}';
            }
            if (empty($voyageInfo['idclient'])){$idclient = "";} else {$idclient = $voyageInfo['idclient'];}
            if (empty($voyageInfo['visibility'])){$visibility = "";} else {$visibility = $voyageInfo['visibility'];}
            $selectedTheme = isset($voyageInfo['idtheme']) ? $voyageInfo['idtheme'] : 1;

            if ($userId !== $idclient){
                header("Location: homeFront.php");
            }

            $travelTheme = $bdd->prepare("SELECT id, theme_name FROM travel_theme");
            $travelTheme->execute();
            $travelThemeValue = $travelTheme->fetchAll(PDO::FETCH_ASSOC);

        }
    } else {
        header ("Location: homefront.php");
    }

    //Mise à jour des informations si besoin
    if (isset($_POST['save'])){

        //Remplacement de la bannière
        if (!empty($_FILES['newBanner']['tmp_name'])) {

            if (!empty($bannerPath) && file_exists($bannerPath)) {
                unlink($bannerPath);
            }

            $newBannerPath = $_FILES['newBanner']['tmp_name'];
            $bannerFileName = $_FILES['newBanner']['name'];
            $bannerDestination = 'Ressources/Travel/' . $voyageId . '/banner/' . $bannerFileName;


            if (move_uploaded_file($newBannerPath, $bannerDestination)) {

                //Modification de l'url dans la bdd
                $stmt = $bdd->prepare("UPDATE travel SET banner = ? WHERE id = ?");
                $stmt->execute([$bannerDestination, $voyageId]);
            }
        }

        //Remplacement de la miniature
        if (!empty($_FILES['newMiniature']['tmp_name'])) {
            if (!empty($miniaturePath) && file_exists($miniaturePath)) {
                unlink($miniaturePath);
            }

            $newMiniaturePath = $_FILES['newMiniature']['tmp_name'];
            $miniatureFileName = $_FILES['newMiniature']['name'];
            $miniatureDestination = 'Ressources/Travel/' . $voyageId . '/miniature/' . $miniatureFileName;


            if (move_uploaded_file($newMiniaturePath, $miniatureDestination)) {

                //Modification de l'url dans la bdd
                $stmt = $bdd->prepare("UPDATE travel SET miniature = ? WHERE id = ?");
                $stmt->execute([$miniatureDestination, $voyageId]);

            }
        }

        //Récupération du titre (potentiellement modifié, du json de l'éditeur et de niveau de visibilité du voyage
        if (isset($_POST['title']) && isset($_POST['visibility']) && isset($_POST['theme'])){
            $title = htmlspecialchars($_POST['title']);
            $json = $_POST['json'];
            $visibility = $_POST['visibility'];
            $summary = $_POST['summary'];
            $travelstatus = $_POST['status'];
            $theme = $_POST['theme'];

            if (!empty($json)) {

                    $stmt = $bdd->prepare("UPDATE travel SET title = ?, content = ?, travel_status = ?, visibility = ?, summary = ?, idtheme = ? WHERE id = ?");
                    if ($stmt->execute([$title, $json, $visibility,$travelstatus,$summary, $theme, $voyageId])) {

                        $logPath = "Admin/Structures/Logs/log.txt";
                        $pageAction = "Modification d'un voyage";
                        $pageId = 11;
                        $logType = "Modification d'un voyage";

                        logActivity($userId, $pseudo, $pageId, $logType, $logPath);

                        header("Location: modificationSuccess.php");

                }
                $errorsJson = "Vous devez écrire un voyage afin de pouvoir totalement le publier ou l'enregister";
            }
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest/dist/editorjs.min.css">
<link rel="stylesheet" href="Design/Css/editorjs.css">
<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">


    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main  mt-5">
        <div class="mx-5">

            <h1 class="mt-5 mx-0">Modification de votre voyage</h1>

            <form id="editor-form" method="post" action="" enctype="multipart/form-data">
                <div class="form-group mb-4">
                    <label for="title">Le titre de votre voyage</label>
                    <input type="text" id="title" name="title" value="<?php echo $title; ?>" placeholder="Saisissez votre titre" class="form-control" maxlength="128">
                </div>
                <div class="form-group mb-4">
                    <label for="summary">Résumé de votre voyage (Ne sera affiché que si mis en avant sur votre profil)</label>
                    <textarea class="form-control" rows="3" id="summary" placeholder="Saisissez votre résumé" name="summary"  required maxlength="256"><?php echo $summary ?></textarea>
                </div>
                <input type="hidden" id="json" name="json">
                <div class="container">
                    <div id="editor"></div>
                </div>
                <div class="col-12 pl-0 pr-0">
                    <div class="mb-4">
                        <label for="status">Statut du voyage :</label><br>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="draft" value="0" <?php if ($travelstatus == 0) echo 'checked'; ?>>
                            <label class="form-check-label" for="draft">Brouillon</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="status" id="published" value="1" <?php if ($travelstatus == 1) echo 'checked'; ?>>
                            <label class="form-check-label" for="published">Publié</label>
                        </div>

                    </div>
                    <div class="form-group mb-4">
                        <label>Visibilité de votre voyage</label>
                        <div>
                            <input type="radio" id="public" name="visibility" value="1" <?php echo ($visibility == 1) ? 'checked' : ''; ?> />
                            <label for="public">Public</label>
                        </div>

                        <div>
                            <input type="radio" id="private" name="visibility" value="2" <?php echo ($visibility == 2) ? 'checked' : ''; ?> />
                            <label for="private">Privé</label>
                        </div>
                        <div>
                            <input type="radio" id="unseen" name="visibility" value="3" <?php echo ($visibility == 3) ? 'checked' : ''; ?> />
                            <label for="unseen">Non répertorié</label>
                        </div>
                    </div>
                    <div class="form-group col-12 col-md-6 mb-4 ">
                        <label for="genre">Thème du voyage</label>
                        <select class="form-control" id="genre" name="theme" required>
                            <option>Choisissez le thème de votre voyage</option>
                            <?php foreach ($travelThemeValue as $theme): ?>
                                <option value="<?php echo $theme['id']; ?>"
                                    <?php if ($theme['id'] == $selectedTheme) echo 'selected'; ?>>
                                    <?php echo $theme['theme_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" id="save" class="btn-landtales mb-5" name="save">Enregistrer</button>
                </div>
            </form>
        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/marker@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@sotaproject/strikethrough@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-paragraph-with-alignment@3.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/underline@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-undo"></script>
<script src="https://cdn.jsdelivr.net/npm/@sotaproject/strikethrough@latest"></script>

<script src="Structure/Functions/editorjs.js"></script>

<script>
    travelEditor(false, <?php echo $text;?>,<?php echo $voyageId; ?>);
</script>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>
