<?php
session_start();
setlocale(LC_TIME, 'fr_FR.utf8', 'fr_FR', 'fr');
require "Structure/Functions/function.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];

    require "Structure/Bdd/config.php";

    $userInfo = $bdd->prepare('SELECT * FROM client WHERE id = ?');
    $userInfo->execute(array($userId));
    $userExists = $userInfo->fetch();

    if ($userExists) {
        if (array_key_exists('profil_picture', $userExists)) $userIconPath = $userExists['profil_picture'];
    }

    if (isset($_GET['id'])) {
        $travelId = $_GET['id'];
        $getTravelInfoQuery = $bdd->prepare("SELECT * FROM travel WHERE id = ?");
        $getTravelInfoQuery->execute([$travelId]);
        $travelInfo = $getTravelInfoQuery->fetch(PDO::FETCH_ASSOC);

        //Calcul du nombre de like déjà existant pour ce voyage.
        $countLikes = $bdd->prepare("SELECT COUNT(*) AS likeCount FROM travel_like WHERE idtravel = ?");
        $countLikes->execute([$travelId]);
        $likeCount = $countLikes->fetch(PDO::FETCH_ASSOC)['likeCount'];

        $checkLikeQuery = $bdd->prepare("SELECT COUNT(*) AS isLiked FROM travel_like WHERE idclient = ? AND idtravel = ?");
        $checkLikeQuery->execute([$userId, $travelId]);
        $isLiked = $checkLikeQuery->fetch(PDO::FETCH_ASSOC)['isLiked'] > 0;

        // Vérification des vues sur le voyage, si l'utilisateur a déjà vu le voyage aujourd'hui, nous n'ajoutons pas de nouvelle vue.
        $checkViewUser = $bdd->prepare("SELECT COUNT(*) AS viewCount FROM travel_view WHERE idtravel = ? AND idclient = ? AND DATE(travel_view_date) = CURDATE()");
        $checkViewUser->execute([$travelId, $userId]);
        $viewCount = $checkViewUser->fetch(PDO::FETCH_ASSOC)['viewCount'];

        if ($viewCount == 0){
            $newView = $bdd->prepare("INSERT INTO travel_view (idtravel, idclient, travel_view_date) VALUES (?,?, NOW())");
            $newView->execute([$travelId, $userId]);
        }

        //Calcul du nombre de vus pour le voyage après ajout potentiel
        $travelView = $bdd->prepare("SELECT COUNT(*) AS viewCount FROM travel_view WHERE idtravel = ?");
        $travelView->execute([$travelId]);
        $totalView = $travelView->fetch(PDO::FETCH_ASSOC)['viewCount'];


        if ($travelInfo) {
            if (array_key_exists('title', $travelInfo)) $title = $travelInfo['title'];
            if (array_key_exists('banner', $travelInfo)) $bannerPath = $travelInfo['banner'];
            if (array_key_exists('miniature', $travelInfo)) $miniaturePath = $travelInfo['miniature'];
            if (array_key_exists('content', $travelInfo)) $text = $travelInfo['content'];
            if (array_key_exists('travel_date', $travelInfo)) $date = $travelInfo['travel_date'];
            if (array_key_exists('idclient', $travelInfo)) $idCreator = $travelInfo['idclient'];
            if (array_key_exists('travel_status', $travelInfo)) $statut = $travelInfo['travel_status'];
            if (array_key_exists('visibility', $travelInfo)) $visibility = $travelInfo['visibility'];
        }

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
        $userCreator = $bdd->prepare("SELECT * FROM client WHERE id = ?");
        $userCreator->execute([$idCreator]);
        $creatorInfo = $userCreator->fetch(PDO::FETCH_ASSOC);
        if ($creatorInfo && array_key_exists('pseudo', $creatorInfo)) $pseudoCreator = $creatorInfo['pseudo'];
        if ($creatorInfo && array_key_exists('profil_picture', $creatorInfo)) $iconCreator = $creatorInfo['profil_picture'];


        $getCommentsQuery = $bdd->prepare("SELECT * FROM travel_comment WHERE idtravel = ?");
        $getCommentsQuery->execute([$travelId]);
        $comments = $getCommentsQuery->fetchAll(PDO::FETCH_ASSOC);


        $commentCount = count($comments);



        //Ajout d'un commentaire

        if (isset($_POST['Envoyer'])) {
            $commentaire = htmlspecialchars($_POST['commentaire']);
            $insertCommentQuery = $bdd->prepare("INSERT INTO travel_comment (comment, travel_comment_date, idtravel, idclient) VALUES (?, NOW(), ?, ?)");
            $insertCommentQuery->execute([$commentaire, $travelId, $userId]);

            header("Location: {$_SERVER['REQUEST_URI']}");
            exit();
        }

    } else {
        echo "ID de voyage non fourni.";
        exit();
    }

    require "Structure/Head/head.php";
} else {

    header("Location: login.php");
    exit();
}




