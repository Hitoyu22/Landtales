<?php
require "Structure/Functions/function.php";

session_start();
if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];

    checkAdminRole();

    $pageTitle = "Gestion des captcha";

    require "Structure/Bdd/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST['newCaptcha'])){
            $newQuestion = isset($_POST['captcha_question']) ? htmlspecialchars($_POST['captcha_question']) : '';
            $newAnswer = isset($_POST['captcha_answer']) ? htmlspecialchars($_POST['captcha_answer']) : '';

            $newCaptcha = $bdd->prepare("INSERT INTO captcha (question, answer, idcreator) VALUES (?,?,?)");
            $newCaptcha->execute([$newQuestion,$newAnswer,$userId]);

            header("Location: captcha.php?add=success");
        }

        if (isset($_POST['delete']) && $_POST['delete'] === 'Oui') {
            $id = $_POST['id'];
            suppr_sql('captcha', $id, $bdd);  // Assurez-vous que cette fonction est bien définie
            header("Location: captcha.php?delete=success");
            exit;
        }

        if (isset($_POST['update']) && $_POST['update'] === 'modify') {
            $id = $_POST['id'];
            foreach ($_POST[$id . 'tab'] as $key => $value) {
                modify_sql('captcha', $id, $key, $value,$bdd);
            }
            header("Location: captcha.php?change=success"); // Redirection pour éviter le rechargement du formulaire
            exit;
        }


        if (isset($_POST['recherche'])) {
            $_SESSION['recherche'] = $_POST['recherche'];
            header("Location: {$_SERVER['REQUEST_URI']}");
            exit;
        }




    }



} else {
    header("Location: ../login.php");
}

function get_table($nom_table, $bdd){

    $query = $bdd->query('SELECT id,question,answer FROM '.$nom_table);
    // Définir le style de récupération à PDO::FETCH_ASSOC
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    return $result;
}

function get_nb_object($data_base,$start,$end){
    //renvoie les objets d'une table, de la premiere ligne donnée à la derniere comprise (la premiere ligne étant 1 et non 0)
    $result = [];
    for($i=$start-1;$i<$end;$i++){
        $result[] = $data_base[$i];
    }
    return $result;
}
function tab_keys($tab){
    //renvoie les keys d'un tableau
    $result=[];
    foreach ($tab as $key => $value){
        $result[]=$key;
    }
    return $result;
}
function tab_without_first_index($first_index,$tab,$key_modified){
    //renvoie le tableau de données sans les premiers éléments du tableau
    //si $key_modified === false, les keys n'ont pas été modifiées et commence à 0 et s'incrémente de 1 à chaque fois, donc $first_index doit etre l'index duu premier element à partir duquel on veut que notre tableau renvoie la valeur
    //si $key_modified === true, les keys ont été modifiées et $first_index doit alors être un tableau avec les premieres clées dont on ne veut pas
    $result = [];
    if($key_modified === false){
        for($i = $first_index; $i<count($tab); $i++){
            $result[]= $tab[$i];
        }
    } else {
        foreach($tab as $key => $value){
            if(!(in_array($key,$first_index))) $result[$key]=$value;
        }
    }
    return $result;
}

function modify_sql($table, $id, $colonne, $value, $bdd){
    // Prepare the SQL statement
    $query = $bdd->prepare("UPDATE $table SET $colonne = :value WHERE id = :id");

    // Bind parameters
    $query->bindParam(':value', $value);
    $query->bindParam(':id', $id);

    // Execute the query
    $query->execute();

    // Redirect back to the referring page
    header("Location: {$_SERVER['REQUEST_URI']}?change=success");
    exit; // Make sure to exit after redirection
}
function html_modify_popup($objet) {
    $name_id = tab_keys($objet)[0];
    $id = $objet[$name_id];
    $tab_without_id = tab_without_first_index([$name_id], $objet, true); ?>
    <!-- Modal for Modify -->
    <div class="modal fade" id="modifyModal<?php echo $id; ?>" tabindex="-1" aria-labelledby="modifyModalLabel<?php echo $id; ?>" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifyModalLabel<?php echo $id; ?>">Modifier l'objet</h5>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <p>L'objet d'id : <?php echo $id; ?></p>
                        <?php foreach ($tab_without_id as $key => $value) { ?>
                            <div class="form-group">
                                <label for="input<?php echo $id . $key; ?>"><?php echo $key; ?></label>
                                <input type="text" class="form-control" id="input<?php echo $id . $key; ?>" name="<?php echo $id . 'tab[' . $key . ']'; ?>" value="<?php echo html_entity_decode($value); ?>">
                            </div>
                        <?php } ?>
                        <input type="hidden" name="update" value="modify">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <input type="submit" class="btn btn-primary" value="Enregistrer les modifications">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php }
