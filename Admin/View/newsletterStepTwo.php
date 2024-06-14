<?php
require "Structure/Functions/function.php";
session_start();

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkAdminRole();

    $pageTitle = "Envoi d'une newsletter";

    require "Structure/Bdd/config.php";

    if (isset($_GET["id"]) && !empty($_GET['id'])) {
        $newsletterId = $_GET['id'];


        $newsletterSelect = $bdd->prepare("SELECT title, client_greeting FROM newsletter WHERE id = ?");
        $newsletterSelect->execute([$newsletterId]);
        $newsletterInfo = $newsletterSelect->fetch();


        if ($newsletterInfo) {
            if (array_key_exists('title', $newsletterInfo)) $subject = $newsletterInfo['title'];
            if (array_key_exists('client_greeting', $newsletterInfo)) $helloUserData = $newsletterInfo['client_greeting'];

        }

        if (isset($_POST['publish'])) {
            if (isset($_POST['json']) && isset($_POST['subject']) && isset($_POST['userHello'])){
                $title = htmlspecialchars($_POST['subject']);
                $json = $_POST['json'];
                $hello_user = $_POST['userHello'];

                $newNewsletter = $bdd->prepare("UPDATE newsletter SET title = ?,content = ?, client_greeting = ? WHERE id = ?");
                $newNewsletter->execute([$title,$json, $hello_user, $newsletterId]);

                header("Location: newsletterToHtml.php?id=$newsletterId");
            }

        }

    } else {
        header("Location: newsletterError.php");
    }
}

require "Admin/Structures/Head/headAdmin.php";
$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest/dist/editorjs.min.css">

<link rel="stylesheet" href="../Design/Css/style.css">
<link rel="stylesheet" href="../Design/Css/editorjs.css">

</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php";?>
<div class="wrapper">

    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php";?>

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
                    <select id="userHello" name="userHello" class="form-select">
                        <option value="1" <?php echo ($helloUserData == 1) ? 'selected' : ''; ?>>Bonjour</option>
                        <option value="2" <?php echo ($helloUserData == 2) ? 'selected' : ''; ?>>Salut</option>
                        <option value="3" <?php echo ($helloUserData == 3) ? 'selected' : ''; ?>>Hey</option>
                        <option value="4" <?php echo ($helloUserData == 4) ? 'selected' : ''; ?>>Yo</option>
                        <option value="5" <?php echo ($helloUserData == 5) ? 'selected' : ''; ?>>Hello</option>
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
<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/editorjs.js"></script>
<script src="../Structure/Functions/script.js"></script>
    <script>
        newsletterEditor(false,<?php echo $newsletterId; ?>, "");
    </script>
</body>
</html>