?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/editorjs.css">
</head>
<body data-bs-theme="light">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">

    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main  mt-5">
        <div class=" banner-div">
            <img src="<?php echo $bannerPath; ?>" alt="Bannière du voyage" class="banner">
        </div>
        <div class=" mt-5">


            <div class="container">
                <h1 class="text-center ml-0"><?php echo $title; ?></h1>
                <div class="d-flex mb-2">
                    <img src="<?php echo $iconCreator; ?>" alt="Photo de profil" class="rounded-circle ml-3" width="40" height="40">
                    <a title="Accéder au profil de <?php echo $pseudoCreator; ?>" href="userProfil.php?id=<?php echo $idCreator; ?>"><p class="pt-1 mx-3">Par <?php echo $pseudoCreator; ?> </p></a>
                </div>
                <p>
                    <?php
                    echo $totalView > 1 ? $totalView . " vues" : $totalView . " vue";
                    ?>
                    - Raconté le <?php echo formatFrenchDate($date); ?>
                </p>
                <div class="d-flex">
                    <button id="likeButton" onclick="handleLikeClick()" onmousedown="hideFocusBorder(this)" type="button" data-is-liked="<?php echo $isLiked ? '1' : '0'; ?>">
                        <span class="material-symbols-outlined mr-2">favorite</span>
                    </button>
                    <div class="like-count"><?php echo $likeCount; ?></div>
                </div>
                <div id="editor"></div>



            <h3>Les commentaires (<?php echo $commentCount > 1 ? $commentCount . " commentaires" : $commentCount . " commentaire";?>)</h3>
            <form class="commentaire" action="" method="post">
                <div class="col-12 d-flex align-items-start mb-3 ">
                    <img src="<?php echo $userIconPath; ?>" alt="Photo de profil" class="rounded-circle mx-3" width="40" height="40">
                    <div class="row col-12 ml-3">
                        <label for="comment" class="form-label col-12 ml-5">Ajouter un commentaire</label>
                        <div class="col-10">
                            <textarea class="form-control mt-3 width-80" id="comment" name="commentaire" rows="3" placeholder="Saisissez votre commentaire" oninput="toggleSubmitButton()"></textarea>
                        </div>
                        <div class="col-12">
                            <button id="submitBtn" type="submit" name="Envoyer" class="btn btn-light btn-outline-dark d-none mt-2 rounded-pill">Envoyer</button>
                        </div>
                    </div>
                </div>
            </form>
            <?php
            if($comments) {
                foreach($comments as $comment) {
                    $userComments = $bdd->prepare("SELECT * FROM client WHERE id = ?");
                    $userComments->execute([$comment['idclient']]);
                    $userCommentsInfo = $userComments->fetch(PDO::FETCH_ASSOC);

                    $dateComment = $comment['travel_comment_date'];
                    $date_formattee = formatFrenchDate($dateComment);

                    echo "<div class='comment col-12 d-flex align-items-start mb-3 '>";
                    echo "<div class='d-flex'>";
                    echo "<img src='" . $userCommentsInfo['profil_picture'] . "' alt='' class='rounded-circle mx-3' width='40' height='40'>";
                    echo "<div class='col-12 ml-3'>";
                    echo "<p class='col-12 ml-5 mt-2'>" . $userCommentsInfo['pseudo'] . " - " . $date_formattee . "</p>";
                    echo "<div class='col-10'>";
                    echo "<p>" . $comment['comment'] . "</p>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>Aucun commentaire disponible.</p>";
            }
            ?>
            </div>
        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>

</div>
<script>
    function handleLikeClick() {
        var likeButton = document.getElementById('likeButton');
        var likeCountElement = document.querySelector('.like-count');

        // Obtient l'état actuel du like
        var isLiked = likeButton.getAttribute('data-is-liked') === '1';

        var travelId = <?php echo $travelId; ?>;
        var userId = <?php echo $userId; ?>;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'likesTreatment.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Met à jour l'état du like et le nombre de likes en conséquence
                        if (isLiked) {
                            likeButton.setAttribute('data-is-liked', '0');
                            likeButton.querySelector('.material-symbols-outlined').textContent = 'favorite_border';
                            likeCountElement.textContent = parseInt(likeCountElement.textContent) - 1; // Diminue le nombre de likes de 1
                        } else {
                            likeButton.setAttribute('data-is-liked', '1');
                            likeButton.querySelector('.material-symbols-outlined').textContent = 'favorite';
                            likeCountElement.textContent = parseInt(likeCountElement.textContent) + 1; // Augmente le nombre de likes de 1
                        }
                    } else {
                        console.error('Erreur : ' + response.message);
                    }
                } else {
                    console.error('Erreur de requête : ' + xhr.status);
                }
            }
        };
        xhr.send('travelId=' + travelId + '&userId=' + userId);
    }

