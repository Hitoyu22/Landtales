<?php
session_start();

require "Structure/Functions/function.php";

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];
    checkUserRole();
    require "Structure/Bdd/config.php";

    $pageTitle = "Boutique des customisations";

    $logPath = "Admin/Structures/Logs/log.txt";
    $pageAction = "Visite de la boutique des customisations";
    $pageId = 8;
    $logType = "Visite";

    logActivity($userId, $pseudo, $pageId, $logType, $logPath);

    // Récupération du solde de coins de l'utilisateur
    $userCoins = $bdd->prepare("SELECT coin FROM client WHERE id = ?");
    $userCoins->execute([$userId]);
    $coins = $userCoins->fetchColumn();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idCustom = isset($_POST['customId']) ? $_POST['customId'] : null;

        if (isset($_POST['buy']) && $idCustom) {
            processCustomisationPurchase($userId, $idCustom, $_POST['cost'], $coins);

            $logPath = "Admin/Structures/Logs/log.txt";
            $pageAction = "Achat d'une customisation";
            $pageId = 8;
            $logType = "Achat d'une customisation";

            logActivity($userId, $pseudo, $pageId, $logType, $logPath);
        }

        if (isset($_POST['applyPromo'])) {
            $promoCode = $_POST['promoCode'];

            // Vérification de l'existence du code promo
            $promoQuery = $bdd->prepare("SELECT id,picture FROM customisation WHERE promo_code = ? AND end_date_code >= NOW()");
            $promoQuery->execute([$promoCode]);
            $promo = $promoQuery->fetch();

            if ($promo) {
                $idCustom = $promo['id']; // Défini ici pour utilisation dans le processus d'achat
                processCustomisationPurchase($userId, $idCustom, 0, $coins, $promo['picture']);

                $logPath = "Admin/Structures/Logs/log.txt";
                $pageAction = "Achat d'une customisation";
                $pageId = 8;
                $logType = "Achat d'une customisation";

                logActivity($userId, $pseudo, $pageId, $logType, $logPath);
            } else {
                header("Location: {$_SERVER['REQUEST_URI']}?promo=error");
                exit();
            }
        }
    }

    $promoStatus = isset($_GET['promo']) ? $_GET['promo'] : '';
    $imageUrl = isset($_GET['image']) ? urldecode($_GET['image']) : '';

    require "Structure/Head/head.php";
} else {
    header("Location: login.php");
    exit();
}
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

    <div class="main mt-5">
        <div id="banner-shop" class="col-12 mt-4 banner-container">
            <div class="banner-text">
                <h1 class="text-center">Bienvenue dans la boutique Landtales</h1>
                <h5 class="text-center text-white">Dépensez vos coins durement obtenus en achetant des customisations de votre profil</h5>
            </div>
            <img alt="Bannière de la boutique" src="https://wallpapers.com/images/featured/pirate-ship-v7qo6pj8pn3gvdfn.jpg" class="banner-user-img mb-4">
        </div>



        <div class="container mt-5">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <span id="userCoinsDisplay">Nombre de pièce : <?php echo $coins . ' ' . ($coins > 1 ? 'pièces' : 'pièce'); ?></span>
                </div>
                <div class="col-lg-6 col-md-12">
                    <form method="post" action="" class="promo-form">
                        <label for="promoCode">Code promo</label>
                        <div class="d-flex">
                            <input id="promoCode" type="text" name="promoCode" placeholder="Saisissez un code promo" required class="form-control" maxlength="20">
                            <button type="submit" name="applyPromo" class="btn btn-landtales ml-2">Appliquer</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php
            $allCustom = $bdd->prepare("SELECT id,picture,price FROM customisation WHERE promo_code IS NULL AND end_date_code IS NULL");
            $allCustom->execute();
            $customs = $allCustom->fetchAll();

            echo '<div class="row">';
            foreach ($customs as $custom) {
                $checkPurchase = $bdd->prepare("SELECT 1 FROM client_customisation WHERE idcustomisation = ? AND idclient = ?");
                $checkPurchase->execute([$custom['id'], $userId]);
                $isPurchased = $checkPurchase->fetchColumn();

                echo '<div class="col-12 col-md-2 mb-4">';
                echo '<div class="custom-card p-3 d-flex flex-column align-items-center">';
                echo '<div class="image-container mb-3" style="width: 100%; aspect-ratio: 1 / 1; overflow: hidden;">';
                echo '<img src="' . str_replace('&#039;', "'", $custom['picture']) . '" alt="Customisation Image" style="width: 100%; height: auto;">';
                echo '</div>';
                echo '<p class="text-center">Prix : '. $custom['price'] .'</p>';

                if ($isPurchased) {
                    echo '<button class="btn btn-success" disabled>Acheté ✅</button>';
                } elseif ($coins >= $custom['price']) {
                    echo '<form method="post" action="">';
                    echo '<input type="hidden" name="customId" value="' . $custom['id'] . '">';
                    echo '<input type="hidden" name="cost" value="' . $custom['price'] . '">';
                    echo '<button type="submit" name="buy" class="btn btn-primary">Acheter</button>';
                    echo '</form>';
                } else {
                    echo '<button class="btn btn-secondary" disabled>Solde insuffisant</button>';
                }
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            ?>
        </div>

        <div class="modal" id="promoSuccessModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Succès</h5>
                    </div>
                    <div class="modal-body">
                        Félicitations, voici la customisation que vous avez obtenu !
                        <?php if ($promoStatus === 'success' && $imageUrl): ?>
                            <div><img src="<?php echo str_replace('&#039;', "'", $imageUrl); ?>" alt="Customisation Image" style="max-width: 100%;"></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="promoErrorModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Erreur</h5>
                    </div>
                    <div class="modal-body">
                        <?php if ($promoStatus === 'already_purchased'): ?>
                            Vous possédez déjà cette customisation.
                        <?php else: ?>
                            Code promo invalide ou expiré.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var promoStatusElement = "<?php echo $promoStatus; ?>";
        if (promoStatusElement) {
            showPromoModal(promoStatusElement);
        }
    });
</script>
</body>
</html>
