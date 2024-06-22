<?php
require "Structure/Functions/function.php";
require "Structure/Functions/alerts.php";

session_start();

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkAdminRole();

    $pageTitle = "Page d'un ticket";

    require "Structure/Bdd/config.php";
} else {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $selectDataFromTicket = $bdd->prepare("SELECT creation_date, target_resolution_date, resolution_date, ticket_status, idsubmitter, idassigned, title, summary, priority, ticket_type FROM ticket WHERE id = ?");
    $selectDataFromTicket->execute(array($id));
    $ticketData = $selectDataFromTicket->fetch();

    $createDate = isset($ticketData['creation_date']) ? $ticketData['creation_date'] : null;
    $priority = isset($ticketData['priority']) ? $ticketData['priority'] : null;
    $title = isset($ticketData['title']) ? $ticketData['title'] : null;
    $summary = isset($ticketData['summary']) ? $ticketData['summary'] : "";
    $target = isset($ticketData['target_resolution_date']) ? formatFrenchDate($ticketData['target_resolution_date']) : "Aucune";
    $resolution = isset($ticketData['resolution_date']) ? $ticketData['resolution_date'] : null;
    $ticketStatus = isset($ticketData['ticket_status']) ? $ticketData['ticket_status'] : 0;
    $ticketType = isset($ticketData['ticket_type']) ? $ticketData['ticket_type'] : null;
    $submitter = isset($ticketData['idsubmitter']) ? $ticketData['idsubmitter'] : null;
    $assigned = isset($ticketData['idassigned']) ? $ticketData['idassigned'] : null;


    if ($assigned != null){
        $assignedUser = $bdd->prepare("SELECT pseudo FROM client WHERE id = ?");
        $assignedUser->execute(array($assigned));
        $assignedUserData = $assignedUser->fetch();
        $pseudo = isset($assignedUserData['pseudo']) ? $assignedUserData['pseudo'] : null;
    } else {
        $pseudo = "Aucun";
    }



    $submitterUser = $bdd->prepare("SELECT pseudo FROM client WHERE id = ?");
    $submitterUser->execute(array($submitter));
    $submitterUserData = $submitterUser->fetch();
    $pseudoSubmitter = isset($submitterUserData['pseudo']) ? $submitterUserData['pseudo'] : null;

    switch ($priority) {
        case 1:
            $priorityDescription = "Mineur";
            break;
        case 2:
            $priorityDescription = "Faible";
            break;
        case 3:
            $priorityDescription = "Moyenne";
            break;
        case 4:
            $priorityDescription = "Haute";
            break;
        case 5:
            $priorityDescription = "Critique";
            break;
        default:
            $priorityDescription = "Non définie";
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sign"])) {

        $ticketDir = 'Admin/Structures/Ticket/' . $id;
        if (!file_exists($ticketDir)) {
            mkdir($ticketDir, 0777, true);
        }


        $imageData = $_POST["sign"];
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
        $imageName = $ticketDir . '/signature.jpg'; // Nom spécifique pour l'image
        file_put_contents($imageName, $image);


        $closeTicket = $bdd->prepare("UPDATE ticket SET ticket_status = 1, resolution_date = NOW() WHERE id = ?");
        $closeTicket->execute(array($id));

        header ("Location: ticket.php?id=" . $id);

    }


} else {
    header("Location: ticketLobby.php");
    exit;
}

require "Admin/Structures/Head/headAdmin.php";

