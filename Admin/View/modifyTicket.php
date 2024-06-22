<?php
require "Structure/Functions/function.php";
session_start();

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkAdminRole();

    $pageTitle = "Modification d'un ticket";

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
    $target = isset($ticketData['target_resolution_date']) ? $ticketData['target_resolution_date'] : "Aucune";
    $resolution = isset($ticketData['resolution_date']) ? $ticketData['resolution_date'] : null;
    $ticketStatus = isset($ticketData['ticket_status']) ? $ticketData['ticket_status'] : null;
    $ticketType = isset($ticketData['ticket_type']) ? $ticketData['ticket_type'] : null;
    $submitter = isset($ticketData['idsubmitter']) ? $ticketData['idsubmitter'] : null;
    $assigned = isset($ticketData['idassigned']) ? $ticketData['idassigned'] : null;

    if ($assigned != null) {
        $assignedUser = $bdd->prepare("SELECT pseudo FROM client WHERE id = ?");
        $assignedUser->execute(array($assigned));
        $assignedUserData = $assignedUser->fetch();
        $pseudo = isset($assignedUserData['pseudo']) ? $assignedUserData['pseudo'] : null;
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

    $selectAdmin = $bdd->prepare('SELECT pseudo, id FROM client WHERE idrank = 2');
    $selectAdmin->execute();
    $selectAdmin = $selectAdmin->fetchAll();

    $ticketDir = "Admin/Structures/Ticket/$id";
    $existingFiles = [];
    if (is_dir($ticketDir)) {
        $existingFiles = array_diff(scandir($ticketDir), ['.', '..']);
    }

    $existingFilesSize = 0;
    foreach ($existingFiles as $file) {
        $filePath = $ticketDir . '/' . $file;
        $existingFilesSize += filesize($filePath);
    }


    if (isset($_POST["updateTicket"])) {
        $title = htmlspecialchars($_POST["ticketTitle"]);
        $json = isset($_POST["json"]) && !empty($_POST["json"]) ? $_POST["json"] : $summary;
        $priority = $_POST["priority"];
        $dateLimit = $_POST["dueDate"];
        $dateToday = date("Y-m-d");
        $assignedTo = $_POST["assignee"];
        $ticketType = $_POST["type"];

        $updateTicket = $bdd->prepare("UPDATE ticket SET target_resolution_date = :target_resolution_date, title = :title, summary = :summary, priority = :priority, idassigned = :idassigned, ticket_type = :ticket_type WHERE id = :id");
        $updateTicket->bindParam(':target_resolution_date', $dateLimit);
        $updateTicket->bindParam(':title', $title);
        $updateTicket->bindParam(':summary', $json);
        $updateTicket->bindParam(':priority', $priority);
        $updateTicket->bindParam(':idassigned', $assignedTo);
        $updateTicket->bindParam(':ticket_type', $ticketType);
        $updateTicket->bindParam(':id', $id);

        if ($updateTicket->execute()) {
            if (!file_exists($ticketDir)) {
                mkdir($ticketDir, 0777, true);
            }

            foreach ($_FILES['file']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['file']['name'][$key];
                $file_size = $_FILES['file']['size'][$key];
                $file_tmp = $_FILES['file']['tmp_name'][$key];

                move_uploaded_file($file_tmp, "$ticketDir/$file_name");
            }

            header("Location: ticket.php?id=$id");
        } else {
            echo "Erreur lors de la mise à jour du ticket.";
        }
    }



} else {
    header("Location: ticketLobby.php");
    exit;
}

