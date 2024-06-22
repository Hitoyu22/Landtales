<?php
session_start();
setlocale(LC_TIME, 'fr_FR.utf8', 'fr_FR', 'fr');
require "Structure/Functions/function.php";
require "Includes/PHPMailerAutoload.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];
    $rank = $_SESSION['rank'];
    checkUserRole();

    require "Structure/Bdd/config.php";

    $userInfo = $bdd->prepare('SELECT profil_picture FROM client WHERE id = ?');
    $userInfo->execute(array($userId));
    $userExists = $userInfo->fetch();

    if ($userExists) {
        $userIconPath = isset($userExists['profil_picture']) ? $userExists['profil_picture'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
    }

    if (isset($_GET['id'])) {
        $travelId = $_GET['id'];
        $getTravelInfoQuery = $bdd->prepare("SELECT title,banner,miniature,content,travel_date,idclient,travel_status,visibility FROM travel WHERE id = ?");
        $getTravelInfoQuery->execute([$travelId]);
        $travelInfo = $getTravelInfoQuery->fetch(PDO::FETCH_ASSOC);

        if (!$travelInfo) {
            header("Location: homeFront.php");
            exit();
        }

        //Calcul du nombre de like déjà existant pour ce voyage.
        $countLikes = $bdd->prepare("SELECT COUNT(idtravel) AS likeCount FROM travel_like WHERE idtravel = ?");
        $countLikes->execute([$travelId]);
        $likeCount = $countLikes->fetch(PDO::FETCH_ASSOC)['likeCount'];

        $checkLikeQuery = $bdd->prepare("SELECT COUNT(idclient) AS isLiked FROM travel_like WHERE idclient = ? AND idtravel = ?");
        $checkLikeQuery->execute([$userId, $travelId]);
        $isLiked = $checkLikeQuery->fetch(PDO::FETCH_ASSOC)['isLiked'] > 0;

        // Vérification des vues sur le voyage, si l'utilisateur a déjà vu le voyage aujourd'hui, nous n'ajoutons pas de nouvelle vue.
        $checkViewUser = $bdd->prepare("SELECT COUNT(idtravel) AS viewCount FROM travel_view WHERE idtravel = ? AND idclient = ? AND DATE(travel_view_date) = CURDATE()");
        $checkViewUser->execute([$travelId, $userId]);
        $viewCount = $checkViewUser->fetch(PDO::FETCH_ASSOC)['viewCount'];

        if ($viewCount == 0){
            $newView = $bdd->prepare("INSERT INTO travel_view (idtravel, idclient, travel_view_date) VALUES (?,?, NOW())");
            $newView->execute([$travelId, $userId]);
        }

        //Calcul du nombre de vues pour le voyage après ajout potentiel
        $travelView = $bdd->prepare("SELECT COUNT(idtravel) AS viewCount FROM travel_view WHERE idtravel = ?");
        $travelView->execute([$travelId]);
        $totalView = $travelView->fetch(PDO::FETCH_ASSOC)['viewCount'];


        if ($travelInfo) {
            if (array_key_exists('title', $travelInfo)) $title = $travelInfo['title'];
            if (array_key_exists('banner', $travelInfo)) $bannerPath = $travelInfo['banner'];
            if (array_key_exists('miniature', $travelInfo)) $miniaturePath = $travelInfo['miniature'];
            if (array_key_exists('content', $travelInfo)) {
                $text = !empty($travelInfo['content']) ? $travelInfo['content'] : '{"time": 1717016088409,"blocks": [{"id": "TKq8_6dfsQ","data": {"text": "Le voyage est momentanément indisponible.","alignment": "left"},"type": "paragraph"},{"id": "JV3VxTYCmo","data": {"text": "Merci de réessayer ultérieurement.","alignment": "left"},"type": "paragraph"}],"version": "2.29.1"}';
            }
            if (array_key_exists('travel_date', $travelInfo)) $date = $travelInfo['travel_date'];
            if (array_key_exists('idclient', $travelInfo)) $idCreator = $travelInfo['idclient'];
            if (array_key_exists('travel_status', $travelInfo)) $statut = $travelInfo['travel_status'];
            if (array_key_exists('visibility', $travelInfo)) $visibility = $travelInfo['visibility'];
        }

        if (empty($text)) $text = "";

        $pageTitle = $title;

        if ($statut == 0) {
            header("Location: homeFront.php");
            exit();
        }

        //Vérification du statut d'accès au voyage (provisoire)
        if ($visibility != 1 && $visibility != 3) {
            header("Location: homeFront.php");
            exit();
        }

        //Récupération des informations du créateurs du voyage
        $userCreator = $bdd->prepare("SELECT email,pseudo,profil_picture FROM client WHERE id = ?");
        $userCreator->execute([$idCreator]);
        $creatorInfo = $userCreator->fetch(PDO::FETCH_ASSOC);
        $iconCreator = isset($creatorInfo['profil_picture']) ? $creatorInfo['profil_picture'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
        if ($creatorInfo && array_key_exists('pseudo', $creatorInfo)) $pseudoCreator = $creatorInfo['pseudo'];
        if ($creatorInfo && array_key_exists('pseudo', $creatorInfo)) $emailCreator = $creatorInfo['email'];

        //Insertion de la ligne de log en bdd et dans le fichier
        $logPath = "Admin/Structures/Logs/log.txt";
        $pageAction = "Découvre le voyage de quelqu'un";
        $pageId = 10;
        $logType = "Visite";

        logActivity($userId, $pseudo, $pageId, $logType, $logPath);


        $getCommentsQuery = $bdd->prepare("SELECT tc.id, tc.comment, c.pseudo, c.profil_picture , tc.travel_comment_date as dateComment FROM travel_comment tc INNER JOIN client c ON c.id = tc.idclient WHERE tc.idtravel = ? AND tc.idcomment IS NULL");
        $getCommentsQuery->execute([$travelId]);
        $comments = $getCommentsQuery->fetchAll(PDO::FETCH_ASSOC);

        $commentCount = count($comments);

        if(isset($_POST['delete_reason'])) {
            $deleteReason = $_POST['delete_reason'];

            $travelIdToDelete = $_GET['id'];

            deleteTravel($bdd,$travelIdToDelete);

            $titleTravel = str_replace('&#039;', "'", $title);

            $html = <<<HTML
                <!DOCTYPE html>
                <html lang="fr">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                </head>
                <body>
                <div class="div-pleine-page">
                    <div class="container text-center">
                        <div class="row">
                            <div class="d-flex flex-column align-items-right justify-content-center erdiv">
                                <div class="text-center mr-0 textee">
                                    <h1>{$pseudoCreator}</h1>
                                    <h3>Votre voyage ({$titleTravel}) a été supprimé de Landtales</h3>
                                    <p>Il est possible que votre voyage ne respectait pas certaines des règles de Landtales.</p>
                                    <p>Vous pouvez retrouver ci-dessous la justification de notre Modérateur : </p>
                                    <p style="color: red; font-weight: bold;">Raison de la suppression : {$deleteReason} </p>
                                    <p>Ce voyage a totalement été supprimé et ne pourra être restauré.</p>
                                    <p>Bien cordialement,</p>
                                    <p>Landtales</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </body>
                </html>
                                    
                HTML;

            $to = $emailCreator;
            $from = 'landtales.website@gmail.com';
            $name = 'Landtales';
            $subj = 'Suppression de l\'un de vos voyages';
            $msg = $html;

            $error = smtpmailer($to, $from, $name, $subj, $msg);

            header("Location: travelLobby.php");

        }

        if (isset($_POST['deleteComment'])) {
            $idComment = $_POST['idcomment_delete'];

            $deleteSubComments = $bdd->prepare("DELETE FROM travel_comment WHERE idcomment = ?");
            $deleteSubComments->execute([$idComment]);

            $deleteComments = $bdd->prepare("DELETE FROM travel_comment WHERE id = ?");
            $deleteComments->execute([$idComment]);

            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }

        if (isset($_POST['deleteCommentSecond'])) {
            $idComment = $_POST['idCommentSecond'];

            $deleteComments = $bdd->prepare("DELETE FROM travel_comment WHERE id = ?");
            $deleteComments->execute([$idComment]);

            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }


        if (isset($_POST['Envoyer'])) {
            $commentaire = htmlspecialchars($_POST['commentaire']);
            $idcomment = isset($_POST['idcomment']) ? intval($_POST['idcomment']) : NULL;
            $insertCommentQuery = $bdd->prepare("INSERT INTO travel_comment (comment, travel_comment_date, idtravel, idclient, idcomment) VALUES (?, NOW(), ?, ?, ?)");
            $insertCommentQuery->execute([$commentaire, $travelId, $userId, $idcomment]);

            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }

    } else {
        header("Location: travelLobby.php");
        exit();
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
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/editorjs.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">

    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main mt-5">
        <div class="banner-div">
            <img src="<?php echo $bannerPath; ?>" alt="Bannière du voyage" class="banner">
        </div>
        <div class="mt-5">
            <div class="container">
                <h1 class="text-center ml-0"><?php echo str_replace('&#039;', "'", $title); ?></h1>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex align-items-center">
                        <div>
                            <img src="<?php echo $iconCreator; ?>" alt="Photo de profil" class="rounded-circle ml-3" width="60" height="60">
                        </div>
                        <a title="Accéder au profil de <?php echo $pseudoCreator; ?>" href="userProfil.php?id=<?php echo $idCreator; ?>"><p class="pt-1 mx-3">Par <?php echo $pseudoCreator; ?></p></a>
                    </div>
                    <div>
                        <!-- Bouton supprimer -->
                        <?php if($_SESSION['rank'] == 3): ?>
                            <button type="button" onclick="confirmDelete()" class="btn btn-danger mt-2 rounded-pill">Supprimer le voyage</button>
                        <?php endif; ?>
                    </div>
                </div>
                <p>
                    <?php echo $totalView > 1 ? $totalView . " vues" : $totalView . " vue"; ?>
                    - Raconté le <?php echo formatFrenchDate($date); ?>
                </p>
                <div class="d-flex">
                    <button id="likeButton" onclick="handleLikeClick('<?php echo $travelId; ?>', '<?php echo $userId; ?>')" onmousedown="hideFocusBorder(this)" type="button" data-is-liked="<?php echo $isLiked ? '1' : '0'; ?>">
                        <span class="material-symbols-outlined mr-2">favorite</span>
                    </button>
                    <div class="like-count"><?php echo $likeCount; ?></div>
                </div>
                <div id="editor"></div>

                <h3>Les commentaires (<?php echo $commentCount > 1 ? $commentCount . " commentaires" : $commentCount . " commentaire";?>)</h3>
                <form class="commentaire" action="" method="post">
                    <div class="col-12 d-flex align-items-start mb-3 ">
                        <div class="d-flex mb-2 mx-3">
                            <img src="<?php echo $userIconPath; ?>" alt="Photo de profil" class="rounded-circle ml-3" width="60" height="60">
                        </div>
                        <div class="row col-12 ml-3">
                            <label for="comment" class="form-label col-12 ml-5">Ajouter un commentaire</label>
                            <div class="col-10">
                                <textarea class="form-control mt-3 width-80" id="comment" name="commentaire" rows="3" placeholder="Saisissez votre commentaire" oninput="toggleSubmitButton()" maxlength="256"></textarea>
                            </div>
                            <div class="col-12">
                                <button id="submitBtn" type="submit" name="Envoyer" class="btn btn-light btn-outline-dark d-none mt-2 rounded-pill">Envoyer</button>
                            </div>
                        </div>
                    </div>
                </form>


                <div class="mt-3">
                    <?php
                    // Parcours de chaque commentaire
                    foreach ($comments as $comment) {
                        // Récupération des informations sur l'utilisateur du commentaire

                        // Formatage de la date du commentaire
                        $dateComment = $comment['dateComment'];
                        $date_formattee = formatFrenchDate($dateComment);

                        // Récupération de l'icône utilisateur
                        $iconUser = isset($comment['profil_picture']) ? $comment['profil_picture'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";

                        // Affichage du commentaire
                        echo "<div class='comment col-12 d-flex align-items-start mb-3'>";
                        echo "<div class='d-flex'>";
                        echo "<div class='d-flex mb-2 mx-3'>";
                        echo "<img src='" . $iconUser . "' alt='Photo de profil' class='rounded-circle ml-3' width='60' height='60'>";
                        echo "</div>";
                        echo "<div class='col-10'>";
                        echo "<div class='row'>";
                        echo "<div class='col-12'>";
                        echo "<p class='mt-2'>" . $comment['pseudo'] . " - " . $date_formattee . "</p>";
                        echo "</div>";
                        echo "<div class='col-12'>";
                        echo "<p class='w-100 limited-text'>" . $comment['comment'] . "</p>";
                        echo "</div>";
                        echo "</div>";

                        // Bouton pour répondre au commentaire
                        echo "<button class='btn' onclick='showReplyForm(" . $comment['id'] . ")'>Répondre</button>";

                        // Formulaire de réponse (initialisé masqué)
                        echo "<form method='post' action='' id='replyForm" . $comment['id'] . "' class='reply-form d-none'>";
                        echo "<input type='hidden' name='idcomment' value='" . $comment['id'] . "'>";
                        echo "<div class='row'>";
                        echo "<div class='col-10'>";
                        echo "<textarea name='commentaire' class='form-control mt-3' oninput='toggleSubmitButton()' placeholder='Votre réponse'></textarea>";
                        echo "</div>";
                        echo "<div class='col-2'>";
                        echo "<button type='submit' name='Envoyer' class='btn btn-light btn-outline-dark mt-2 rounded-pill'>Envoyer</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</form>";

                        // Bouton pour afficher les réponses
                        echo "<button class='btn' id='repliesButton" . $comment['id'] . "' onclick=\"showReplies('" . $comment['id'] . "', '" . $_SESSION['idclient'] . "', '" . $idCreator . "', '" . $_SESSION['rank'] . "')\">Afficher les réponses</button>";

                        // Formulaire pour la suppression du commentaire (affiché seulement pour l'utilisateur ou l'administrateur)
                        if ($_SESSION['idclient'] == $idCreator || $_SESSION['rank'] == 3) {
                            echo "<form method='post' action='' class='delete-form'>";
                            echo "<input type='hidden' name='idcomment_delete' value='" . $comment['id'] . "'>";
                            echo "<button type='submit' name='deleteComment' class='btn btn-danger mt-2 rounded-pill'>Supprimer</button>";
                        }

                        // Div pour afficher les réponses
                        echo "<div id='replies" . $comment['id'] . "' class='replies d-none'></div>";

                        echo "</div>"; // Fermeture de la div col-10
                        echo "</div>"; // Fermeture de la div d-flex
                        echo "</div>"; // Fermeture de la div comment
                    }
                    ?>


                </div>
            </div>
        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>

</div>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
<script src="Structure/Functions/travel.js"></script>
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
    travelEditor(true, <?php echo $text;?>,<?php echo $travelId; ?>);
</script>
</body>
</html>


