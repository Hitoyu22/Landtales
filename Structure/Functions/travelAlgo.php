<?php

function recommendationAlgorithm($bdd, $idclient){

    $travels = [];

    $poids = [
        "mostViewAllTime" => 20,
        "mostViewAllYear" => 20+15,
        "mostViewAllMonth" => 35+10,
        "mostViewAllWeek" => 45+10,
        "fromPopularClient" => 55+25,
        "fromNonPopularClient" => 80+20,

        "fromFollowedClient" => 40,
        "fromFriend" => 40+15,
        "recentfromPopularClient" => 55+15,
        "recentWithSimilarThemes" => 70+30
    ];


    $query = $bdd->prepare('SELECT COUNT(idclient) AS view_count FROM travel_view WHERE idclient = ?');
    $query->execute([$idclient]);
    $progress = $query->fetch(PDO::FETCH_ASSOC);


    function newTravelArray($array){
        $maxelement = count($array);
        $rnd = rand(0,$maxelement-1);
        $travel = $array[$rnd];
        $array[$rnd] = array_pop($array);
        return ['arrayTravels' => $array, 'travel' => $travel];
    }



    function exclusion($excludedTravels)
    {
        if (empty($tableau)) {
            return "('')";
        }
        $str = "(".implode(', ', $excludedTravels).")";
        return $str;
    }


    $SQLcommand = [
        'getTravelsFromNonPopularClients' =>
            "WITH eligible_clients AS (
    SELECT id
    FROM client
    WHERE (SELECT COUNT(id) FROM travel WHERE idclient = client.id AND visibility = 1) > 0
),
clients_with_follower_count AS (
    SELECT id,
           (SELECT COUNT(idclientfollowed) FROM follower WHERE idclientfollowed = client.id) AS follower_count
    FROM eligible_clients AS client
),
sorted_clients AS (
    SELECT id
    FROM clients_with_follower_count
    ORDER BY follower_count ASC
    LIMIT 40
),
selected_travels AS (
    SELECT id AS idtravel, idclient,
           ROW_NUMBER() OVER (PARTITION BY idclient ORDER BY RAND()) AS rn,
           COUNT(id) OVER (PARTITION BY idclient) AS total_travels
    FROM travel
    WHERE idclient IN (SELECT id FROM sorted_clients) AND visibility = 1
)
SELECT idtravel
FROM selected_travels
WHERE rn <= ROUND(total_travels * 0.2)
ORDER BY rn",
        'getTravelsFromPopularClients' =>
            "WITH eligible_clients AS (
    SELECT id
    FROM client
    WHERE (SELECT COUNT(id) FROM travel WHERE idclient = client.id AND visibility = 1) > 0
),
sorted_clients AS (
    SELECT id
    FROM eligible_clients AS client
    ORDER BY (SELECT COUNT(idclientfollowed) FROM follower WHERE idclientfollowed = client.id) DESC
    LIMIT 40
),
client_travels AS (
    SELECT t.id as idtravel, t.idclient,
           ROW_NUMBER() OVER (PARTITION BY t.idclient ORDER BY RAND()) AS rn,
           COUNT(t.id) OVER (PARTITION BY t.idclient) AS total_travels
    FROM travel t
    JOIN sorted_clients c ON t.idclient = c.id
    WHERE t.visibility = 1
)
SELECT idtravel
FROM client_travels
WHERE rn <= ROUND(total_travels * 0.2)
ORDER BY rn",
        'getMostViewedTravelsOfWeek' =>
            "WITH eligible_travel AS (
    SELECT id
    FROM travel
    WHERE visibility != 0 AND travel_status = 1 AND DATEDIFF(NOW(), travel_date) <= 7
),
travel_count AS (
    SELECT idtravel, COUNT(idtravel) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM eligible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM travel_count
ORDER BY cnt DESC
LIMIT 40",
        'getMostViewedTravelsOfMonth' =>
            "WITH eligible_travel AS (
    SELECT id
    FROM travel
    WHERE visibility != 0 AND travel_status = 1 AND DATEDIFF(NOW(), travel_date) <= 30
),
travel_count AS (
    SELECT idtravel, COUNT(idtravel) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM eligible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM travel_count
ORDER BY cnt DESC
LIMIT 40",
        'getMostViewedTravelsOfYear' =>
            "WITH eligible_travel AS (
    SELECT id
    FROM travel
    WHERE visibility != 0 AND travel_status = 1 AND DATEDIFF(NOW(), travel_date) <= 365
),
travel_count AS (
    SELECT idtravel, COUNT(idtravel) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM eligible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM travel_count
ORDER BY cnt DESC
LIMIT 40",
        'getMostViewedTravelsOfAllTime'=>
            "WITH visible_travel AS (
    SELECT id
    FROM travel
    WHERE visibility = 1 AND travel_status = 1
),
visible_travel_count AS (
    SELECT idtravel, COUNT(idtravel) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM visible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM visible_travel_count
ORDER BY cnt
LIMIT 40",
        'getTravelsFromFollowedClients'=>
            "WITH eligibles_clients AS (
    SELECT id
    FROM client
    WHERE (id IN (SELECT idclient1 FROM friend WHERE idclient2 = ".$idclient." AND accepted = 2)
    OR id IN (SELECT idclient2 FROM friend WHERE idclient1 = ".$idclient." AND accepted = 2))
)
SELECT travel.id
FROM travel
JOIN eligibles_clients ON travel.idclient = eligibles_clients.id
WHERE travel.visibility = 1 ORDER BY travel_date DESC",
        'getTravelsFromFriendClients'=>
            "WITH eligibles_clients AS (
    SELECT id
    FROM client
    WHERE (
    SELECT COUNT(id) FROM travel WHERE idclient = client.id AND visibility = 1) > 0
      AND (id IN (SELECT idclient1 FROM friend WHERE idclient2 = ".$idclient." AND accepted = 2) OR id IN (SELECT idclient2 FROM friend WHERE idclient1 = ".$idclient." AND accepted = 2))
)
SELECT travel.id
FROM travel JOIN eligibles_clients ON travel.idclient = eligibles_clients.id
WHERE visibility = 1 ORDER BY travel.travel_date DESC",
        'getRecentTravelsFromPopularClients'=>
            "WITH eligibles_clients AS (
    SELECT id
    FROM client
    WHERE (SELECT COUNT(id) FROM travel WHERE visibility = 1) > 0
),
sorted_clients AS (
    SELECT id
    FROM eligibles_clients
    ORDER BY (SELECT COUNT(idclientfollowed) FROM follower WHERE idclientfollowed = eligibles_clients.id) DESC
    LIMIT 40
),
client_travels AS (
    SELECT t.id as idtravel, t.idclient,
           ROW_NUMBER() OVER (PARTITION BY t.idclient ORDER BY RAND()) AS rn,
           COUNT(t.id) OVER (PARTITION BY t.idclient) AS total_travels
    FROM travel t
    JOIN sorted_clients c ON t.idclient = c.id
    WHERE t.visibility = 1 AND t.travel_status = 1 AND DATEDIFF(NOW(), t.travel_date) <= 14
)
SELECT idtravel
FROM client_travels
WHERE rn <= ROUND(total_travels * 0.2)
ORDER BY rn",
        'getRecentPopularTravelsWithSimilarThemes'=>
            "WITH theme_popularity AS (
    SELECT travel.idtheme AS idtheme, COUNT(travel.idtheme) AS theme_count
    FROM travel 
    JOIN travel_view ON travel.id = travel_view.idtravel
    WHERE travel_view.idclient = ".$idclient." AND travel.visibility = 1
    GROUP BY travel.idtheme
),
good_themes AS (
    SELECT idtheme,theme_count
    FROM theme_popularity
    ORDER BY theme_count DESC
    LIMIT 5
),
eligible_travel AS (
    SELECT id
    FROM travel JOIN good_themes ON travel.idtheme = good_themes.idtheme
    WHERE visibility != 0 AND travel_status = 1 AND DATEDIFF(NOW(), travel_date) <= 14
),
travel_count AS (
    SELECT idtravel, COUNT(idtravel) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM eligible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM travel_count
ORDER BY cnt DESC
LIMIT 40"
    ];

    $SQLcommandResults = [];
    foreach ($SQLcommand as $goal => $command) {
        $query = $bdd->prepare($command);
        $query->execute();

        $results = $query->fetchAll();

        $commandResult = [];
        foreach ($results as $result) {
            foreach ($result as $columnName => $columnValue) {
                $idtravel = $columnValue;
                array_push($commandResult, $idtravel);
            }
        }
        $SQLcommandResults[$goal] = $commandResult;
    }

    $query = $bdd->prepare('SELECT COUNT(idclient) AS view_count FROM travel_view WHERE idclient = ?');
    $query->execute([$idclient]);
    $progress = $query->fetch(PDO::FETCH_ASSOC);

    if (!$progress) {
        $progress = ['view_count' => 0];
    }

    $choiceMax = intdiv($progress['view_count'], 10) + 20;
    if ($choiceMax > 100) {
        $choiceMax = 100;
    }

    $travelsTotal = 0;
    foreach ($SQLcommandResults as $commandResult){
        $travelsTotal += count($commandResult);
    }

    while ($travelsTotal > 0){
        $choice = rand(1, $choiceMax);
        if ($choice <= 20){
            // Partie aléatoire
            $choice = rand(1, 100);
            if ($choice <= $poids["mostViewAllTime"]){
                $categorie = "getMostViewedTravelsOfAllTime";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["mostViewAllYear"]){
                $categorie = "getMostViewedTravelsOfYear";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["mostViewAllMonth"]){
                $categorie = "getMostViewedTravelsOfMonth";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["mostViewAllWeek"]){
                $categorie = "getMostViewedTravelsOfWeek";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["fromPopularClient"]){
                $categorie = "getTravelsFromPopularClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["fromNonPopularClient"]){
                $categorie = "getTravelsFromNonPopularClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            }
        } else {
            // Partie recommandée
            $choice = rand(1, 100);
            if ($choice <= $poids["fromFollowedClient"]){
                $categorie = "getTravelsFromFollowedClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["fromFriend"]){
                $categorie = "getTravelsFromFriendClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["recentfromPopularClient"]){
                $categorie = "getRecentTravelsFromPopularClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["recentWithSimilarThemes"]){
                $categorie = "getRecentPopularTravelsWithSimilarThemes";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            }
        }
    }

    $travels = array_unique($travels);
    $_SESSION["excludedTravels"] = $travels;

    return $travels;
}