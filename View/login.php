<?php
$pageTitle = "Connexion";

require "Structure/Functions/function.php";
require "Structure/Functions/alerts.php";

session_start();
require "Structure/Bdd/config.php";

// Récupérer les données du captcha depuis la base de données
$captcha = $bdd->prepare("SELECT question, answer FROM captcha ORDER BY RAND() LIMIT 1");
$captcha->execute();
$captchaData = $captcha->fetch(PDO::FETCH_ASSOC);

// Vérifier si les données du captcha ont été récupérées avec succès
if ($captchaData) {
    // Stocker la réponse du captcha dans une variable de session
    $captchaAnswer = strtolower($captchaData['answer']);
    $captchaQuestion = $captchaData['question'];
} else {
    // Gérer l'erreur si les données du captcha ne peuvent pas être récupérées
    $captchaAnswer = "Erreur lors de la récupération du captcha.";
    $captchaQuestion = "Erreur lors de la récupération du captcha.";
}

$errorEmail ="";
$errorPassword ="";
$errorCaptcha ="";


if (isset($_POST['valider'])) {
    if (!empty($_POST["email"])) {
        $email = $_POST["email"];
    } else {
        $errorEmail = "Veuillez saisir votre email";
    }

    if (!empty($_POST["mdp"])) {
        $password = $_POST["mdp"];
    } else {
        $errorPassword = "Veuillez saisir votre mot de passe";
    }

    // Récupérer la réponse du captcha
    $reponseCaptchaUser = strtolower($_POST['captcha']);
    $captchaAnswerTrue = strtolower($_POST['captcha_answer']);

    // Vérifier si la réponse du captcha correspond à celle stockée en session
    similar_text(strtolower($reponseCaptchaUser), $captchaAnswerTrue, $percent);

    if ($percent < 80) {
        $errorCaptcha = "La réponse au captcha est incorrecte.";
    }

    // Stocker les données en session
    $_SESSION['form_data'] = [
        'email' => $email
    ];

    if (empty($errorEmail) && empty($errorPassword) && empty($errorCaptcha)) {
        // Vérification si l'email existe dans la base de données
        $userInfo = $bdd->prepare('SELECT id, account_verification_key, client_password, account_verificated, idrank, pseudo, permaBan, tempBan FROM client WHERE email = ?');
        $userInfo->execute(array($email));
        $userExists = $userInfo->fetch(PDO::FETCH_ASSOC);

        if ($userExists) {
            $today = date('Y-m-d');

            if ($userExists['permaBan'] == 1) {
                header("Location: login.php?permaBan=true");
                exit();
            } elseif (!is_null($userExists['tempBan']) && $userExists['tempBan'] >= $today) {
                $tempBanDate = urlencode($userExists['tempBan']);  // Encode the date properly
                header("Location: login.php?tempBan=true&date={$tempBanDate}");
                exit();
            }

            if (password_verify($password, $userExists['client_password'])) {
                if ($userExists['account_verificated'] != 1) {
                    header('Location: verification.php?idclient=' . $userExists['id'] . '&account_verification_key=' . $userExists['account_verification_key']);
                    exit();
                } else {
                    $_SESSION['idclient'] = $userExists['id'];
                    $_SESSION['pseudo'] = $userExists['pseudo'];
                    $_SESSION['rank'] = $userExists['idrank'];

                    $updateLoginDate = $bdd->prepare("UPDATE client SET last_login_date = NOW() WHERE id=?");
                    $updateLoginDate->execute([$_SESSION['idclient']]);
                    $userId = $userExists['id'];
                    $pseudo = $userExists['pseudo'];

                    if ($userExists['idrank'] == 1 || $userExists['idrank'] == 3) {
                        $logPath = "Admin/Structures/Logs/log.txt";
                        $pageAction = "S'est connecté à Landtales | Front Office";
                        $pageId = 1000;
                        $logType = "Connexion";
                        logActivity($userId, $pseudo, $pageId, $logType, $logPath);
                        header('Location: homeFront.php');
                    } elseif ($userExists['idrank'] == 2) {
                        $logPath = "Admin/Structures/Logs/log.txt";
                        $pageAction = "S'est connecté à Landtales | Back Office";
                        $pageId = 1000;
                        $logType = "Connexion";
                        logActivity($userId, $pseudo, $pageId, $logType, $logPath);
                        header('Location: Admin/homeBack.php');
                    } else {
                        echo "Erreur: rôle utilisateur non défini.";
                    }

                    // Détruire les données de session spécifiques
                    unset($_SESSION['form_data']);
                    exit();
                }
            } else {
                $errorPassword = "Le mot de passe est incorrect.";
            }
        } else {
            $errorEmail = "L'email n'existe pas.";
        }
    }
}
require "Structure/Head/head.php";

tempBan();
permaBan();
?>
<link rel="stylesheet" href="Design/Css/login.css">
</head>
<body>
<div class="container mt-0 col-md-12 col-ms-12">
    <div class="login-form">
        <h1 class="text-white font-weight-bold text-center ">Se connecter</h1>
        <p class="text-white fw-bold text-center mb-5">Vous ne possédez pas encore de compte ? <a href="register.php">Inscrivez-vous !</a></p>
        <form method="post" action="" autocomplete="off">

            <label for="email" class="text-white">Email</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-envelope fa-lg mt-3"></i>
                <input id="email" type="email" name="email" autocomplete="off" placeholder="Saisissez votre adresse mail" required maxlength="128" value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
            </div>
            <p class="text-danger"><?php echo $errorEmail; ?></p>

            <label for="password" class="text-white">Mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg mt-3"></i>
                <input type="password" id="password" name="mdp" autocomplete="off" placeholder="Saisissez votre mot de passe" required maxlength="128">
                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                    <i class="fas fa-eye mt-3"></i>
                </button>
            </div>
            <p class="text-danger"><?php echo $errorPassword; ?></p>

            <h3 class="text-center text-white font-weight-bold">Interrogation surprise !</h3>
            <p class="text-center text-white mb-3">Afin de continuer votre voyage, merci de bien vouloir répondre à la question suivante (pour vérifier que nos voyageurs ne soient pas des cyborgs).</p>
            <label class="text-white font-weight-bold">Question : <?php if (isset($captchaQuestion)) echo str_replace('&#039;', "'", $captchaQuestion); ?></label>

            <div class="form-group d-flex position-relative">
                <i class="fas fa-check fa-lg mt-3"></i>
                <input type="text" id="captcha" name="captcha" placeholder="Saisissez votre réponse" required autocomplete="off" value="<?php echo isset($_SESSION['form_data']['captcha']) ? htmlspecialchars($_SESSION['form_data']['captcha']) : ''; ?>">
            </div>
            <p class="text-danger"><?php echo $errorCaptcha; ?></p>

            <div class="text-center mb-5">
                <button type="submit" class="btn btn-primary btn-login" name="valider">Connexion</button>
            </div>
            <input type="hidden" name="captcha_answer" value="<?php echo htmlspecialchars($captchaAnswer); ?>" tabindex="-1">
        </form>
        <div class="text-center">
            <p class="text-white">Mot de passe oublié ? <a href="forgotPassword.php">Cliquez-ici</a></p>
        </div>
        <script src="Structure/Functions/bootstrap.js"></script>
        <script src="Structure/Functions/script.js"></script>
</body>
</html>