function suppr_sql($table, $id, $bdd) {

    $query = $bdd->prepare('DELETE FROM ' . $table . ' WHERE id = ?');
    $query->execute([$id]);
    echo $table;
    echo $id;
    header("Location: {$_SERVER['REQUEST_URI']}"); // Redirection pour éviter le rechargement du formulaire
}

function update_table($nom_table,$bdd){
    if(isset($_POST['update'])){
        $type_update = $_POST['update'];
        $id = $_POST['id'];
        if ($type_update=== 'delete'){
            if($_POST['delete']==='yes')
                suppr_sql($nom_table,$id,$bdd);
        }else if ($type_update === 'modify'){
            foreach($_POST[$id.'tab'] as $key => $value){
                modify_sql($nom_table,$id,$key,$value,$bdd);
            }
        }
    }
}
function afficher_tableau($donnees, $nom_table,$bdd) {
    ?>
    <style>
        .table {
            --bs-table-bg: transparent;
        }
        table.table-custom-alternative-row-color thead {
            background-color: var(--actuel-bouton-couleur);
        }
        table.table-custom-alternative-row-color tbody tr:nth-of-type(2n+1) {
            background-color: var(--actuel-footer-bas-couleur);
        }
        table.table-custom-alternative-row-color tbody tr:nth-of-type(2n) {
            background-color: var(--actuel-barre-recherche-couleur);
        }
    </style>
    <div class="table-responsive">
        <table class="table table-bordered table-custom-alternative-row-color">
            <thead>
            <tr>
                <?php
                if (!empty($donnees) && is_array($donnees)) {
                    $head = tab_keys($donnees[0]);
                    foreach ($head as $column) {
                        echo "<th scope='col'>".html_entity_decode($column)."</th>";
                    }
                    echo "<th scope='col'>Modification</th>";
                } else {
                    echo "<tr><td colspan='100%'>Aucune donnée trouvée pour la table '$nom_table'.</td></tr>";
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($donnees)) {
                foreach ($donnees as $objet) {
                    echo "<tr><th scope='row'>".html_entity_decode($objet[$head[0]])."</th>";
                    $banned_keys = [$head[0]];
                    $tab2 = tab_without_first_index($banned_keys, $objet, true);
                    foreach ($tab2 as $attribut) {
                        echo "<td>".html_entity_decode($attribut)."</td>";
                    }
                    $id = $objet[$head[0]];
                    echo "<td>
            <button type='button' class='btn btn-primary' onclick='openModifyModal($id)'>Modifier</button>
            <button type='button' class='btn btn-danger' onclick='openDeleteModal($id)'>Supprimer</button>
            </td></tr>";
                }
            }
            ?>

            </tbody>
        </table>
    </div>
    <?php
}
require "Admin/Structures/Head/headAdmin.php";

dataDelete();
dataChange();
addData();

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link rel="stylesheet" href="../../Design/Css/style.css">
<link rel="stylesheet" href="../Design/Css/home-admin.css">
<style>
    .popup-overlay{
        position : fixed;
        top:0;
        left: 0;
        right: 0;
        bottom:0;
        background: rgba(255,255,255,0.7);
        z-index:100;
        display:none;
    }

    .popup-overlay.openPopup{
        display: block !important;
    }

</style>
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php";?>
<div class="wrapper">

    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php";?>




<body data-bs-theme="light">

    <div class="main mt-5">
        <div class="container mt-5">
            <h2>Gestion des Captcha</h2>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <form method="POST" action="" class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Rechercher dans le tableau" name="recherche" id="recherche">
                    </form>
                </div>
            </div>


            <div class="modal fade" id="modifyModal" tabindex="-1" aria-labelledby="modifyModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modifyModalLabel">Modifier l'objet</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="">
                                <div id="modifyFormContent"></div>
                                <input type="hidden" name="update" value="modify">
                                <input type="hidden" id="modifyId" name="id" value="">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <input type="submit" class="btn btn-primary" value="Enregistrer les modifications">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir supprimer cet élément ?</p>
                        </div>
                        <div class="modal-footer">
                            <form method="POST" action="">
                                <input type="hidden" name="id" id="deleteId" value="">
                                <input type="hidden" name="delete" value="Oui">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>




            <?php