require "Admin/Structures/Head/headAdmin.php";
$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest/dist/editorjs.min.css">
<link rel="stylesheet" href="../Design/Css/style.css">
<link rel="stylesheet" href="../Design/Css/editorjs.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php"; ?>
<div class="wrapper">
    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php"; ?>

    <div class="main mt-5">
        <h1 class="mt-5 mx-2">Modification du ticket numéro <?php echo $id ?></h1>

        <div class="container">
            <form action="" method="post" class="mx-2" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="ticketTitle" class="form-label">Enoncé d'un ticket</label>
                    <input id="ticketTitle" type="text" class="form-control" required maxlength="128" name="ticketTitle" placeholder="Saisissez l'énoncé" value="<?php echo str_replace('&#039;', "'",$title); ?>">
                </div>

                <div class="mb-3">
                    <label for="priority" class="form-label">Importance du ticket</label>
                    <select id="priority" class="form-select" required name="priority">
                        <option value="">Sélectionnez une priorité</option>
                        <option value="1" <?php echo ($priority == 1) ? 'selected' : ''; ?>>Mineur</option>
                        <option value="2" <?php echo ($priority == 2) ? 'selected' : ''; ?>>Faible</option>
                        <option value="3" <?php echo ($priority == 3) ? 'selected' : ''; ?>>Moyenne</option>
                        <option value="4" <?php echo ($priority == 4) ? 'selected' : ''; ?>>Haute</option>
                        <option value="5" <?php echo ($priority == 5) ? 'selected' : ''; ?>>Critique</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="ticketType" class="form-label">Type de ticket</label>
                    <select id="ticketType" class="form-select" required name="type">
                        <option value="">Sélectionnez le type de ticket</option>
                        <option value="1" <?php echo ($ticketType == 1) ? 'selected' : ''; ?>>Signalement d'un utilisateur</option>
                        <option value="2" <?php echo ($ticketType == 2) ? 'selected' : ''; ?>>Incident</option>
                        <option value="3" <?php echo ($ticketType == 3) ? 'selected' : ''; ?>>Problème</option>
                        <option value="4" <?php echo ($ticketType == 4) ? 'selected' : ''; ?>>Requête</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="assignee" class="form-label">Assigné à</label>
                    <select id="assignee" class="form-select" required name="assignee">
                        <?php foreach ($selectAdmin as $admin): ?>
                            <option value="<?php echo $admin['id']; ?>" <?php echo ($assigned == $admin['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($admin['pseudo']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="dueDate" class="form-label">Date d'échéance</label>
                    <input id="dueDate" type="date" class="form-control" required name="dueDate" value="<?php echo htmlspecialchars($target); ?>">
                </div>

                <div class="mb-3">
                    <label for="attachment">
                        <a class="btn btn-primary text-light" role="button" aria-disabled="false">+ Ajouter un fichier</a>
                    </label>
                    <input type="file" name="file[]" accept="*/*" id="attachment" style="visibility: hidden; position: absolute;" multiple />
                </div>

                <div id="files-area" class="mb-3">
                    <span id="filesList">
                        <?php
                        $totalExistingSize = 0; // Initialise la taille totale des fichiers existants à zéro
                        foreach ($existingFiles as $file): ?>
                            <?php
                            $fileSize = filesize("$ticketDir/$file"); // Obtient la taille du fichier
                            $totalExistingSize += $fileSize; // Ajoute la taille du fichier à la taille totale des fichiers existants
                            ?>
                            <span class="existing-file" data-fileSize="<?php echo $fileSize; ?>"><?php echo $file; ?></span><br>
                        <?php endforeach; ?>
                        <span id="files-names"></span>
                    </span>
                </div>

                <div id="total-size" class="mb-3 text-danger"></div>

                <div class="container">
                    <div id="editor"></div>
                </div>
                <input type="hidden" id="editorContent" name="json">

                <button type="submit" name="updateTicket" class="btn btn-primary">Sauvegarder</button>
            </form>
        </div>

    </div>
</div>
<script>
    let totalSize = <?php echo $totalExistingSize; ?>;


    document.addEventListener("DOMContentLoaded", function() {
        const dt = new DataTransfer();
        const MAX_SIZE = 2 * 1024 * 1024;

        document.getElementById('total-size').textContent = `Taille totale : ${(totalSize / 1024 / 1024).toFixed(2)} Mo`;

        document.getElementById('attachment').addEventListener('change', function(e) {
            Array.from(this.files).forEach(file => {
                if (totalSize + file.size > MAX_SIZE) {
                    alert('La taille totale des fichiers dépasse 2 Mo.');
                    return;
                }

                totalSize += file.size;

                let fileBloc = document.createElement('span');
                fileBloc.classList.add('file-block');

                let fileName = document.createElement('span');
                fileName.classList.add('name');
                fileName.textContent = file.name;

                let br = document.createElement('br');

                let fileDelete = document.createElement('span');
                fileDelete.classList.add('file-delete');

                let deleteBtn = document.createElement('button');
                deleteBtn.classList.add('btn', 'btn-danger');
                deleteBtn.textContent = '-';
                deleteBtn.addEventListener('click', function() {
                    let name = this.parentElement.nextElementSibling.textContent;
                    this.parentElement.parentElement.remove();

                    for (let i = 0; i < dt.items.length; i++) {
                        if (name === dt.items[i].getAsFile().name) {
                            let sizeToRemove = dt.items[i].getAsFile().size; // Taille du fichier à supprimer
                            dt.items.remove(i);
                            totalSize -= sizeToRemove; // Soustrait la taille du fichier supprimé de totalSize
                            break;
                        }
                    }

                    document.getElementById('attachment').files = dt.files;
                    document.getElementById('total-size').textContent = `Taille totale : ${(totalSize / 1024 / 1024).toFixed(2)} Mo`;

                    if (totalSize > MAX_SIZE) {
                        alert('La taille totale des fichiers dépasse 2 Mo.');
                    }
                });

                fileDelete.appendChild(deleteBtn);
                fileBloc.appendChild(fileDelete);
                fileBloc.appendChild(document.createTextNode('\u00A0\u00A0')); // Ajout d'espaces entre le bouton et le nom du fichier
                fileBloc.appendChild(fileName);
                fileBloc.appendChild(br);

                document.getElementById('files-names').appendChild(fileBloc);

                dt.items.add(file);
            });

            this.files = dt.files;

            document.getElementById('total-size').textContent = `Taille totale : ${(totalSize / 1024 / 1024).toFixed(2)} Mo`;
        });
    });


</script>
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
    const ticketData = <?php echo $summary; ?>;
    initializeEditorTicket(false, ticketData);
</script>
<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>



</body>
</html>
