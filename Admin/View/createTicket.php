<?php
require "Structure/Functions/function.php";
session_start();

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkAdminRole();

    $pageTitle = "Création d'un ticket";

    require "Structure/Bdd/config.php";

} else {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["createTicket"])) {

        $title = htmlspecialchars($_POST["ticketTitle"]);
        $description = $_POST["json"];
        $priority = $_POST["priority"];
        $dateLimit = $_POST["dueDate"];
        $dateToday = date("Y-m-d");
        $assignedTo = $_POST["assignee"];
        $ticketType = $_POST["type"];

        if (empty($description)) {
            echo "Le champ description ne peut pas être vide.";
            exit;
        }

        $newTicket = $bdd->prepare("INSERT INTO ticket (creation_date, target_resolution_date, title, ticket_status, summary, priority, idsubmitter, idassigned, ticket_type) VALUES (:creation_date, :target_resolution_date, :title, 0, :summary, :priority, :idsubmitter, :idassigned, :ticket_type)");
        $newTicket->bindParam(':creation_date', $dateToday);
        $newTicket->bindParam(':target_resolution_date', $dateLimit);
        $newTicket->bindParam(':title', $title);
        $newTicket->bindParam(':summary', $description);
        $newTicket->bindParam(':priority', $priority);
        $newTicket->bindParam(':idsubmitter', $userId);
        $newTicket->bindParam(':idassigned', $assignedTo);
        $newTicket->bindParam(':ticket_type', $ticketType);

        if ($newTicket->execute()) {
            $ticketId = $bdd->lastInsertId();

            // Création du dossier pour le ticket
            $ticketDir = "Admin/Structures/Ticket/$ticketId";
            if (!file_exists($ticketDir)) {
                mkdir($ticketDir, 0777, true);
            }

            // Déplacement des fichiers téléchargés dans le dossier du ticket
            foreach ($_FILES['file']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['file']['name'][$key];
                $file_size = $_FILES['file']['size'][$key];
                $file_tmp = $_FILES['file']['tmp_name'][$key];

                move_uploaded_file($file_tmp, "$ticketDir/$file_name");
            }

            header("Location: ticket.php?id=$ticketId&ticket=success");
        } else {
            echo "Erreur lors de la création du ticket.";
        }
    }
}

$selectAdmin = $bdd->prepare('SELECT pseudo, id FROM client WHERE idrank = 2');
$selectAdmin->execute();
$selectAdmin = $selectAdmin->fetchAll();

require "Admin/Structures/Head/headAdmin.php";
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
        <h1 class="mt-5 mx-2">Créer un ticket</h1>

        <form action="" method="post" class="mx-5" enctype="multipart/form-data">

            <div class="mb-3">
                <label for="ticketTitle" class="form-label">Enoncé d'un ticket</label>
                <input id="ticketTitle" type="text" class="form-control" required maxlength="128" name="ticketTitle" placeholder="Saisissez l'énoncé">
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Importance du ticket</label>
                <select id="priority" class="form-select" required name="priority">
                    <option value="">Sélectionnez une priorité</option>
                    <option value="1">Mineur</option>
                    <option value="2">Faible</option>
                    <option value="3">Moyenne</option>
                    <option value="4">Haute</option>
                    <option value="5">Critique</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Type de ticket</label>
                <select id="priority" class="form-select" required name="type">
                    <option value="">Sélectionnez le type de ticket</option>
                    <option value="1">Signalement d'un utilisateur</option>
                    <option value="2">Incident</option>
                    <option value="3">Problème</option>
                    <option value="4">Requête</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="assignee" class="form-label">Assigné à</label>
                <select id="assignee" class="form-select" required name="assignee">
                    <?php foreach ($selectAdmin as $admin): ?>
                        <option value="<?php echo $admin['id']; ?>"><?php echo htmlspecialchars($admin['pseudo']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="dueDate" class="form-label">Date d'échéance</label>
                <input id="dueDate" type="date" class="form-control" required name="dueDate">
            </div>

            <div class="mb-3">
                <label for="attachment">
                    <a class="btn btn-primary text-light" role="button" aria-disabled="false">+ Ajouter un fichier</a>
                </label>
                <input type="file" name="file[]" accept="*/*" id="attachment" style="visibility: hidden; position: absolute;" multiple />
            </div>

            <div id="files-area" class="mb-3">
        <span id="filesList">
            <span id="files-names"></span>
        </span>
            </div>

            <div id="total-size" class="mb-3 text-danger"></div>


            <div class="container">
                <div id="editor"></div>
            </div>
            <input type="hidden" id="editorContent" name="json">

            <button type="submit" name="createTicket" class="btn btn-primary">Créer</button>
        </form>
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
    initializeEditorTicket(false, "");

</script>

<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>
<script src="Structures/Functions/ticket.js"></script>

</body>
</html>

