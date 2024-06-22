<?php

require "Structure/Bdd/config.php";

if (isset($_GET['keyword']) && isset($_GET['table'])) {

    $keyword = htmlspecialchars($_GET['keyword']);
    $table = htmlspecialchars($_GET['table']);
    $suggestions = [];

    switch ($table) {
        case 'Voyage':
            $column = 'title';
            $link = 'travel.php?id=';
            // Modifiez la requête pour inclure la recherche par mot-clé
            $query = $bdd->prepare("SELECT id, title FROM travel WHERE travel_status = 1 AND visibility = 1 AND $column LIKE :keyword LIMIT 7");
            $query->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $suggestions[] = array(
                    'url' => $link . $row['id'],
                    'title' => str_replace('&#039;', "'", $row[$column])
                );
            }
            break;
        case 'Quiz':
            $column = 'title';
            $link = 'homeQuiz.php?id=';
            // Modifiez la requête pour inclure la recherche par mot-clé
            $query = $bdd->prepare("SELECT id, title FROM quiz WHERE $column LIKE :keyword LIMIT 7");
            $query->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $suggestions[] = array(
                    'url' => $link . $row['id'],
                    'title' => str_replace('&#039;', "'", $row[$column])
                );
            }
            break;
        case 'Utilisateur':
            $column = 'pseudo';
            $link = 'userProfil.php?id=';
            // Modifiez la requête pour inclure la recherche par mot-clé
            $query = $bdd->prepare("SELECT id, pseudo FROM client WHERE (visibility  = 1 AND idrank != 2 AND permaBan != 1) AND $column LIKE :keyword LIMIT 7");
            $query->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
            $query->execute();
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $suggestions[] = array(
                    'url' => $link . $row['id'],
                    'title' => str_replace('&#039;', "'", $row[$column])
                );
            }
            break;
    }

    if (empty($suggestions)) {
        echo json_encode(array('message' => 'Aucun résultat trouvé'));
    } else {
        echo json_encode($suggestions);
    }
}

?>