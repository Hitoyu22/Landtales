<?php
$pageTitle = "Mot de passe oublié";

require "Includes/PHPMailerAutoload.php";
require "Structure/Functions/function.php";
session_start();

require "Structure/Bdd/config.php";
$errors = "";
if (isset($_POST['valider'])) {


    if (!empty($_POST["email"])) {
        $email = $_POST["email"];

        $search = $bdd->prepare("SELECT email, permaBan, tempBan FROM client WHERE email = ?");
        $search->execute(array($email));


        if ($search->rowCount() == 0) {
            $errors = "L'email n'existe pas";
        }

        $datas = $search->fetch();

        $today = date('Y-m-d');

        if ($datas['permaBan'] == 1) {
            header("Location: forgotPassword.php?permaBan=true");
            exit();
        } elseif (!is_null($datas['tempBan']) && $datas['tempBan'] >= $today) {
            $tempBanDate = urlencode($datas['tempBan']);  // Encode the date properly
            header("Location: forgotPassword.php?tempBan=true&date={$tempBanDate}");
            exit();
        }

    } else {
        $errors = "Veuillez saisir votre email";
    }
    if (empty($errors)) {
        $query = $bdd->prepare('SELECT id, account_verification_key FROM client WHERE email = ?');
        $query->execute(array($email));
        $userInfo = $query->fetch();

        if ($userInfo) {
            $iduser = $userInfo['id'];
            $account_verification_key = $userInfo['account_verification_key'];
        }else {
            $errorMail = "Ce mail n'est pas celui d'un membre de Landtales. Merci de bien vouloir réessayer.";
        }


        $linkhtml = 'https://landtales.freeddns.org/newPassword.php?iduser=' . $iduser . '&account_verification_key=' . $account_verification_key;

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
                    <h3>Mot de passe oublié ?</h3>
                    <p>Pas d'inquiétude, appuyer sur le lien ci-dessous pour être redirigé vers la page de changement de mot de passe.</p>
                    <p>Bien cordialement,</p>
                    <p>Landtales</p>
                </div>
                <div class="text-center mt-3 btnediv">
                <a href="{$linkhtml}">Nouveau mot de passe.</a>
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
        $subj = 'Landtales - Mot de passe oublié';
        $msg =  $html;

        $error = smtpmailer($to, $from, $name, $subj, $msg);

        header('Location: passwordRequestConfirmation.php');
    }

}

require "Structure/Head/head.php";

tempBan();
permaBan();
?>
<link rel="stylesheet" href="Design/Css/login.css">
</head>
<body>
<div class="container col-md-12 col-ms-12">
    <div class="login-form">
        <h1 class="text-white fw-bold text-center mb-5">Mot de passe oublié ?</h1>
        <h4 class="text-white fw-bold text-center mb-5">Pas de panique, cela arrive à tout le monde.<br>Saisissez votre email pour régler le problème.</h4>
        <form method="post" action="">
            <label for="password" class="text-white">Email</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-envelope fa-lg"></i>
                <input type="email" id="email" name="email" autocomplete="off" placeholder="Saisissez votre adresse mail">

            </div>
            <p class="text-danger"><?php echo $errors; ?></p>


            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-login" name="valider" >Connexion</button>
            </div>
        </form>

        <script>
            function togglePassword(inputId, iconId) {
                var passwordField = document.getElementById(inputId);
                var passwordToggleBtn = document.getElementById(iconId);

                if (passwordField.type === "password") {
                    passwordField.type = "text";
                    passwordToggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
                } else {
                    passwordField.type = "password";
                    passwordToggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
                }
            }
        </script>
        <script src="Structure/Functions/bootstrap.js"></script>
        <script src="Structure/Functions/script.js"></script>
</body>
</html>
