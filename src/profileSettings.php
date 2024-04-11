<?php
session_start();

$pageTitle = "Paramètres - Vos voyages";

require "Structure/Functions/function.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    require "Structure/Bdd/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer les autres champs du formulaire
        $nom = htmlspecialchars($_POST['name']);
        $prenom = htmlspecialchars($_POST['firstname']);
        $pseudo = htmlspecialchars($_POST['pseudo']);
        $humeur = htmlspecialchars($_POST['humeur']);

        $newsletter = isset($_POST['newsletter']) ? 1 : 0;

        $userInfo = $bdd->prepare('UPDATE client SET lastname = ?, firstname = ?, pseudo = ?, news_letter_accepted = ?, mood = ? WHERE id = ?');
        $userInfo->execute([$nom, $prenom,$pseudo,$newsletter,$humeur, $userId]);
        $userData = $userInfo->fetch();

        header("Location: {$_SERVER['REQUEST_URI']}");
    }


    $userInfo = $bdd->prepare('SELECT pseudo, lastname, firstname, news_letter_accepted, mood FROM client WHERE id = ?');
    $userInfo->execute([$userId]);
    $userData = $userInfo->fetch();

    $pseudo = $userData['pseudo'];
    $nom = $userData['lastname'];
    $prenom = $userData['firstname'];
    $humeur = $userData['mood'];
    $newsletter = isset($userData['news_letter_accepted']) ? $userData['news_letter_accepted'] : '';



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
                            <a class="nav-link" href="profileSettings.php"><u>Général</u></a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileCustom.php">Modifier le profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileTravel.php">Vos voyages</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileConfidentiality.php">Confidentialité</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileReporting.php">Signalement</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="col-12">
                <h2>Modifier vos informations</h2>
                <form id="editor-form" method="post" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="form-group mb-4 col-md-6">
                            <label for="name" class="col-12">Modifier votre nom</label>
                            <input id="name" class="form-control" name="name" placeholder="Modifier votre nom" type="text" value="<?php echo $nom; ?>">
                        </div>
                        <div class="form-group mb-4 col-md-6">
                            <label for="firstname" class="col-12">Modifier votre prénom</label>
                            <input id="firstname" class="form-control" name="firstname" placeholder="Modifier votre prénom" type="text" value="<?php echo $prenom; ?>">
                        </div>
                    </div>
                    <div class="form-group mb-4 col-md-6">
                        <label for="pseudo" class="col-12">Modifier votre pseudo</label>
                        <input id="pseudo" class="form-control" name="pseudo" placeholder="Modifier votre pseudo" type="text" value="<?php echo $pseudo; ?>">
                    </div>
                    <div class="form-group mb-4 col-md-6">
                        <label for="humeur" class="col-12">Modifier votre humeur</label>
                        <input id="humeur" class="form-control" name="humeur" placeholder="Modifier votre humeur" type="text" value="<?php echo $humeur; ?>">
                    </div>
                    <div class="form-check mb-4">
                        <h3 class="px-0">La newsletter</h3>
                        <input id="newsletter" class="form-check-input" name="newsletter" type="checkbox" <?php echo ($newsletter == 1) ? 'checked' : ''; ?>>
                        <label for="newsletter" class="col-12 form-check-label">J'accepte de recevoir des promotions et informations de Landtales par mail</label>
                    </div>
                    <button type="submit" id="publish" class="btn-landtales mb-5" name="publish">Mettre à jour mes informations</button>
                </form>
                <h3>Supprimer votre compte</h3>
                <p>En supprimant votre compte, toutes les informations relatives à vous (voyages, messages privés, commentaires) seront supprimées. Vos customisations obtenues dans la boutique ne seront plus récupérables.</p>
                <button class="btn btn-danger mb-5" data-bs-toggle="modal" data-bs-target="#confirmationModal">Supprimer mon compte</button>

                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="confirmationModalLabel">Confirmation de suppression</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="" method="post">
                                <div class="modal-body">
                                    Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.

                                    <label for="Password">Veuillez saisir votre mot de passe</label>

                                    <input type="password" id="Password" class="form-control" name="password" placeholder="Saisisez votre mot de passe">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                    <button type="submit" class="btn btn-danger">Oui, supprimer mon compte</button>
                                </div>
                                </form>
                            </div>
                        </div>

                </div>


            </div>
        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>
</div>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>