createTicket();

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest/dist/editorjs.min.css">
<link rel="stylesheet" href="../Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-admin.css">
<link rel="stylesheet" href="../Design/Css/editorjs.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php"; ?>
<div class="wrapper">
    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php"; ?>

    <div class="main mt-5">
        <h1 class="mt-5 mx-2">Ticket numéro <?php echo $id;
            if($ticketStatus != 0){
                echo ' (Ticket fermé)';
            }

        ?>



        </h1>

        <div class="container">
            <div class="table-responsive">
            <table class="table table-bordered table-custom-alternative-row-color">
                <thead>
                <tr>
                    <th class="text-center" scope="col">Identifiant</th>
                    <th class="text-center" scope="col">Demandé par</th>
                    <th class="text-center" scope="col">Affecté à </th>
                    <th class="text-center" scope="col">Priorité</th>
                    <th class="text-center" scope="col">Date de création</th>
                    <th class="text-center" scope="col">Date de rendu </th>
                    <?php if($resolution != NULL){
                        echo '<th class="text-center" scope="col">Date de rendu </th>';
                    } ?>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center"><?php echo $id;?></td>
                    <td class="text-center"><?php echo $pseudoSubmitter;?></td>
                    <td class="text-center"><?php echo $pseudo;?></td>
                    <td class="text-center"><?php echo $priorityDescription; ?></td>
                    <td class="text-center"><?php echo formatFrenchDate($createDate); ?></td>
                    <td class="text-center"><?php echo $target; ?></td>
                    <?php if($resolution != NULL){
                        echo '<td class="text-center">'.formatFrenchDate($resolution).'</td>';
                    } ?>
                </tr>
                </tbody>
            </table>
                </div>

                <h2><?php echo $title; ?></h2>

                <div id="editor"></div>
            <div class="mt-3">

                <?php
                $logDir = 'Admin/Structures/Ticket/' . $id;
                if (file_exists($logDir)) {
                $logs = scandir($logDir);

                if ($logs) {
                    ?>

                <h3>Liste des fichiers de logs</h3>
                <div class="table-responsive">
                <table class="logs-table table table-bordered table-custom-alternative-row-color">
                    <thead>
                    <tr>
                        <th scope="col">Nom du fichier</th>
                        <th scope="col">Date d'ajout</th>
                        <th scope="col">Télécharger</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach ($logs as $log) {
                        if ($log !== '.' && $log !== '..') {
                            $filePath = $logDir . '/' . $log;
                            if (is_file($filePath)) {
                                $modificationTime = date("d-m-Y H:i:s", filemtime($filePath));
                                $encodedFileName = urlencode($log); // Encodage du nom du fichier
                                echo '<tr>';
                                echo '<td>' . $log . '</td>';
                                echo '<td>' . $modificationTime . '</td>';
                                echo '<td><button class="btn-primary btn" onclick="window.location.href=\'/Admin/downloadDocumentTicket?id=' . $id . '&file=' . $encodedFileName . '\'">Télécharger</button></td>';

                                echo '</tr>';
                            }
                        }
                    }

                    ?>

                    </tbody>
                </table>
                </div>
                <?php }

                }?>
            </div>



            <?php if($ticketStatus != 1){

                echo '<button class="btn-landtales mb-5" onclick="window.location.href=\'modifyTicket.php?id=' . $id . '\'">Modifier le ticket</button>';


                if ($userId == $assigned){ ?>
                    <button type="button" class="btn-landtales" data-bs-toggle="modal" data-bs-target="#signatureModal">Fermer le ticket</button>
                    <div class="modal fade" id="signatureModal" tabindex="-1" aria-labelledby="signatureModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="signatureModalLabel">Signature du ticket</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="canvas-container text-center">
                                        <canvas id="canvas" class="canvas-container" width="350" height="350" style="border: 1px solid black;"></canvas>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="button" class="btn btn-danger" id="clearCanvasBtn">Réinitialiser</button> <!-- Bouton de réinitialisation -->
                                    <button type="button" class="btn btn-primary" id="saveSignatureBtn">Valider</button>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                }
            } ?>


            <div>
                <?php if($ticketStatus != 0){
                    echo '<img src="Structures/Ticket/' . $id . '/signature.jpg">';
                    echo  '<p>Signé le '.formatFrenchDate($resolution) .' par '.$pseudo.'</p>';

                } ?>
            </div>



        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/checklist@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/quote@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/table@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/marker@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@sotaproject/strikethrough@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-paragraph-with-alignment@3.0.0"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/warning@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/list@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/underline@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/editorjs-undo"></script>
<script src="https://cdn.jsdelivr.net/npm/@sotaproject/strikethrough@latest"></script>

<script src="../Structure/Functions/editorjs.js"></script>
<script>
    initializeEditorTicket(true, <?php echo $summary ?>);
</script>

<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>
<script src="Structures/Functions/ticket.js"></script>
<script src="Structures/Functions/admin.js"></script>

</body>
</html>
