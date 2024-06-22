<?php
session_start();

$pageTitle = "Paramètres - Confidentialité";

require "Structure/Functions/function.php";
require "Structure/Functions/alerts.php";
require "Includes/PHPMailerAutoload.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkUserRole();
    require "Structure/Bdd/config.php";



    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $userInfo = $bdd->prepare('SELECT email, account_verification_key FROM client WHERE id = ?');
        $userInfo->execute([$userId]);
        $userData = $userInfo->fetch();

        if (isset($_POST['update'])){
            if (isset($_POST['visibility'])){
                $visibility = $_POST['visibility'];
            } else {
                $visibility = 1;
            }

            $userVisibility = $bdd->prepare("UPDATE client SET visibility = ? WHERE id = ?");
            $userVisibility->execute(array($visibility, $userId));

            header('Location: profileConfidentiality.php?change=success');
            exit();

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

            $linkhtml = 'https://landtales.freeddns.org/newPassword.php?iduser=' . $userId . '&account_verification_key=' . $account_verification_key;

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
                    <h3>Vous souhaitez changer de mot de passe</h3>
                    <p>Pas d'inquiétude, appuyer sur le lien ci-dessous pour être redirigé vers la page de changement de mot de passe.</p>
                    <p>Bien cordialement,</p>
                    <p>Landtales</p>
                </div>
                <div class="text-center mt-3 btnediv">
                <a href="{$linkhtml}">Changement de mot de passe.</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
                    
HTML;

            $to = $email;
            $from = 'landtales.website@gmail.com';
            $name = 'Landtales';
            $subj = 'Changement de mot de passe';
            $msg = $html;

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
dataChange();

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-front.css">
</head>

<body class="hidden" data-bs-theme="<?php echo $theme; ?>">

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
                            <a class="nav-link" href="profileDrawing.php">Votre dessin</a>
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

            <form method="post" action="">
                <div class="mb-3">
                    <h5>Modifier votre mot de passe</h5>
                    <p>Si vous souhaitez modifier votre mot de passe, appuyez sur le bouton ci-dessous pour qu’un mail soit envoyé à l’adresse suivante : <?php echo $emailUser; ?></p>
                    <button type="submit" class="btn-landtales mb-5" name="newPassword">Envoyer une demande de modification de mots de passe</button>
                </div>
            </form>

            <div class="mb-3">
                <h5>Télécharger vos données personnelles</h5>
                <p>En respect de l'article 20 du RGPD (Réglement Général de Protection des Données), nous mettons à votre disposition la possibilité de télécharger sous forme de PDF l'ensemble de vos données.</p>
                <p>Vous pouvez le télécharger via le lien ci-dessous.</p>
                <button onclick="window.location.href='Includes/genpdf.php'" class="btn-landtales">Télécharger vos données</button>
            </div>

        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>

</div>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>