<?php
require "Structure/Functions/function.php";
session_start();

// Vérifie si l'utilisateur est connecté
if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkAdminRole();

    $pageTitle = "Suivi des Logs de Landtales";

    require "Structure/Bdd/config.php";

} else {
    // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../login.php");
    exit;
}

    if(isset($_GET['error']) && !empty($_GET['error'])){

        echo '<script>alert("Le ticket n\'existe pas.");</script>';

    }


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["searchTicket"])) {
    $id = $_POST["searchTicket"];

    $isExist = $bdd->prepare("SELECT id FROM ticket WHERE id = :id");
    $isExist->bindParam(":id", $id);
    $isExist->execute();
    $ticket = $isExist->fetch();

    if ($ticket) {
        header("Location: ticket.php?id=" . $id);
        exit;
    } else {
        header("Location: ticketLobby.php?error=true");
    }
}



// Inclut les fichiers de configuration et de style
require "Admin/Structures/Head/headAdmin.php";
$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../Design/Css/style.css">
    <link rel="stylesheet" href="Design/Css/home-admin.css">
    <style>
        .scrollable {
            max-height: 530px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php"; ?>
<div class="wrapper">
    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php"; ?>

    <div class="main mt-5">
        <h1 class="mt-5 mx-5">Panel de contrôle des tickets</h1>
        <button class="btn-landtales mx-5" onclick="window.location.href='createTicket.php'">Créer un ticket</button>
        <div class="d-flex justify-content-end mx-5">
            <div class="col-12 col-md-3">
                <form action="" method="post" class="mb-3">
                    <label for="searchTicket" class="form-label">Recherche un ticket</label>
                    <input id="searchTicket" type="number" name="searchTicket" class="form-control" placeholder="Rechercher un ticket (n°)">
                </form>
            </div>
        </div>




        <div class="row mx-5">
            <?php
            // Boucle sur les colonnes pour afficher les sections de tickets
            $columns = array(
                array("title" => "Tickets non réalisés", "id" => "assignedNotCompleted"),
                array("title" => "Tickets réalisés", "id" => "assignedCompleted"),
                array("title" => "Tous les tickets non attribués", "id" => "notAssigned")
            );

            foreach ($columns as $column) {
                echo '<div class="col-12 col-md-6 col-lg-4">
                <div class="card mx-2 my-3">
                    <div class="card-header">' . $column["title"] . '</div>
                    <div class="card-body" id="' . $column["id"] . '"></div>
                    <div class="card-footer"><div class="collapse" id="collapse' . ucfirst($column["id"]) . '"></div></div>
                </div>
              </div>';
            }
            ?>
        </div>
    </div>
</div>

<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        fetchTickets();

        function fetchTickets() {
            fetch('fetchTicket.php?id=<?php echo $userId; ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error from server:', data.error);
                    } else {
                        // Appelle la fonction pour afficher les tickets dans chaque section
                        displayTickets('assignedNotCompleted', data.assignedNotCompleted);
                        displayTickets('assignedCompleted', data.assignedCompleted);
                        displayTickets('notAssigned', data.notAssigned);
                    }
                })
                .catch(error => console.error('Error fetching tickets:', error));
        }

        function displayTickets(containerId, tickets) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';

            if (tickets.length === 0) {
                container.innerHTML = '<p>Aucun ticket n\'existe pour le moment.</p>';
                return;
            }

            // Affiche les 5 premiers tickets dans chaque section
            tickets.slice(0, 5).forEach(ticket => {
                const ticketElement = document.createElement('a');
                ticketElement.classList.add('ticket', 'card', 'my-2', 'p-2');
                ticketElement.href = `ticket?id=${ticket.id}`;

                // Vérifie si la date de résolution est null
                const resolutionDate = ticket.target_resolution_date ? ticket.target_resolution_date : 'Aucune';

                ticketElement.innerHTML = `
            <h5>${ticket.title}</h5>
            <p>Date limite de résolution : ${resolutionDate}</p>
        `;
                container.appendChild(ticketElement);
            });

            // Ajoute un bouton pour voir plus de tickets s'il y en a plus de 5
            if (tickets.length > 5) {
                const moreButton = document.createElement('button');
                moreButton.classList.add('btn', 'btn-link', 'btn-more');
                moreButton.textContent = 'Voir plus';
                moreButton.addEventListener('click', function() {
                    if (container.classList.contains('show')) {
                        // Réduit le nombre de tickets affichés à 5 s'il y en a plus
                        moreButton.textContent = 'Voir plus';
                        container.classList.remove('show');
                        container.classList.remove('scrollable'); // Supprime la classe scrollable
                        container.innerHTML = '';
                        tickets.slice(0, 5).forEach(ticket => {
                            const ticketElement = document.createElement('a');
                            ticketElement.classList.add('ticket', 'card', 'my-2', 'p-2');
                            ticketElement.href = `ticket?id=${ticket.id}`;
                            const resolutionDate = ticket.target_resolution_date ? ticket.target_resolution_date : 'Aucune';
                            ticketElement.innerHTML = `
                        <h5>${ticket.title}</h5>
                        <p>Date de résolution attendu : ${resolutionDate}</p>
                    `;
                            container.appendChild(ticketElement);
                        });
                    } else {
                        // Affiche tous les tickets s'ils sont masqués
                        moreButton.textContent = 'Voir moins';
                        container.classList.add('show');
                        container.classList.add('scrollable'); // Ajoute la classe scrollable
                        container.innerHTML = '';
                        tickets.forEach(ticket => {
                            const ticketElement = document.createElement('a');
                            ticketElement.classList.add('ticket', 'card', 'my-2', 'p-2');
                            ticketElement.href = `ticket?id=${ticket.id}`;
                            const resolutionDate = ticket.target_resolution_date ? ticket.target_resolution_date : 'Aucune';
                            ticketElement.innerHTML = `
                        <h5>${ticket.title}</h5>
                        <p>Date de résolution : ${resolutionDate}</p>
                    `;
                            container.appendChild(ticketElement);
                        });
                    }
                });
                container.parentNode.querySelector('.card-footer').appendChild(moreButton);
            }
        }
    });
</script>
</body>
</html>
