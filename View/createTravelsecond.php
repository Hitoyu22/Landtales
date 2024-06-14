<?php
session_start();
$pageTitle = "Création d'un voyage | Partie 2";
require "Structure/Functions/function.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];
    checkUserRole();
    require "Structure/Bdd/config.php";

    if (isset($_GET['id'])) {
        $voyageId = $_GET['id'];

        $getVoyageInfoQuery = $bdd->prepare("SELECT title,banner,miniature,travel_status,summary FROM travel WHERE id = ?");
        $getVoyageInfoQuery->execute([$voyageId]);
        $voyageInfo = $getVoyageInfoQuery->fetch(PDO::FETCH_ASSOC);


        if ($voyageInfo) {
            if (array_key_exists('title', $voyageInfo)) $title = $voyageInfo['title'];
            if (array_key_exists('banner', $voyageInfo)) $bannerPath = $voyageInfo['banner'];
            if (array_key_exists('miniature', $voyageInfo)) $miniaturePath = $voyageInfo['miniature'];
            if (array_key_exists('travel_status',$voyageInfo) )$travelstatus = $voyageInfo['travel_status'];
            if (array_key_exists('summary',$voyageInfo) )$summary = $voyageInfo['summary'];

            if ($travelstatus == 1){
                header("Location: travel.php?id=$voyageId");
            }
        }
    } else {
        header ("Location: homefront.php");
    }

    $travelTheme = $bdd->prepare("SELECT id, theme_name FROM travel_theme");
    $travelTheme->execute();
    $travelThemeValue = $travelTheme->fetchAll(PDO::FETCH_ASSOC);

    //Mise à jour des informations si besoin
    if (isset($_POST['save']) || isset($_POST['publish'])) {

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

        // Vérifier et traiter les autres champs du formulaire

        //Récupération du titre (potentiellement modifié, du json de l'éditeur et de niveau de visibilité du voyage
        if (isset($_POST['title']) && isset($_POST['json']) && isset($_POST['visibility']) && isset($_POST['theme'])){
            $title = htmlspecialchars($_POST['title']);
            $json = $_POST['json'];
            $visibility = $_POST['visibility'];
            $summary = $_POST['summary'];
            $theme = $_POST['theme'];

            if (!empty($json)) {
                if (isset($_POST['save'])) {
                    $stmt = $bdd->prepare("UPDATE travel SET title = ?, content = ?, travel_status = 0, visibility = ?, summary = ?, idtheme = ? WHERE id = ?");
                    if ($stmt->execute([$title, $json, $visibility,$summary,$theme, $voyageId])) {
                        header("Location: saveSuccess.php");
                    }
                } elseif (isset($_POST['publish'])) {
                    $stmt = $bdd->prepare("UPDATE travel SET title = ?, content = ?, travel_status = 1, visibility =?, summary = ?, idtheme = ? WHERE id = ?");

                    if ($stmt->execute([$title, $json, $visibility,$summary,$theme, $voyageId])) {

                        $logPath = "Admin/Structures/Logs/log.txt";
                        $pageAction = "Création d'un voyage";
                        $pageId = 2;
                        $logType = "Création de voyage";

                        logActivity($userId, $pseudo, $pageId, $logType, $logPath);

                        header("Location: publicationSuccess.php?id=$voyageId");
                    }
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

            <h1 class="mt-5 mx-0">Créer votre voyage | Partie 2</h1>

            <form id="editor-form" method="post" action="" enctype="multipart/form-data">
                <div class="form-group mb-4">
                    <label for="title">Le titre de votre voyage</label>
                    <input type="text" id="title" name="title" value="<?php echo html_entity_decode($title); ?>" placeholder="Saisissez votre titre" class="form-control">
                </div>
                <div class="form-group mb-4">
                    <label for="summary">Résumé de votre voyage (Ne sera affiché que si mis en avant sur votre profil)</label>
                    <textarea class="form-control" rows="3" id="summary" placeholder="Saisissez votre résumé" name="summary"  required><?php echo html_entity_decode($summary) ?></textarea>
                </div>
                <div class="container">
                    <div id="editor"></div>
                </div>
                <div class="col-12 pl-0 pr-0">
                    <div class="form-group mb-4">
                        <label>Visibilité de votre voyage</label>
                        <div>
                            <input type="radio" id="public" name="visibility" value="1" checked/>
                            <label for="public">Public</label>
                        </div>

                        <div>
                            <input type="radio" id="private" name="visibility" value="2" />
                            <label for="private">Privé</label>
                        </div>
                        <div>
                            <input type="radio" id="unseen" name="visibility" value="3"/>
                            <label for="unseen">Non répertorié</label>
                        </div>
                    </div>
                <div class="form-group col-12 col-md-6 mb-4 ">
                    <label for="genre">Thème du voyage</label>
                    <select class="form-control" id="genre" name="theme" required>
                        <?php foreach ($travelThemeValue as $theme){ ?>
                            <option value="<?php echo $theme['id']; ?>">
                                <?php echo $theme['theme_name']; ?>
                            </option>
                        <?php }; ?>
                    </select>
                </div>
                </div>
                <input type="hidden" id="json" name="json">
                <div class="mb-5">
                <button type="submit" id="save" class="btn-landtales" name="save">Enregistrer (en Brouillon)</button>
                <button type="submit" id="publish" class="btn-landtales" name="publish">Publier</button>
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
    travelEditor(false, "",<?php echo $voyageId; ?>);
</script>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>
