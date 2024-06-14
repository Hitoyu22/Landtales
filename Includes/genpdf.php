<?php
use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
require "dompdf/autoload.inc.php";

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    $pseudo = $_SESSION['pseudo'];


    require "Structure/Bdd/config.php";

    //Récupération de l'ensemble des informations de chaque table en rapport à l'utilisateur

    $data = $bdd->prepare('SELECT c.email, c.pseudo, c.lastname, c.firstname, c.idrank, c.last_login_date, c.github, c.twitter,c.insta,c.youtube,c.facebook, c.profil_picture, c.summary, c.mood, c.coin
                                    FROM client c
                                    WHERE c.id = ?
                                    GROUP BY c.email, c.pseudo, c.lastname, c.firstname, c.idrank, c.last_login_date, c.github, c.twitter,c.insta,c.youtube,c.facebook, c.profil_picture, c.summary, c.mood, c.coin;');
    $data->execute([$userId]);
    $user = $data->fetchAll(PDO::FETCH_ASSOC);

    $travelUser = $bdd->prepare('SELECT +t.title,
                                               t.travel_date as dateTravel,
                                               nbTV.nb as nbView,
                                               nbTL.nb as nbLike
                                        FROM travel t
                                        LEFT JOIN (SELECT COUNT(tv.idclient) AS nb ,tv.idtravel as id FROM travel_view tv GROUP BY tv.idtravel) nbTV ON nbTV.id = t.id
                                        LEFT JOIN (SELECT COUNT(tl.idclient) AS nb ,tl.idtravel as id FROM travel_like tl GROUP BY tl.idtravel) nbTL ON nbTL.id = t.id
                                        WHERE t.idclient = ?');
    $travelUser->execute([$userId]);
    $travels = $travelUser->fetchAll(PDO::FETCH_ASSOC);
    $numberTravel=$travelUser->RowCount();



    $quizFinish = $bdd->prepare('SELECT q.title,q.difficulty_level as diff, ca.score
                                        FROM quiz q
                                        INNER JOIN client_answer ca ON ca.idquiz = q.id
                                        WHERE ca.idclient = ? AND ca.coin_added = 1');
    $quizFinish->execute([$userId]);
    $Quiz = $quizFinish->fetchAll(PDO::FETCH_ASSOC);

    $customOwn = $bdd->prepare('SELECT cc.idclient, cu.picture_name, cc.purchase_date
                                        FROM client_customisation cc
                                        INNER JOIN customisation cu ON cu.id = cc.idcustomisation
                                        WHERE cc.idclient = ?');
    $customOwn->execute([$userId]);
    $customisations = $customOwn->fetchAll(PDO::FETCH_ASSOC);

    $friendData = $bdd->prepare('SELECT f.idclient2 AS idfriend, c.pseudo as pseudo
                                        FROM friend f
                                        INNER JOIN client c ON c.id = f.idclient2
                                        WHERE idclient1 = ?
                                        UNION
                                        SELECT idclient1 AS idfriend, c.pseudo as pseudo
                                        FROM friend f
                                        INNER JOIN client c ON c.id = f.idclient1
                                        WHERE idclient2 = ?');
    $friendData->execute([$userId, $userId]);
    $friends = $friendData->fetchAll(PDO::FETCH_ASSOC);

    ob_start();
    require "userInfoPdf.php";
    $html = ob_get_contents();
    ob_end_clean();


    $options = new Options();
    $options->set('defaultFont', 'Courier');

    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();


    $fichier = 'mon-pdf.pdf';
    $dompdf->stream($fichier);

} else {
    header("Location: login.php");
    exit();
}
?>