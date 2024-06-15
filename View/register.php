<?php

$pageTitle = "Inscription";

require "Includes/PHPMailerAutoload.php";
require "Structure/Functions/function.php";
session_start();

require "Structure/Bdd/config.php";

$errorEmail = $errorPseudo = $errorPassword = $errorConfirmPassword = $errorCheck = $errorCaptcha = "";

// Récupérer une question et une réponse de captcha
$captcha = $bdd->prepare("SELECT question, answer FROM captcha ORDER BY RAND() LIMIT 1");
$captcha->execute();
$captchaData = $captcha->fetch(PDO::FETCH_ASSOC);
$question = $captchaData['question'];
$captchaAnswer = strtolower($captchaData['answer']);

if (isset($_POST['valider'])) {

    // Récupérer les données
    $email = $_POST['email'];
    $pseudo = $_POST['pseudo'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $captchaAnswerTrue = strtolower($_POST['captcha_answer']);
    $reponseCaptchaUser = strtolower($_POST['captcha']);
    similar_text(strtolower($reponseCaptchaUser), $captchaAnswerTrue, $percent);

    // Stocker les données en session
    $_SESSION['form_data'] = [
        'email' => $email,
        'pseudo' => $pseudo,
        'newsletter' => isset($_POST['newsletter']) ? $_POST['newsletter'] : 0,
        'termCondition' => isset($_POST['termCondition']) ? $_POST['termCondition'] : 0
    ];

    $pseudoLength = strlen($_POST['pseudo']);
    $mailLength = strlen($_POST['email']);


    $recupBans = $bdd->prepare("SELECT tempBan, permaBan FROM client WHERE email = ?");
    $recupBans->execute([$email]);
    $bans = $recupBans->fetch(PDO::FETCH_ASSOC);

    $recuperationEmail = $bdd->prepare("SELECT id FROM client WHERE email = ?");
    $recuperationEmail->execute(array($email));
    $nbEmail = $recuperationEmail->rowCount();


    $today = date('Y-m-d');

    if ($nbEmail > 0){
        if ($bans['permaBan'] == 1) {
            header("Location: register.php?permaBan=true");
            exit();
        } elseif (!is_null($bans['tempBan']) && $bans['tempBan'] >= $today) {
            $tempBanDate = urlencode($bans['tempBan']);  // Encode the date properly
            header("Location: register.php?tempBan=true&date={$tempBanDate}");
            exit();
        }
    }




    // Vérification de la conformité des données
    if (empty($email)) $errorEmail = "L'email est requis.";
    if (empty($pseudo)) $errorPseudo = "Le nom d'utilisateur est requis.";
    if (empty($password)) $errorPassword = "Le mot de passe est requis.";
    if ($pseudoLength > 64) $errorPseudo = "Votre nom d'utilisateur ne peut pas dépasser 64 caractères.";
    if ($mailLength > 128) $errorEmail = "Cette adresse mail ne peut pas dépasser 128 caractères.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errorEmail = "Cette adresse mail n'est pas valide.";
    if ($nbEmail > 0) $errorEmail = "Cette adresse mail est déjà utilisée.";
    if ($password != $confirmPassword) $errorPassword = "Les mots de passe ne correspondent pas.";
    if (!isset($_POST['termCondition'])) $errorCheck = "Vous devez accepter les règles et conditions d'utilisation pour créer un compte.";
    if ($percent < 80) $errorCaptcha = "La réponse au captcha est incorrecte.";

    if (!empty($password) && !preg_match('/^(?=.*\d)(?=.*[^\w\s])(?=.*[a-z])(?=.*[A-Z])[\w\s\D]{12,64}$/', $password)) {
        $errorPassword = "Le mot de passe doit comporter au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial et être long de minimum 12 caractères.";
    }

    // Création du profil dans la BDD si pas d'erreurs
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

        $linkhtml = 'https://landtales.freeddns.org/verification.php?idclient=' . $_SESSION['idclient'] . '&account_verification_key=' . $account_verification_key;
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
                    <h3>{$pseudo}</h3>
                    <p>Nous vous remercions de vous être inscris sur Landtales</p>
                    <p>Afin de finaliser votre inscription, il faut encore que vous fassiez vérifier votre compte en appuyant sur le bouton suivant. </p>
                    <p>Encore une fois, merci infiniment pour votre inscription. Nous sommes honorés de vous avoir parmi nous et nous espérons que vous vivrez une aventure.</p>
                    <p>Bien cordialement,</p>
                    <p>Landtales</p>
                </div>
                <div class="text-center mt-3 btnediv">
                <a href="{$linkhtml}">Vérifier votre compte</a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
HTML;

        // Envoi du mail de confirmation
        $to = $email;
        $from = 'landtales.website@gmail.com';
        $name = 'Landtales';
        $subj = 'Confirmation de compte';
        $msg = $html;
        $error = smtpmailer($to, $from, $name, $subj, $msg);

        // Suppression des données de session après envoi réussi
        unset($_SESSION['form_data']);
        header('Location: mailConfirmation.php');
    }
}

