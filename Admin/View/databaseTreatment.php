<?php
require "Structure/Bdd/config.php";
require "Structure/Functions/function.php";

if (isset($_GET['id']) && isset($_GET['table'])) {
    $id = $_GET['id'];
    $table = htmlspecialchars($_GET['table']);

    switch ($table) {
        case 'captcha':
            $query = $bdd->prepare('SELECT question, answer FROM captcha WHERE id = ?');
            $query->execute([intval($id)]);
            break;
        case 'theme':
            $query = $bdd->prepare('SELECT theme_name FROM travel_theme WHERE id = ?');
            $query->execute([intval($id)]);
            break;
        case 'newsletter':
            $query = $bdd->prepare('SELECT html FROM newsletter WHERE id = ?');
            $query->execute([intval($id)]);
            break;
        case 'userRight':
            $query = $bdd->prepare('SELECT pseudo, idrank, tempBan FROM client WHERE id = ? AND permaBan != 1');
            $query->execute([intval($id)]);
            break;
        case 'client':
            $query = $bdd->prepare('SELECT pseudo, firstname, lastname, email, coin, mood, insta, twitter, facebook, github, youtube, summary FROM client WHERE id = ?');
            $query->execute([intval($id)]);
            break;
        case 'travel':
            $query = $bdd->prepare('SELECT travel_date, summary, title, idtheme FROM travel WHERE id = ?');
            $query->execute([intval($id)]);
            break;
        case 'quiz':
            $query = $bdd->prepare('SELECT title, potential_gain, universe, summary FROM quiz WHERE id = ?');
            $query->execute([intval($id)]);
            break;
        case 'friend':
            list($idclient1, $idclient2) = explode('_', $id);
            $query = $bdd->prepare('SELECT idclient1,idclient2,accepted FROM friend WHERE idclient1 = ? AND idclient2 = ? OR idclient1 = ? AND idclient2 = ?');
            $query->execute([intval($idclient1), intval($idclient2), intval($idclient2), intval($idclient1)]);
            break;
        case 'travel_like':
            list($idclient, $idtravel) = explode('_', $id);
            $query = $bdd->prepare('SELECT idtravel,idclient FROM travel_like WHERE idclient = ? AND idtravel = ?');
            $query->execute([intval($idclient), intval($idtravel)]);
            break;
        case 'travel_view':
            list($idtravel, $idclient, $travel_view_date) = explode('_', $id);
            $query = $bdd->prepare('SELECT idclient,idtravel,travel_view_date FROM travel_view WHERE idtravel = ? AND idclient = ? AND travel_view_date = ?');
            $query->execute([intval($idtravel), intval($idclient), $travel_view_date]);
            break;
        default:
            echo json_encode(['error' => 'Invalid table']);
            exit;
    }

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $result = decodeHtmlEntities($result);
        echo json_encode($result);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode(['error' => 'Missing id or table parameter']);
}
?>