function html_tableau($nom_table,$bdd){

    update_table($nom_table,$bdd);

    $table_tableau = get_table($nom_table,$bdd);

    if(isset($_SESSION['recherche'])){     //fonctionnalité de recherche dans la table
        $recherche = $_SESSION['recherche'];
        $comparaison = " LIKE '%".$recherche."%'";

        $table_head = tab_keys($table_tableau[0]);
        $recherche_commande = ' WHERE '.$table_head[0].$comparaison;
        if(count($table_head)>1){
            $reste_colonnes = tab_without_first_index([$table_head[0]],$table_head,true);
            foreach($reste_colonnes as $colonne){
                $recherche_commande = $recherche_commande.' OR '.$colonne.$comparaison;
            }
        }
        $table_tableau = get_table($nom_table.$recherche_commande,$bdd);

    }

    $liste_tailles = [5,10,25,50,100];
    if(isset($_GET['taille_tableau'])){
        $taille = $_GET['taille_tableau'];
        if(!(in_array($taille,$liste_tailles))) $taille = $liste_tailles[0];
    } else {
        $taille = $liste_tailles[0];
    }
    //on regarde si une taille de page a été donnée, si non la taille de page par défaut est 1 (pour l'instant)

    $max_page = ceil(count($table_tableau)/$taille);
    //on calcul le numéro de la derniere page possible pour le tableau

    if(isset($_GET['n_page'])){
        $page = $_GET['n_page'];
        if($page>$max_page) $page = $max_page;
        else if($page<1) $page = 1;
    } else {
        $page = 1;
    }
    //on regarde si un numéro de page a été donné, si non le numéro de page par défaut est 1

    $debut_tableau = ($page-1)*$taille+1;
    $fin_tableau = $page*$taille;
    //on calcul quelle est la plage de ligne à afficher qui correspond pour le tableau avec une taille et le numéro de la page tous deux donnés

    if($fin_tableau>(count($table_tableau))) {
        $fin_tableau = count($table_tableau);
    }
    //si la derniere ligne calculée est trop grande, alors elle est modifiée pour correspondre à etre la derniere disponible dans le tableau de données

    $table_tableau_plage = get_nb_object($table_tableau,$debut_tableau,$fin_tableau);
    //plage de données à afficher dans le tableau

    afficher_tableau($table_tableau_plage,$nom_table,$bdd)?>


    <div class="row mx-3 justify-content-between">

            <div class="col-md-4 mt-2">
                <form method="GET" action="">
                <select class="form-select" name="taille_tableau" id="taille_tableau" onchange="this.form.submit()">
                    <?php foreach ($liste_tailles as $taille_propose) { ?>
                        <option value="<?php echo $taille_propose; ?>" <?php if (isset($_GET['taille_tableau']) && $_GET['taille_tableau'] == $taille_propose) echo "selected"; ?>>
                            <?php echo $taille_propose; ?>
                        </option>
                    <?php } ?>
                </select>
                </form>
            </div>

        <div class="col-md-4 text-center mt-2">
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                        <a class="page-link" href="captcha.php?taille_tableau=<?php echo $taille; ?>&n_page=<?php echo $page - 1; ?>">Précédent</a>
                    </li>
                    <li class="page-item disabled"><a class="page-link">Page <?php echo $page; ?> / <?php echo $max_page; ?></a></li>
                    <li class="page-item <?php if($page >= $max_page) echo 'disabled'; ?>">
                        <a class="page-link" href="captcha.php?taille_tableau=<?php echo $taille; ?>&n_page=<?php echo $page + 1; ?>">Suivant</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>


    <?php
}

html_tableau('captcha',$bdd);

?>
            <h3>Ajouter une nouvelle question</h3>
            <form action="" method="POST">
                <div class="form-row">
                    <div class="col-12 col-md-6 mb-3">
                        <label for="captcha_question">Poser une question</label>
                        <input type="text" class="form-control" id="captcha_question" name="captcha_question" placeholder="Saisissez votre question" required maxlength="128">
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label for="captcha_reponse">Réponse attendue</label>
                        <input type="text" class="form-control" id="captcha_reponse" name="captcha_answer" placeholder="Saisissez votre réponse" required maxlength="128">
                    </div>
                    <div class="col-lg-2 col-md-12 mb-3 align-self-end">
                        <button type="submit" name="newCaptcha" class="btn btn-primary btn-block">Créer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function togglePopup($id){
        let popup = document.querySelector($id);
        popup.classList.toggle("openPopup");
    }
</script>
<script>

    async function openModifyModal(id) {
        try {
            const response = await fetch(`databaseTreatment.php?id=${id}&table=captcha`);
            const data = await response.json();

            // Clear previous content
            const modifyFormContent = document.getElementById('modifyFormContent');
            modifyFormContent.innerHTML = '';

            // Fill the form with fetched data
            for (const [key, value] of Object.entries(data)) {
                const div = document.createElement('div');
                div.classList.add('form-group');
                const label = document.createElement('label');
                label.setAttribute('for', `input${key}`);
                label.textContent = key;
                const input = document.createElement('input');
                input.type = 'text';
                input.classList.add('form-control');
                input.id = `input${key}`;
                input.name = `${id}tab[${key}]`;
                input.value = value;
                div.appendChild(label);
                div.appendChild(input);
                modifyFormContent.appendChild(div);
            }

            // Set the hidden ID input value
            document.getElementById('modifyId').value = id;

            // Show the modal
            const modifyModal = new bootstrap.Modal(document.getElementById('modifyModal'));
            modifyModal.show();
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    function openDeleteModal(id) {
        document.getElementById('deleteId').value = id;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>
<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>
</body>

</html>