require "Structure/Head/head.php";

tempBan();
permaBan();
?>

<link rel="stylesheet" href="Design/Css/login.css">
</head>
<body>
<div class="container mt-0 mb-0 col-md-12 col-ms-12">
    <div class="login-form">
        <h1 class="text-white font-weight-bold text-center ">S'inscrire</h1>
        <p class="text-white fw-bold text-center mb-3">Vous possédez déjà un compte Landtales ? <a href="login.php">Connectez-vous !</a></p>
        <form method="post" action="" autocomplete="off">

            <label for="email" class="text-white font-weight-bold">Email</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-envelope fa-lg mt-3"></i>
                <input id="email" type="email" name="email" autocomplete="off" placeholder="Saisissez votre adresse mail" required maxlength="128" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorEmail; ?></p>

            <label for="pseudo" class="text-white font-weight-bold">Nom d'utilisateur</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-user fa-lg mt-3"></i>
                <input id="pseudo" type="text" name="pseudo" autocomplete="off" placeholder="Saisissez votre nom d'utilisateur" required maxlength="64" value="<?php echo isset($_SESSION['form_data']['pseudo']) ? htmlspecialchars($_SESSION['form_data']['pseudo']) : ''; ?>">
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorPseudo; ?></p>

            <label for="password" class="text-white font-weight-bold">Mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg mt-3"></i>
                <input type="password" id="password" name="password" autocomplete="off" placeholder="Saisissez votre mot de passe" required maxlength="64">
                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                    <i class="fas fa-eye mt-3"></i>
                </button>
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorPassword; ?></p>

            <label for="confirmPassword" class="text-white font-weight-bold">Confirmation du mot de passe</label>
            <div class="form-group position-relative d-flex mb-0">
                <i class="fas fa-lock fa-lg mt-3"></i>
                <input type="password" id="confirmPassword" name="confirmPassword" autocomplete="off" placeholder="Confirmez votre mot de passe" required maxlength="64">
                <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                    <i class="fas fa-eye mt-3"></i>
                </button>
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorConfirmPassword; ?></p>

            <h3 class="text-center text-white font-weight-bold">Interrogation surprise !</h3>
            <label class="text-white font-weight-bold">Question : <?php echo html_entity_decode($question); ?></label>
            <div class="form-group d-flex position-relative">
                <i class="fas fa-check fa-lg mt-3"></i>
                <input type="text" id="captcha" name="captcha" placeholder="Saisissez votre réponse" required autocomplete="off">
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorCaptcha; ?></p>

            <div class="group-form mb-3">
                <div id="term&conditionCheck" class="text-white">
                    <div class="d-flex align-items-center flex-wrap">
                        <input type="checkbox" id="termConditions" name="termCondition" autocomplete="off" class="me-2" <?php echo isset($_SESSION['form_data']['termCondition']) && $_SESSION['form_data']['termCondition'] ? 'checked' : ''; ?>>
                        <label for="termConditions">J'accepte les <a target="_blank" href="Structure/Pdf/legaleMention.pdf">Conditions Générales d'Utilisation</a> de Landtales</label>
                    </div>
                    <p class="text-danger font-weight-bold"><?php echo $errorCheck; ?></p>
                </div>
            </div>

            <div class="group-form mb-3">
                <div id="newsletterCheck" class="text-white">
                    <div class="d-flex align-items-center flex-wrap">
                        <input type="checkbox" id="newsletter" name="newsletter" value="1" class="me-2" <?php echo isset($_SESSION['form_data']['newsletter']) && $_SESSION['form_data']['newsletter'] ? 'checked' : ''; ?>>
                        <label for="newsletter">J'accepte de recevoir des mails de la <a target="_blank" href="Structure/Pdf/newsletter.pdf">Newsletter</a></label>
                    </div>
                </div>
            </div>


            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-login" name="valider">Inscription</button>
            </div>

            <input type="hidden" name="captcha_answer" value="<?php echo $captchaAnswer; ?>" tabindex="-1">
        </form>
    </div>
</div>

<script>
    function togglePassword(targetId) {
        var passwordField = document.getElementById(targetId);
        var passwordToggleBtn = passwordField.nextElementSibling;

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
