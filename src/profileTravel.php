<?php
session_start();

$pageTitle = "Paramètres - Vos voyages";

require "Structure/Functions/function.php";

if(isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    require "Structure/Bdd/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {

        $travelIdToDelete = $_POST['travel_id'];
        $deleteView = $bdd->prepare('DELETE FROM travel_view WHERE idtravel = ?');
        $deleteView->execute([$travelIdToDelete]);
        $deleteLike = $bdd->prepare('DELETE FROM travel_like WHERE idtravel = ?');
        $deleteLike->execute([$travelIdToDelete]);
        $deleteComment = $bdd->prepare('DELETE FROM travel_comment WHERE idtravel = ?');
        $deleteComment->execute([$travelIdToDelete]);
        $deleteQuery = $bdd->prepare('DELETE FROM travel WHERE id = ?');
        $deleteQuery->execute([$travelIdToDelete]);


        header("Location: profileTravel.php");
        exit();
    }

    $userInfo = $bdd->prepare('SELECT * FROM travel WHERE idclient = ?');
    $userInfo->execute(array($userId));
    $userTravels = $userInfo->fetchAll();

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

            <nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center bg-transparent" style="z-index: 100;"> <!-- Définissez un z-index inférieur -->
                <div class="container-fluid">
                    <ul class="navbar-nav" style="margin: 0 auto;">
                        <li class="nav-item">
                            <a class="nav-link" href="profileSettings.php">Général</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileCustom.php">Modifier le profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profileTravel.php"><u>Vos voyages</u></a>
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

            <div>
                <?php
                if ($userTravels) {

                    foreach ($userTravels as $travel) {

                        if (isset($travel['id'])) {
                            $travelId = $travel['id'];
                        } else {
                            $travelId = '';
                        }

                        if (isset($travel['title'])) {
                            $travelTitle = $travel['title'];
                        } else {
                            $travelTitle = '';
                        }

                        if (isset($travel['miniature'])) {
                            $travelMiniature = $travel['miniature'];
                        } else {
                            $travelMiniature = '';
                        }

                        if (isset($travel['travel_date'])) {
                            $travelDate = $travel['travel_date'];
                        } else {
                            $travelDate = '';
                        }


                        // Requête pour compter le nombre de vues pour ce voyage
                        $viewCountQuery = $bdd->prepare('SELECT COUNT(*) AS view_count FROM travel_view WHERE idtravel = ?');
                        $viewCountQuery->execute([$travelId]);
                        $viewCountResult = $viewCountQuery->fetch();
                        $viewNumber = $viewCountResult['view_count'];

                        // Requête pour compter le nombre de likes pour ce voyage
                        $likeCountQuery = $bdd->prepare('SELECT COUNT(*) AS like_count FROM travel_like WHERE idtravel = ?');
                        $likeCountQuery->execute([$travelId]);
                        $likeCountResult = $likeCountQuery->fetch();
                        $likeNumber = $likeCountResult['like_count'];

                        $dateFrench = formatFrenchDate($travelDate);

                        echo '<div class="col pb-2">';
                        echo '<div class="card">';
                        echo '<div class="row g-0">';
                        echo '<div class="col-md-3 miniature-container">';
                        echo '<img src="' . $travelMiniature . '" class="card-img-top" alt="Miniature">';
                        echo '</div>';
                        echo '<div class="col-md-9">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title pb-0">' . $travelTitle . '</h5>';
                        echo '<p class="card-text pb-0">Date de création : ' . $dateFrench . '</p>';
                        echo '<p class="card-text pb-0">' . ($viewNumber > 1 ? $viewNumber . " vues" : $viewNumber . " vue") . ' - ' . ($likeNumber > 1 ? $likeNumber . " j'aimes" : $likeNumber . " j'aime") . '</p>';
                        echo '<div class="btn-group" role="group" aria-label="Actions">';
                        echo '<a href="modifyTravel.php?id=' . $travelId . '" class="btn btn-primary">Modifier le voyage</a>';
                        echo '<button type="button" class="btn btn-danger delete-btn" data-travel-id="' . $travelId . '" data-bs-toggle="modal" data-bs-target="#deleteModal">Supprimer le voyage</button>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';

                    }
                } else {
                    echo "Aucun voyage trouvé pour cet utilisateur.";

                    echo '<a href="createTravelfirst.php">Créer un voyage</a>';
                    exit();
                }

                ?>
            </div>
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Êtes-vous sûr de vouloir supprimer ce voyage ?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                            <form method="post" action="">
                                <input type="hidden" name="travel_id" id="travelIdToDelete">
                                <button type="submit" name="delete" class="btn btn-danger">Oui</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <?php require "Structure/Footer/footer.php";?>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const travelId = this.getAttribute('data-travel-id');
                document.getElementById('travelIdToDelete').value = travelId;
            });
        });
    });
</script>
<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>

</html>
