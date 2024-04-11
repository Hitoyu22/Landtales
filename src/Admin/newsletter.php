<?php
session_start();

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];

    $pageTitle = "Newsletter";

    require "../Structure/Bdd/config.php";

    if (isset($_GET["error"]) && !empty($_GET['error'])) {
        if ($_GET['error'] == 1) {
            echo "<script>alert('Une erreur est survenue lors du traitement des informations, merci de bien vouloir essayer de nouveau.')</script>";
        }
    } elseif (isset($_GET["validate"]) && $_GET["validate"] == 1) {
        echo "<script>alert('La newsletter a bien été envoyée.')</script>";
    } else {
        if (isset($_POST['publish'])) {
            if (isset($_POST['subject']) && isset($_POST['userHello'])) {
                $title = htmlspecialchars($_POST['subject']);
                $hello_user = $_POST['userHello'];

                $newNewsletter = $bdd->prepare("INSERT INTO newsletter (title, newsletter_date, idwriter, client_greeting ) VALUES (?, NOW(), ?,?)");
                $newNewsletter->execute([$title, $userId, $hello_user]);
                $newsletterId = $bdd->lastInsertId();

                header("Location: newsletterStepTwo.php?id=$newsletterId");
            }
        }
    }
}

require "Structures/Head/headAdmin.php";

?>
    <link rel="stylesheet" href="../Design/Css/style.css">
    <link rel="stylesheet" href="Design/Css/home-admin.css">
</head>
<body data-bs-theme="light">
<?php require "Structures/Navbar/navbarAdmin.php";?>
<div class="wrapper">

    <?php require "Structures/Sidebar/sidebarAdmin.php";?>

    <div class="main mt-5">
        <div class="mx-5">
            <h1 class="mt-5 mx-0">La Newsletter de Landtales</h1>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Sujet</th>
                    <th scope="col">Date</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>1</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>1</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>1</td>
                        <td>1</td>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>1</td>
                        <td>1</td>
                    </tr>
                </tbody>
            </table>
            <form id="editor-form" method="post" action="" enctype="multipart/form-data">
                <div class="form-group mb-4">
                    <label for="subject" class="col-12">L'objet de votre newsletter</label>
                    <input  id="subject" class="col-12 col-md-6 form-control" name="subject" placeholder="Saississez l'objet de votre mail" type="text" >
                </div>
                <div class="form-group col-12 col-md-6 mb-4 ">
                    <label for="userHello">Choisissez comment interpeler les voyageurs</label>
                    <select id="userHello" name="userHello" class="form-select">
                        <option value="1">Bonjour {#nom de l'utilisateur}</option>
                        <option value="2">Salut {#nom de l'utilisateur}</option>
                        <option value="3">Hey {#nom de l'utilisateur}</option>
                    </select>
                </div>

                <button type="submit" id="publish" class="btn-landtales" name="publish">Continuer votre newsletter newsletter</button>
            </form>
        </div>
    </div>
</div>
<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>




</body>
</html>
