<?php
session_start();

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];

    $pageTitle = "Envoi d'une newsletter";

    require "../Structure/Bdd/config.php";

    if (isset($_GET["id"]) && !empty($_GET['id'])) {
        $newsletterId = $_GET['id'];


        $newsletterSelect = $bdd->prepare("SELECT * FROM newsletter WHERE idnews_letter = ?");
        $newsletterSelect->execute([$newsletterId]);
        $newsletterInfo = $newsletterSelect->fetch();


        if ($newsletterInfo) {
            if (array_key_exists('titre', $newsletterInfo)) $subject = $newsletterInfo['titre'];
            if (array_key_exists('hello_user', $newsletterInfo)) $helloUserData = $newsletterInfo['hello_user'];

        }


        if (isset($_POST['publish'])) {
            if (isset($_POST['json']) && isset($_POST['subject']) && isset($_POST['userHello'])){
                $title = htmlspecialchars($_POST['subject']);
                $json = $_POST['json'];
                $hello_user = $_POST['userHello'];

                $newNewsletter = $bdd->prepare("UPDATE newsletter SET titre = ?,text = ?, hello_user = ? WHERE idnews_letter = ?");
                $newNewsletter->execute([$title,$json, $hello_user, $newsletterId]);

                header("Location: newsletterToHtml.php?id=$newsletterId");
            }

        }

    } else {
        header("Location: newsletterError.php");
    }
}

require "Structures/Head/headAdmin.php";

?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest/dist/editorjs.min.css">

<link rel="stylesheet" href="../Design/Css/style.css">
<link rel="stylesheet" href="../Design/Css/editorjs.css">

</head>
<body data-bs-theme="light">
<?php require "Structures/Navbar/navbarAdmin.php";?>
<div class="wrapper">

    <?php require "Structures/Sidebar/sidebarAdmin.php";?>

    <div class="main mt-5">
        <div class="mx-5 mt-5">
            <h1 class="mx-0">Rédiger le contenu de votre newsletter</h1>
            <form id="editor-form" method="post" action="" enctype="multipart/form-data">
                <div class="form-group mb-4">
                    <label for="subject" class="col-12">L'objet de votre newsletter</label>
                    <input  id="subject" class="col-12 col-md-6 form-control" name="subject" placeholder="Saississez l'objet de votre mail" value="<?php echo $subject;?>" type="text" >
                </div>
                <div class="form-group col-12 col-md-6 mb-4 ">
                    <label for="userHello">Choisissez comment interpeler les voyageurs</label>
                    <select name="userHello" id="userHello" class="form-select">
                        <option value="1" <?php echo ($helloUserData == 1) ? 'selected' : ''; ?>>Bonjour {#nom de l'utilisateur}</option>
                        <option value="2" <?php echo ($helloUserData == 2) ? 'selected' : ''; ?>>Salut {#nom de l'utilisateur}</option>
                        <option value="3" <?php echo ($helloUserData == 3) ? 'selected' : ''; ?>>Hey {#nom de l'utilisateur}</option>
                    </select>
                </div>
                <input type="hidden" id="json" name="json">
                <div class="container">
                    <div id="editor"></div>
                </div>

                <button type="submit" id="publish" class="btn-landtales" name="publish">Publier la newsletter</button>
            </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/marker@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/image@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/underline@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-undo"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
<script>
    const newsletterId = "<?php echo $newsletterId; ?>";

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
                    defaultStyle: 'ordered'
                }
            },
            marker: {
                class: Marker,
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: 'http://localhost/src/Endpoint/uploadNewsletter.php',
                    },
                    additionalRequestData: {
                        newsletterId: newsletterId
                    }
                }
            },
            embed: Embed,
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
                    "Marker": "Marqueur",
                    "Image": "Image",
                    "Embed": "Incorporer",
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
                    document.getElementById("publish").disabled = false;
                }
            }).catch((error) => {
                console.error('Saving error', error);
            });
        }
    });

</script>
<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>
</body>
</html>
