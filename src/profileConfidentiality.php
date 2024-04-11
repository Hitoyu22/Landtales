<?php
session_start();

$pageTitle = "Paramètres - Vos voyages";

require "Structure/Functions/function.php";
require "Includes/PHPMailerAutoload.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    require "Structure/Bdd/config.php";

    $userInfo = $bdd->prepare('SELECT email, account_verification_key FROM client WHERE id = ?');
    $userInfo->execute([$userId]);
    $userData = $userInfo->fetch();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST['update'])){
            if (isset($_POST['visibility'])){
                $visibility = $_POST['visibility'];
            } else {
                $visibility = 1;
            }

            $userVisibility = $bdd->prepare("UPDATE client SET visibility = ? WHERE id = ?");
            $userVisibility->execute(array($visibility, $userId));

        }

        if(isset($_POST['newPassword'])) {



            if ($userData) {
                $email = $userData['email'];
                $account_verification_key = $userData['account_verification_key'];
            } else {
                $email = "";
                $account_verification_key = "";
            }

            echo $email;
            echo $account_verification_key;

            $to = $email;
            $from = 'landtales.website@gmail.com';
            $name = 'Landtales';
            $subj = 'Changement de compte de compte';
            $msg = '<h1>Changement de mot de passe</h1><br>http://localhost/src/newPassword.php?iduser=' . $userId . '&account_verification_key=' . $account_verification_key;

            $error = smtpmailer($to, $from, $name, $subj, $msg);

        }

        header("Location: {$_SERVER['REQUEST_URI']}");
    }

    $visibilityUser = $bdd->prepare("SELECT visibility FROM client WHERE id = ?");
    $visibilityUser->execute([$userId]);
    $visibilityAccount = $visibilityUser->fetch();

    if(isset($userData['email'])){
        $emailUser = $userData['email'];
    } else {
        $emailUser = "";
    }



    require "Structure/Head/head.php";
} else {
    header("Location: login.php");
    exit();
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-front.css">
</head>

<body data-bs-theme="light">

<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">
    <?php require "Structure/Sidebar/sidebar.php";?>
    <div class="main  mt-5">
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
                            <a class="nav-link" href="profileTravel.php">Vos voyages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileConfidentiality.php"><u>Confidentialité</u></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileReporting.php">Signalement</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <form method="post" action="">
                <div class="form-group mb-5">
                    <h5>Modifier la confidentialité de votre compte</h5>
                    <div>
                        <input type="radio" id="public" name="visibility" value="1" <?php echo ($visibilityAccount['visibility'] == 1) ? 'checked' : ''; ?> />
                        <label for="public">Public</label>
                    </div>

                    <div>
                        <input type="radio" id="private" name="visibility" value="2" <?php echo ($visibilityAccount['visibility'] == 2) ? 'checked' : ''; ?> />
                        <label for="private">Privé</label>
                    </div>
                </div>

                <button type="submit" id="publish" class="btn-landtales mb-5" name="update">Mettre à jour le niveau de confidentialité</button>
            </form>

            <form>
                <div class="mb-5">
                    <h5>Modifier votre mot de passe</h5>
                    <p>Si vous souhaitez modifier votre mot de passe, appuyez sur le bouton ci-dessous pour qu’un mail soit envoyé à l’adresse suivante : <?php echo $emailUser; ?></p>
                    <button type="submit" id="publish" class="btn-landtales mb-5" name="newPassword">Envoyer une demande de modification de mots de passe</button>
                </div>
            </form>

        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>

</div>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>