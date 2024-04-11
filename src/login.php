<?php
$pageTitle = "Connexion";

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

if (isset($_POST['valider'])) {
    $errors = array();

    if(!empty($_POST["email"])) {
        $email = $_POST["email"];
    } else {
        $errorEmail = "Veuillez saisir votre email";
    }

    if(!empty($_POST["mdp"])) {
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

    if (empty($errorEmail) && (empty($errorCaptcha)) && (empty($errorPassword))) {
        // Vérification si l'email existe dans la base de données
        $userInfo = $bdd->prepare('SELECT id, account_verification_key, client_password, account_verificated, idrank FROM client WHERE email = ?');
        $userInfo->execute(array($email));
        $userExists = $userInfo->fetch();

        if (!$userExists) {
            $errorEmail = "L'email n'existe pas.";
        } elseif ($userExists && password_verify($password, $userExists['client_password'])) {
            if ($userExists && password_verify($password, $userExists['client_password'])) {
                if ($userExists['account_verificated'] != 1) {
                    header('Location: verification.php?idclient=' . $userExists['id'] . '&account_verification_key=' . $userExists['account_verification_key']);
                    exit();
                } else {
                    $_SESSION['idclient'] = $userExists['id'];
                    if ($userExists['idrank'] == 1) {
                        header('Location: homeFront.php');
                        exit();
                    } elseif ($userExists['idrank'] == 2) {
                        header('Location: Admin/homeBack.php');
                        exit();
                    } else {
                        echo "Erreur: rôle utilisateur non défini.";
                    }
                }
            }
        } else {
            $errorPassword = "Le mot de passe est incorrect.";
        }
    }
}
require "Structure/Head/head.php";
?>


<link rel="stylesheet" href="Design/Css/login.css">
</head>
<body>
<div class="container col-md-12 col-ms-12">
    <div class="login-form">
        <h1 class="text-white font-weight-bold text-center ">Se connecter</h1>
        <p class="text-white fw-bold text-center mb-5">Vous ne possédez pas encore de compte ? <a href="register.php">Inscrivez-vous !</a></p>
        <form method="post" action="" autocomplete="off">

            <label for="email" class="text-white">Email</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-envelope fa-lg"></i>
                <input id="email" type="email" name="email" autocomplete="off" placeholder="Saisissez votre adresse mail" required>
            </div>
            <p class="text-white"><?php if (isset($errorEmail)) echo $errorEmail; ?></p>

            <label for="password" class="text-white">Mot de passe</label>
            <div class="form-group position-relative d-flex">
                <i class="fas fa-lock fa-lg"></i>
                <input type="password" id="password" name="mdp" autocomplete="off" placeholder="Saisissez votre mot de passe" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <p class="text-white"><?php if (isset($errorPassword)) echo $errorPassword; ?></p>


            <h3 class="text-center text-white font-weight-bold">Interrogation surprise !</h3>
            <p class="text-center text-white mb-3">Afin de continuer votre voyage, merci de bien vouloir répondre à la question suivante (pour vérifier que nos voyageurs ne soient pas des cyborgs).</p>
            <label class="text-white font-weight-bold">Question : <?php if (isset($captchaQuestion)) echo $captchaQuestion; ?></label>

            <div class="form-group position-relative">
                <i class="fas fa-check fa-lg"></i>
                <input type="text" id="captcha" name="captcha" placeholder="Saisissez votre réponse" required autocomplete="off">

            </div>
            <div class="text-center mb-5">
                <button type="submit" class="btn btn-primary btn-login" name="valider" >Connexion</button>
            </div>
            <input type="hidden" name="captcha_answer" value="<?php echo $captchaAnswer; ?>" tabindex="-1">
        </form>
        <div class="text-center">
            <p class="text-white">Mot de passe oublié ? <a href="forgotPassword.php">Cliquez-ici</a></p>
        </div>

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
