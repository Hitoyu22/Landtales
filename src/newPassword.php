<?php
session_start();
$pageTitle = "Nouveau mot de passe";

require "Structure/Bdd/config.php";


$errorPassword = $errorConfirmPassword = $errorCaptcha = '';


$captcha = $bdd->prepare("SELECT question, answer FROM captcha ORDER BY RAND() LIMIT 1");
$captcha->execute();
$captchaData = $captcha->fetch(PDO::FETCH_ASSOC);


if ($captchaData) {

    $_SESSION['captcha_answer'] = strtolower($captchaData['answer']);
    $captchaQuestion = $captchaData['question'];
} else {

    $_SESSION['captcha_answer'] = "Erreur lors de la récupération du captcha.";
    $captchaQuestion = "Erreur lors de la récupération du captcha.";
}

if (isset($_GET['iduser']) && !empty($_GET['iduser']) && isset($_GET['cle_verif_account']) && !empty($_GET['cle_verif_account'])) {
    $getId = $_GET['iduser'];
    $getCle = $_GET['cle_verif_account'];
    $recupUser = $bdd->prepare('SELECT * FROM client WHERE id = ? AND account_verification_key = ?');
    $recupUser->execute(array($getId, $getCle));

    if ($recupUser->rowCount() > 0) {
        if (empty($_POST['password']) || empty($_POST['confirmPassword'])) {
            $errorPassword = "Le mot de passe est requis.";
        } else {
            $newPassword = $_POST['password'];
            $confirmNewPassword = $_POST['confirmPassword'];

            if ($newPassword != $confirmNewPassword) {
                $errorConfirmPassword = "Les mots de passe ne correspondent pas.";
            } elseif (!preg_match('/^(?=.*\d)(?=.*[&\-é_èçà^ù*:!ù#~@°%§+.])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z&\-é_èçà^ù*:!ù#~@°%§+.]{12,64}$/', $newPassword)) {
                $errorPassword = "Le mot de passe doit comporter au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial de à @ é è & ç ù _ ! . + - : # % § ^ * ~ °.";
            } else {

                if (isset($_POST['captcha'])) {
                    $reponseCaptchaUser = strtolower($_POST['captcha']);
                    if ($_SESSION['captcha_answer'] !== $reponseCaptchaUser) {
                        $errorCaptcha = "La réponse au captcha est incorrecte.";
                    }
                } else {
                    $errorCaptcha = "La réponse au captcha est requise.";
                }

                if (empty($errorPassword) && empty($errorCaptcha)) {

                    $updatePassword = $bdd->prepare('UPDATE client SET client_password = ? WHERE id = ?');
                    $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updatePassword->execute(array($newPassword, $getId));

                    $_SESSION['id'] = $getId;

                    header('Location: passwordChangeConfirmation.php');
                    exit();
                }
            }
        }
    } else {

        $errorGeneral = "Votre clé ou identifiant est incorrect.";
    }
}

require "Structure/Head/head.php";
?>

    <link rel="stylesheet" href="Design/Css/login.css">
    <title>Inscription</title>
</head>
<body>
<div class="container col-md-12 col-ms-12">
    <div class="login-form">
        <h1 class="text-white font-weight-bold text-center ">Nouveau mot de passe</h1>
        <h4 class="text-white text-center">Il fallait bien que ça arrive aux meilleurs.<br>Saisissez votre nouveau mot de passe.</h4>
        <form method="POST" action="">
            <label for="password" class="text-white font-weight-bold">Mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg"></i>
                <input type="password" id="password" name="password" autocomplete="off" placeholder="Saisissez votre mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorPassword; ?></p>

            <label for="confirmPassword" class="text-white font-weight-bold">Confirmation du mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg"></i>
                <input type="password" id="confirmPassword" name="confirmPassword" autocomplete="off" placeholder="Confirmez votre mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorConfirmPassword; ?></p>

            <input type="hidden" name="captcha_answer" value="<?php echo $_SESSION['captcha_answer']; ?>" tabindex="-1">
            <h3 class="text-center text-white font-weight-bold">Interrogation surprise !</h3>
            <p class="text-center text-white">Afin de continuer votre voyage, merci de bien vouloir répondre à la question suivante (pour vérifier que nos voyageurs ne soient pas des cyborgs).</p>
            <label class="text-white font-weight-bold">Question : <?php echo $captchaQuestion; ?></label>

            <div class="form-group position-relative">
                <i class="fas fa-check fa-lg"></i>
                <input type="text" id="captcha" name="captcha" placeholder="Saisissez votre réponse" required autocomplete="off">
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorCaptcha; ?></p>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-login" name="valider">Valider le nouveau mot de passe</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>