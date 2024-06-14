<?php
session_start();
$pageTitle = "Nouveau mot de passe";

require "Structure/Bdd/config.php";

$errorPassword = $errorConfirmPassword = $errorCaptcha = $errorGeneral = '';


// Récupération des données du captcha depuis la base de données
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

// Vérification des paramètres d'URL pour l'identifiant de l'utilisateur et la clé de vérification
if (isset($_GET['iduser']) && !empty($_GET['iduser']) && isset($_GET['account_verification_key']) && !empty($_GET['account_verification_key'])) {
    $getId = $_GET['iduser'];
    $getCle = $_GET['account_verification_key'];


    // Récupération des informations de l'utilisateur pour vérifier la clé
    $recupUser = $bdd->prepare('SELECT id FROM client WHERE id = ? AND account_verification_key = ?');
    $recupUser->execute(array($getId, $getCle));


    if ($recupUser->rowCount() > 0) {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {


            if (empty($_POST['password']) || empty($_POST['confirmPassword'])) {
                $errorPassword = "Le mot de passe est requis.";

            } else {
                $newPassword = $_POST['password'];
                $confirmNewPassword = $_POST['confirmPassword'];

                if ($newPassword != $confirmNewPassword) {
                    $errorConfirmPassword = "Les mots de passe ne correspondent pas.";
                } else {
                    // Vérification du captcha
                    $captchaAnswerTrue = strtolower($_SESSION['captcha_answer']);
                    $reponseCaptchaUser = strtolower($_POST['captcha']);
                    similar_text($reponseCaptchaUser, $captchaAnswerTrue, $percent);

                    if ($percent < 80) {
                        $errorCaptcha = "La réponse au captcha est incorrecte.";
                    }

                    if (empty($errorPassword) && empty($errorConfirmPassword) && empty($errorCaptcha)) {
                        // Mise à jour du mot de passe dans la base de données
                        $updatePassword = $bdd->prepare('UPDATE client SET client_password = ? WHERE id = ?');
                        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $updatePassword->execute(array($newPassword, $getId));


                        header('Location: passwordChangeConfirmation.php');
                        exit();
                    }
                }
            }
        }
    } else {
        $errorGeneral = "Votre clé ou identifiant est incorrect.";

    }
} else {
    header("Location: Error/Erreur404.php");
}

require "tructure/Head/head.php";
?>

<link rel="stylesheet" href="Design/Css/login.css">
</head>
<body>
<div class="container col-md-12 col-ms-12">
    <div class="login-form">
        <h1 class="text-white font-weight-bold text-center">Nouveau mot de passe</h1>
        <h4 class="text-white text-center">Saisissez votre nouveau mot de passe.</h4>
        <p class="text-danger"><?php echo $errorGeneral;?></p>
        <form method="POST" action="">
            <label for="password" class="text-white font-weight-bold">Mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg"></i>
                <input type="password" id="password" name="password" autocomplete="off" placeholder="Saisissez votre mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorPassword; ?></p>

            <label for="confirmPassword" class="text-white font-weight-bold">Confirmation du mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg"></i>
                <input type="password" id="confirmPassword" name="confirmPassword" autocomplete="off" placeholder="Confirmez votre mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword('confirmPassword')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <p class="text-danger font-weight-bold"><?php echo $errorConfirmPassword; ?></p>

            <input type="hidden" name="captcha_answer" value="<?php echo $_SESSION['captcha_answer']; ?>" tabindex="-1">
            <h3 class="text-center text-white font-weight-bold">Interrogation surprise !</h3>
            <label class="text-white font-weight-bold">Question : <?php echo $captchaQuestion; ?></label>

            <div class="form-group d-flex position-relative">
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
</body>
</html>