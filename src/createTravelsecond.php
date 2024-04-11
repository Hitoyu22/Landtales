<?php
session_start();
$pageTitle = "Création d'un voyage | Partie 2";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    require "Structure/Bdd/config.php";

    if (isset($_GET['id'])) {
        $voyageId = $_GET['id'];


        $getVoyageInfoQuery = $bdd->prepare("SELECT * FROM travel WHERE id = ?");
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
        if (isset($_POST['title']) && isset($_POST['json']) && isset($_POST['visibility'])){
            $title = htmlspecialchars($_POST['title']);
            $json = $_POST['json'];
            $visibility = $_POST['visibility'];
            $summary = $_POST['summary'];

            if (!empty($json)) {
                if (isset($_POST['save'])) {
                    $stmt = $bdd->prepare("UPDATE travel SET title = ?, content = ?, travel_status = 0, visibility = ?, summary = ? WHERE id = ?");
                    if ($stmt->execute([$title, $json, $visibility,$summary, $voyageId])) {
                        header("Location: saveSuccess.php");
                    }
                } elseif (isset($_POST['publish'])) {
                    $stmt = $bdd->prepare("UPDATE travel SET title = ?, content = ?, travel_status = 1, visibility =?, summary = ? WHERE id = ?");

                    if ($stmt->execute([$title, $json, $visibility,$summary, $voyageId])) {
                        header("Location: publicationSuccess.php");
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


?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest/dist/editorjs.min.css">
    <link rel="stylesheet" href="Design/Css/editorjs.css">
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="Design/Css/style.css">
</head>
<body data-bs-theme="light">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">


    <?php require "Structure/Sidebar/sidebar.php";?>


    <div class="main  mt-5">
        <div class="mx-5">

            <h1 class="mt-5 mx-0">Créer votre voyage | Partie 2</h1>

            <form id="editor-form" method="post" action="" enctype="multipart/form-data">
                <div class="form-group mb-4">
                    <label for="title">Le titre de votre voyage</label>
                    <input type="text" id="title" name="title" value="<?php echo $title; ?>" placeholder="Saisissez votre titre" class="form-control">
                </div>
                <div class="form-group mb-4">
                    <label for="summary">Résumé de votre voyage (Ne sera affiché que si mis en avant sur votre profil)</label>
                    <textarea class="form-control" rows="3" id="summary" placeholder="Saisissez votre résumé" name="summary"  required><?php echo $summary ?></textarea>
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
                    <select class="form-control" id="genre" name="theme">
                        <option selected>Choisissez le thème de votre voyage (provisoirement)</option>
                        <option value="1">Espace</option>
                        <option value="2">Futuriste</option>
                        <option value="3">Médiéval</option>
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

<!-- Charger EditorJS et autres scripts -->
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
    const articleId = "<?php echo $voyageId; ?>";

    const editor = new EditorJS({
        onReady: () => {
            new Undo({ editor });
        },
        holder: 'editor',
        placeholder: 'Commencez à écrire...',
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
                    defaultStyle: 'unordered'
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
                        byFile: 'http://localhost/src/Endpoint/upload.php',
                    },
                    additionalRequestData: {
                        articleId: articleId
                    }
                }
            },
            link: false,
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
        data: {},
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
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>
