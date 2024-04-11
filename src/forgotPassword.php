<?php
$pageTitle = "Mot de passe oublié";

require "Includes/PHPMailerAutoload.php";
require "Structure/Functions/function.php";
session_start();

require "Structure/Bdd/config.php";

if (isset($_POST['valider'])) {
    $errors = array();

    if (!empty($_POST["email"])) {
        $email = $_POST["email"];
    } else {
        $errors[] = "Veuillez saisir votre email";
    }
    if (empty($errors)) {
        $query = $bdd->prepare('SELECT id, account_verification_key FROM client WHERE email = ?');
        $query->execute(array($email));
        $userInfo = $query->fetch();

        if ($userInfo) {
            $iduser = $userInfo['id'];
            $cle_verif_account = $userInfo['account_verification_key'];
        }else {
            $errorMail = "Ce mail n'est pas celui d'un membre de Landtales. Merci de bien vouloir réessayer.";
        }

        $to = $email;
        $from = 'landtales.website@gmail.com';
        $name = 'Landtales';
        $subj = 'Confirmation de compte';
        $msg = '<h1>Récupération de compte</h1><br>http://localhost/src/newPassword.php?iduser=' . $iduser . '&cle_verif_account=' . $cle_verif_account;

        $error = smtpmailer($to, $from, $name, $subj, $msg);

        header('Location: .php');
    }

}

require "Structure/Head/head.php";
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
                <input type="email" id="email" name="email" autocomplete="off" class="form-control" placeholder="Saisissez votre adresse mail">
            </div>


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
</body>
</html>
