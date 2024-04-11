<?php
$pageTitle = "Inscription";

require "Includes/PHPMailerAutoload.php";
require "Structure/Functions/function.php";
session_start();

require "Structure/Bdd/config.php";

$errorEmail = $errorPseudo = $errorPassword = $errorConfirmPassword = $errorCheck = $errorCaptcha = "";


    $captcha = $bdd->prepare("SELECT question, answer FROM captcha ORDER BY RAND() LIMIT 1");
    $captcha->execute();
    $captchaData = $captcha->fetch(PDO::FETCH_ASSOC);
    $question = $captchaData['question'];
    $captchaAnswer = strtolower($captchaData['answer']);

    if (isset($_POST['valider'])) {

    //Récupération des données

    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $pseudoLength = strlen($_POST['pseudo']);
    $mailLength = strlen($_POST['email']);


    $recuperationEmail = $bdd->prepare("SELECT * FROM client WHERE email = ?");
    $recuperationEmail->execute(array($email));
    $nbEmail = $recuperationEmail->rowCount();


    $captchaAnswerTrue = strtolower($_POST['captcha_answer']);
    $reponseCaptchaUser = strtolower($_POST['captcha']);
    similar_text(strtolower($reponseCaptchaUser), $captchaAnswerTrue, $percent);

        if ($percent < 80) {
            $errorCaptcha = "La réponse au captcha est incorrecte.";
        }

    //Vérification de la conformité des données

    if (empty($_POST['email'])) $errorEmail = "L'email est requis.";
    if (empty($_POST['pseudo'])) $errorPseudo = "Le nom d'utilisateur est requis.";
    if (empty($_POST['password'])) $errorPassword = "Le mot de passe est requis.";
    if (!isset($_POST['termCondition'])) $errorCheck = "Vous devez accepter les règles et conditions d'utilisation pour créer un compte.";
    if ($pseudoLength > 64) $errorPseudo = "Votre nom d'utilisateur ne peut pas dépasser 64 caractères.";
    if ($mailLength > 128) $errorEmail = "Cette adresse mail ne peut pas dépasser 128 caractères.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errorEmail = "Cette adresse mail n'est pas valide.";
    if ($nbEmail > 0) $errorEmail = "Cette adresse mail est déjà utilisée.";
    if ($password != $confirmPassword) $errorPassword = "Les mots de passe ne correspondent pas.";
    if (!isset($_POST['termCondition'])) $errorCheck = "Vous devez accepter les règles et conditions d'utilisation pour créer un compte.";
    if ($percent < 80) $errorCaptcha = "La réponse au captcha est incorrecte.";

    if (!empty($password) && !preg_match('/^(?=.*\d)(?=.*[&\-é_èçà^ù*:!#~@°%§+.])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z&\-é_èçà^ù*:!#~@°%§+.]{12,64}$/', $password)) {
            if (isset($errorPassword)) {
                $errorPassword .= "Les mots de passe ne correspondent pas. <br> Le mot de passe doit comporter au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial de à @ é è & ç ù _ ! . + - : # % § ^ * ~ °.";
            } else {
                $errorPassword = "Le mot de passe doit comporter au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial de à @ é è & ç ù _ ! . + - : # % § ^ * ~ °.";
            }
        }

        //Création du profil dans la BDD

        if (empty($errorPseudo) && empty($errorEmail) && empty($errorPassword) && empty($errorCheck) && empty($errorCaptcha)) {
            $account_verification_key = rand(1000000, 9000000);
            $email = htmlspecialchars($_POST["email"]);
            $pseudo = htmlspecialchars($_POST["pseudo"]);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $newsletterValue = isset($_POST['newsletter']) ? $_POST['newsletter'] : 0;

            $insererUser = $bdd->prepare('INSERT INTO client (email, pseudo, client_password, account_verification_key ,account_verificated,news_letter_accepted, idrank) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $insererUser->execute(array($email, $pseudo, $password, $account_verification_key, 0, $newsletterValue, 1));

            $recupUser = $bdd->prepare('SELECT id FROM client WHERE email = ?');
            $recupUser->execute(array($email));

            if ($recupUser->rowCount() > 0) {
                $usersInfos = $recupUser->fetch();
                $_SESSION['idclient'] = $usersInfos['id'];
            }



            //Envoi du mail de confirmation

            $to = $email;
            $from = 'landtales.website@gmail.com';
            $name = 'Landtales';
            $subj = 'Confirmation de compte';
            $msg = '<h1>Confirmation</h1><br>http://localhost/src/verification.php?idclient=' . $_SESSION['idclient'] . '&account_verification_key=' . $account_verification_key;

            $error = smtpmailer($to, $from, $name, $subj, $msg);



            header('Location: mailConfirmation.php');
        }

}


require "Structure/Head/head.php";
?>

    <link rel="stylesheet" href="Design/Css/login.css">
</head>
<body>
<div class="container col-md-12 col-ms-12">
    <div class="login-form">
        <h1 class="text-white font-weight-bold text-center ">S'inscrire</h1>
        <p class="text-white fw-bold text-center mb-3">Vous possédez déjà un compte Landtales ? <a href="login.php">Connectez-vous !</a></p>
        <form method="post" action="" autocomplete="off">


            <label for="email" class="text-white font-weight-bold">Email</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-envelope fa-lg"></i>
                <input id="email" type="email" name="email" autocomplete="off" placeholder="Saisissez votre adresse mail" required>
            </div>
            <p class="text-danger font-weight-bold"><?php if (isset($errorEmail)) echo $errorEmail; ?></p>


            <label for="pseudo" class="text-white font-weight-bold">Nom d'utilisateur</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-user fa-lg"></i>
                <input id="pseudo" type="text" name="pseudo" autocomplete="off" placeholder="Saisissez votre nom d'utilisateur" required>
            </div>
            <p class="text-danger font-weight-bold"><?php if (isset($errorpseudo)) echo $errorPseudo; ?></p>


            <label for="password" class="text-white font-weight-bold">Mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg"></i>
                <input type="password" id="password" name="password" autocomplete="off" placeholder="Saisissez votre mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <p class="text-danger font-weight-bold"><?php if (isset($errorPassword)) echo $errorPassword; ?></p>


            <label for="confirmPassword" class="text-white font-weight-bold">Confirmation du mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg"></i>
                <input type="password" id="confirmPassword" name="confirmPassword" autocomplete="off" placeholder="Confirmez votre mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <p class="text-danger font-weight-bold"><?php if (isset($errorConfirmPassword)) echo $errorConfirmPassword; ?></p>
            <h3 class="text-center text-white font-weight-bold">Interrogation surprise !</h3>
            <p class="text-center text-white">Afin de continuer votre voyage, merci de bien vouloir répondre à la question suivante (pour vérifier que nos voyageurs ne soient pas des cyborgs).</p>
            <label class="text-white font-weight-bold">Question : <?php if (isset($question)) echo $question; ?></label>

            <div class="form-group position-relative">
                <i class="fas fa-check fa-lg"></i>
                <input type="text" id="captcha" name="captcha" placeholder="Saisissez votre réponse" required autocomplete="off">

            </div>
            <p class="text-danger font-weight-bold"><?php if (isset($errorCaptcha)) echo $errorCaptcha; ?></p>
            <div class="group-form">
                <div id="term&conditionCheck" class="text-white">
                    <input type="checkbox" id="termConditions" name="termCondition" autocomplete="off">
                    <label for="termConditions">J'accepte les <a href="#">Conditions Générales d'Utilisation</a> de Landtales</label>
                    <p class="text-danger font-weight-bold"><?php if (isset($errorCheck)) echo $errorCheck; ?></p>
                </div>
            </div>
            <div class="group-form">
                <div id="newsletterCheck" class="text-white">
                    <input type="checkbox" id="newsletter" name="newsletter" value="1">
                    <label for="newsletter">J'accepte de recevoir des mails de la <a href="#">Newsletter</a></label>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-login" name="valider" >Inscription</button>
            </div>
            <input type="hidden" name="captcha_answer" value="<?php echo $captchaAnswer; ?>" tabindex="-1">
</form>
        <script>
            function togglePassword() {
                var passwordField = document.getElementById("password");
                var passwordToggleBtn = document.querySelector(".toggle-password");

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
