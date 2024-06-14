<?php
require "Structure/Bdd/config.php";
require "Structure/Functions/function.php";

if (isset($_GET['commentId'])) {
    $commentId = $_GET['commentId'];

    // Récupération des données ajoutées en GET
    $idSession = isset($_GET['idSession']) ? $_GET['idSession'] : null;
    $idCreator = isset($_GET['idCreator']) ? $_GET['idCreator'] : null;
    $rank = isset($_GET['rank']) ? $_GET['rank'] : null;

    $getRepliesQuery = $bdd->prepare("SELECT tc.id, tc.comment, c.pseudo, c.profil_picture , tc.travel_comment_date as dateComment FROM travel_comment tc INNER JOIN client c ON c.id = tc.idclient WHERE tc.idcomment = ?");
    $getRepliesQuery->execute([$commentId]);
    $replies = $getRepliesQuery->fetchAll(PDO::FETCH_ASSOC);

    if ($replies) {
        foreach($replies as $reply) {

            $dateReply = $reply['dateComment'];
            $date_formattee_reply = formatFrenchDate($dateReply);

            $iconUser = isset($reply['profil_picture']) ? $reply['profil_picture'] : "http://localhost/src/Design/Pictures/banner_base.png";

            echo "<div class='reply col-12 d-flex align-items-start mb-3'>";
            echo "<div class='d-flex'>";
            echo "<div class='d-flex mb-2 mx-3'>";
            echo "<img src='" . $iconUser . "' alt='Photo de profil' class='rounded-circle ml-3' width='60' height='60'>";
            echo "</div>";
            echo "<div class='col-12 ml-3'>";
            echo "<p class='col-12 ml-5 mt-2'>" . $reply['pseudo'] . " - " . $date_formattee_reply . "</p>";
            echo "<div class='col-10'>";
            echo "<p>" . html_entity_decode($reply['comment']) . "</p>";
            echo "</div>";

            if ($idSession == $idCreator || $rank == 3) {
                echo "<form method='post' action='' class='delete-form'>";
                echo "<input type='hidden' name='idCommentSecond' value='" . $reply['id'] . "'>"; // Utilisation de l'ID du commentaire actuel
                echo "<button type='submit' name='deleteCommentSecond' class='btn btn-danger mt-2 rounded-pill'>Supprimer</button>";
                echo "</form>";
            }

            echo "<div id='replies" . $reply['id'] . "' class='replies d-none'></div>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "<p>Aucune réponse disponible.</p>";
    }
}
?>