</script>
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
<script>
        const articleId = "<?php echo $travelId; ?>";
        var initialData = <?php echo $text;?>;

        const editor = new EditorJS({
            onReady: () => {
                new Undo({ editor });
            },
            holder: 'editor',
            placeholder: 'Commencez à écrire...',
            readOnly:true,
            tools: {
                header: {
                    class: Header,
                    config: {
                        placeholder: 'Entrez un titre',
                        levels: [2, 3, 4, 5],
                        defaultLevel: 2
                    },
                },
                underline: Underline,
                list: {
                    class: List,
                    inlineToolbar: true,
                    config: {
                        defaultStyle: 'ordered'
                    }
                },
                warning: {
                    class: Warning,
                    inlineToolbar: true,
                    config: {
                        titlePlaceholder: 'Titre de l\'alerte',
                        messagePlaceholder: 'Message',
                    },
                },
                paragraph: {
                    class: Paragraph,
                    inlineToolbar: true,
                },
                strikethrough: Strikethrough,
                quote: {
                    class: Quote,
                    inlineToolbar: true,
                    config: {
                        quotePlaceholder: 'Entrez une citation',
                        captionPlaceholder: 'Entrez les informations sur l\'auteur de la citation',
                    },
                },
                delimiter: {
                    class: Delimiter,
                },
                marker: {
                    class: Marker,
                },
                embed: Embed,
                table: {
                    class: Table,
                    inlineToolbar: true,
                },
                checklist: {
                    class: Checklist,
                    inlineToolbar: true
                },
                image: {
                    class: ImageTool,
                    config: {
                        endpoints: {
                            byFile: 'http://localhost/upload.php',
                        },
                        additionalRequestData: {
                            articleId: articleId
                        }
                    }
                },

            },
            i18n: {
                messages: {
                    "ui": {
                        "blockTunes": {
                            "toggler": {
                                "Click to tune": "Cliquez pour régler",
                                "or drag to move": "ou déplacez pour déplacer"
                            }
                        },
                        "inlineToolbar": {
                            "converter": {
                                "Convert to": "Convertir en"
                            }
                        },
                        "toolbar": {
                            "toolbox": {
                                "Add": "Ajouter",
                                "Filter": "Rechercher",
                                "Nothing found": "Rien trouvé"
                            }
                        }

                    },
                    "toolNames": {
                        "Text": "Texte",
                        "Heading": "Titre",
                        "List": "Liste",
                        "Warning": "Avertissement",
                        "Checklist": "Liste de contrôle",
                        "Quote": "Citation",
                        "Code": "Code",
                        "Delimiter": "Délimiteur",
                        "Table": "Tableau",
                        "Link": "Lien",
                        "Marker": "Marqueur",
                        "Bold": "Gras",
                        "Italic": "Italique",
                        "Image": "Image",
                        "Underline" : "Souligner",
                        "Strikethrough" : "Barrer",
                    },
                    "tools": {
                        "link": {
                            "Add a link": "Ajouter un lien"
                        },
                        "stub": {
                            "The block can not be displayed correctly.": "Ce bloc ne peut pas être affiché correctement."
                        },
                        "image": {
                            "Caption": "Légende",
                            "Select an Image": "Sélectionner une image",
                            "With border": "Avec bordure",
                            "Stretch image": "Étirer l'image",
                            "With background": "Avec arrière-plan"
                        },
                        "linkTool": {
                            "Link": "Entrez l'adresse du lien",
                            "Couldn't fetch the link data": "Impossible de récupérer les données du lien",
                            "Couldn't get this link data, try the other one": "Impossible d'obtenir ces données de lien, essayez l'autre",
                            "Wrong response format from the server": "Format de réponse incorrect du serveur"
                        },
                        "header": {
                            "Header": "En-tête",
                            "Heading 2": "Titre 2",
                            "Heading 3": "Titre 3",
                            "Heading 4": "Titre 4",
                            "Heading 5": "Titre 5"
                        },
                        "paragraph": {
                            "Enter something": "Entrez quelque chose"
                        },
                        "list": {
                            "Ordered": "Liste ordonnée",
                            "Unordered": "Liste non ordonnée"
                        },
                        "table": {
                            "Heading": "Titre",
                            "Add column to left": "Ajouter une colonne à gauche",
                            "Add column to right": "Ajouter une colonne à droite",
                            "Delete column": "Supprimer la colonne",
                            "Add row above": "Ajouter une ligne au-dessus",
                            "Add row below": "Ajouter une ligne en-dessous",
                            "Delete row": "Supprimer la ligne",
                            "With headings": "Avec titres",
                            "Without headings": "Sans titres"
                        },
                        "quote": {
                            "Align Left": "Aligner à gauche",
                            "Align Center": "Centrer"
                        },

                    },
                    "blockTunes": {
                        "delete": {
                            "Delete": "Supprimer",
                            "Click to delete": "Cliquez pour supprimer"
                        },
                        "moveUp": {
                            "Move up": "Déplacer vers le haut"
                        },
                        "moveDown": {
                            "Move down": "Déplacer vers le bas"
                        },
                        "filter": {
                            "Filter": "Filtrer"
                        }
                    }
                }
            },
            data: initialData,
            onReady: () => {
                console.log('Editor.js is ready to work!')
            },
            onChange: () => {
                editor.save().then((savedData) => {
                    document.getElementById("json").value = JSON.stringify(savedData)
                    if (savedData.blocks.length > 0) {
                        document.getElementById("submit").disabled = false
                    }
                }).catch((error) => {
                    console.error('Saving error', error);
                });
            }
        });
    </script>
<script>
    function toggleSubmitButton() {
        var commentInput = document.getElementById("comment");
        var submitButton = document.getElementById("submitBtn");

        if (commentInput.value.trim() !== "") {
            submitButton.classList.remove("d-none");
        } else {
            submitButton.classList.add("d-none");
        }
    }
</script>
</body>
</html>


