<?php
session_start();

$pageTitle = "Paramètres - Signalement";

require "Structure/Functions/function.php";
require "Structure/Functions/alerts.php";


if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];
    checkUserRole();
    require "Structure/Bdd/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['signal'])){
            $report = $_POST['json'];
            $title = "Signalement de la part de $pseudo";

            $reportInfo = $bdd->prepare('INSERT INTO ticket (idsubmitter, summary, creation_date, ticket_type,title, priority) VALUES (?, ?, NOW(), ?,?,?)');
            $reportInfo->execute(array($userId, $report, 1, $title,1));

            header("Location: profileReporting.php?ticket=success");
            exit();
        }
    }

    require "Structure/Head/head.php";
} else {
    header("Location: login.php");
    exit();
}

ticketCreate();

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest/dist/editorjs.min.css">
<link rel="stylesheet" href="Design/Css/editorjs.css">
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
                            <a class="nav-link" href="profileCustom.php">Modifier le profil</a>
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
                            <a class="nav-link" href="profileReporting.php"><u>Signalement</u></a>
                        </li>
                    </ul>
                </div>
            </nav>

            <form method="post" action="">
                <div class="form-group mb-5">
                    <h5><label for="report">Vous avez rencontrez un problème lors de votre voyage ? Informez nous dès maintenant afin que l’on puisse régler le problème.</label></h5>
                    <div class="container">
                        <div id="editor"></div>
                    </div>
                    <input type="hidden" id="editorContent" name="json">
                </div>

                <button type="submit" id="publish" class="btn-landtales mb-5" name="signal">Envoyer un signalement</button>
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
<script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/underline@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-undo"></script>
<script src="https://cdn.jsdelivr.net/npm/@sotaproject/strikethrough@latest"></script>
<script src="Structure/Functions/editorjs.js"></script>
<script>
    initializeEditorTicket(false, "");
</script>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>
