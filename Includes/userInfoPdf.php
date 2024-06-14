<?php
include "Structure/Functions/function.php";
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="../Design/Css/bootstrap.css" rel="stylesheet">
    <link href="../Design/Css/style.css" rel="stylesheet">
    <title>Vos informations personnelles</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2, h3 {
            color: #0066cc;
            margin-bottom: 10px;
        }

        p {
            margin: 10px 0;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .social-links a {
            color: #0066cc;
            text-decoration: none;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Vos informations personnelles</h1>
    <?php if (!empty($user) && isset($user[0])): ?>
        <?php $userData = $user[0]; ?>
        <div class="user-info">
            <h2>
                <?php
                $displayName = '';

                if (!empty($userData['lastname'])) {
                    $displayName .= $userData['lastname'];
                }

                if (!empty($userData['firstname'])) {
                    // Ajoutez un espace avant le prénom si un nom de famille a été ajouté
                    if (!empty($userData['lastname'])) {
                        $displayName .= ' ';
                    }
                    $displayName .= $userData['firstname'];
                }

                if (!empty($displayName)) {
                    // Ajoutez le pseudo entre parenthèses si le nom ou prénom a été ajouté
                    $displayName .= ' (alias ' . $userData['pseudo'] . ')';
                } else {
                    // Sinon, affichez uniquement le pseudo
                    $displayName = $userData['pseudo'];
                }

                echo $displayName;
                ?>
            </h2>
            <h3>Email : <?php echo $userData['email']; ?></h3>
            <p>Rôle :
                <?php
                switch ($userData['idrank']) {
                    case 1:
                        echo "Utilisateur";
                        break;
                    case 2:
                        echo "Administrateur";
                        break;
                    case 3:
                        echo "Modérateur";
                        break;
                    default:
                        echo "Inconnu";
                }
                ?>
            </p>
            <p>Dernière connexion : <?php echo formatFrenchDate($userData['last_login_date']); ?></p>
            <?php if (!empty($userData['coin'])): ?>
                <p>Pièces : <?php echo $userData['coin']; ?></p>
            <?php endif; ?>
            <?php if (!empty($userData['mood'])): ?>
                <p>Humeur : <?php echo $userData['mood']; ?></p>
            <?php endif; ?>
            <?php if (!empty($userData['summary'])): ?>
                <p>Résumé : <?php echo $userData['summary']; ?></p>
            <?php endif; ?>
            <?php if (!empty($userData['insta']) || !empty($userData['facebook']) || !empty($userData['github']) || !empty($userData['twitter']) || !empty($userData['youtube'])): ?>
                <p>Réseaux sociaux :</p>
                <ul class="social-links">
                    <?php
                    $socialNetworks = ['insta', 'facebook', 'github', 'twitter', 'youtube'];
                    foreach ($socialNetworks as $network) {
                        if (!empty($userData[$network])) {
                            echo '<li><a href="' . $userData[$network] . '">' . ucfirst($network) . '</a></li>';
                        }
                    }
                    ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="travel-info">
            <h3>Nombre de voyages rédigés : <?php echo $numberTravel; ?></h3>
            <?php if($numberTravel != 0): ?>
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Titre</th>
                        <th scope="col">Date</th>
                        <th scope="col">Nombre de vues</th>
                        <th scope="col">Nombre de likes</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($travels as $travel): ?>
                        <tr>
                            <td><?php echo $travel['title']; ?></td>
                            <td><?php echo formatFrenchDate($travel['travel_date']); ?></td>
                            <td><?php echo $travel['nbView']; ?></td>
                            <td><?php echo $travel['nbLike']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <div class="quiz-info">
            <h3>Customisation possédée</h3>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">Nom de la customisation</th>
                    <th scope="col">Date d'achat</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($customisations as $customisation): ?>
                    <tr>
                        <td><?php echo $customisation['picture_name']; ?></td>
                        <td><?php echo formatFrenchDate($customisation['purchase_date']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <div class="custom-info">
            <h3>Quiz réalisés : </h3>
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th scope="col">Titre</th>
                    <th scope="col">Difficulté</th>
                    <th scope="col">Score</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($Quiz as $quiz):?>
                    <tr>
                        <td><?php echo $quiz['title']; ?></td>
                        <td><?php echo $quiz['diff']; ?></td>
                        <td><?php echo $quiz['score']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <div class="friend-info">
            <h3>Liste de vos amis</h3>
            <ul class="list-group">
                <?php foreach ($friends as $friend):?>



                    <li class="list-group-item"><?php echo $friend['pseudo']; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <p>Aucune donnée utilisateur trouvée.</p>
    <?php endif; ?>
</div>
</body>
</html>