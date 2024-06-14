<?php
require "Structure/Functions/function.php";

session_start();
if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];

    checkAdminRole();

    $pageTitle = "Gestion de la base de données";

    require "Structure/Bdd/config.php";

    $tableName = isset($_GET['table']) ? htmlspecialchars($_GET['table']) : 'client';

    switch ($tableName) {
        case 'client':
        case 'travel':
        case 'quiz':
        case 'friend':
        case 'travel_comment':
        case 'travel_like':
        case 'travel_view':
            break;
        default:
            $tableName = 'client'; // Valeur par défaut
            break;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['delete']) && $_POST['delete'] === 'Oui') {
            if (isset($_GET['table'])) {
                $tableName = $_GET['table'];
                $id = $_POST['id'];

                switch ($tableName) {
                    case 'travel':
                        deleteTravel($bdd, $id);
                        break;
                    case 'quiz':
                        deleteQuiz($bdd, $id);
                        break;
                    case 'friend':
                        list($idclient1, $idclient2) = explode('_', $id);
                        $deleteFriendship = $bdd->prepare("DELETE FROM friend WHERE ($idclient1 = ? AND $idclient2 = ?) OR ($idclient2 = ? AND $idclient1 = ?)");
                        $deleteFriendship->execute([$idclient1, $idclient2, $idclient1, $idclient2]);
                        break;
                    case 'travel_comment':
                        deleteComments($bdd, $id);
                        break;
                    case 'travel_view':
                        list($idtravel, $idclient, $travel_view_date) = explode('_', $id);
                        deleteView($bdd, $idclient, $idtravel, $travel_view_date);
                        break;
                    case 'travel_like':
                        list($idclient, $idtravel) = explode('_', $id);
                        deleteLike($bdd, $idclient, $idtravel);
                        break;
                    default:
                        break;
                }

                // Après la logique de suppression, rediriger vers la page actuelle
                $redirectUrl = $_SERVER['REQUEST_URI'];
                if (strpos($redirectUrl, '?') !== false) {
                    $redirectUrl .= "&delete=success";
                } else {
                    $redirectUrl .= "?delete=success";
                }
                header("Location: $redirectUrl");
                exit;
            }
        }

        if (isset($_POST['update']) && $_POST['update'] === 'modify') {
            $id = $_POST['id'];
            foreach ($_POST[$id . 'tab'] as $key => $value) {
                modify_sql($tableName, $id, $key, $value, $bdd);
            }
            $redirectUrl = $_SERVER['REQUEST_URI'];
            if (strpos($redirectUrl, '?') !== false) {
                $redirectUrl .= "&change=success";
            } else {
                $redirectUrl .= "?change=success";
            }
            header("Location: $redirectUrl");
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


    switch ($nom_table) {
        case 'client':
            $query = $bdd->query('SELECT id,pseudo,firstname,lastname,email,coin,mood,insta,twitter,facebook,github,youtube,summary FROM client');
            break;
        case 'travel':
            $query = $bdd->query('SELECT id,travel_date,idclient,summary,title,idtheme FROM travel');
            break;
        case 'quiz':
            $query = $bdd->query('SELECT id,title,potential_gain,universe,summary FROM quiz');
            break;
        case 'friend':
            $query = $bdd->query('SELECT idclient1, idclient2, accepted, encrypt_key FROM friend');
            break;
        case 'travel_comment':
            $query = $bdd->query('SELECT id,comment,travel_comment_date,idcomment FROM travel_comment');
            break;
        case 'travel_like':
            $query = $bdd->query('SELECT idtravel,idclient FROM travel_like');
            break;
        case 'travel_view':
            $query = $bdd->query('SELECT idclient,idtravel,travel_view_date FROM travel_view');
            break;
        default:
            throw new Exception('Table non reconnue: ' . $nom_table);
    }

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

function modify_sql($table,$id,$colonne,$value, $bdd){
    //on obtient la valeur de la table donnée en parametre
    //Il faudra modifier la fonction lors de son importation sur le site

    if(gettype($value)==="string"){
        $value = "'$value'";
    }
    $query = $bdd->prepare('UPDATE '.$table.' SET '.$colonne.' = '.$value.' WHERE id='.$id);
    $query ->execute();
    header("Location: {$_SERVER['REQUEST_URI']}");

    //$result = $query ->fetchAll();
}
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
                    $firstValue = $objet[$head[0]];
                    echo "<tr><th scope='row'>" . ($firstValue !== null ? html_entity_decode($firstValue) : "Aucun") . "</th>";
                    $banned_keys = [$head[0]];
                    $tab2 = tab_without_first_index($banned_keys, $objet, true);
                    foreach ($tab2 as $attribut) {
                        echo "<td>" . ($attribut !== null ? html_entity_decode($attribut) : "Aucun") . "</td>";
                    }

                    // Concaténer les IDs primaires en fonction de la table
                    $id = '';
                    switch ($nom_table) {
                        case 'friend':
                            $id = $objet['idclient1'] . '_' . $objet['idclient2'];
                            break;
                        case 'travel_like':
                            $id = $objet['idclient'] . '_' . $objet['idtravel'];
                            break;
                        case 'travel_view':
                            $id = $objet['idtravel'] . '_' . $objet['idclient'] . '_' . $objet['travel_view_date'];
                            break;
                        default:
                            $id = $objet[$head[0]];
                            break;
                    }

                    // Vérification si la table est 'client'
                    if ($nom_table !== 'client') {
                        echo "<td>
                <button type='button' class='btn btn-primary' onclick='openModifyModal(\"$id\")'>Modifier</button>
                <button type='button' class='btn btn-danger' onclick='openDeleteModal(\"$id\")'>Supprimer</button>
                </td>";
                    } else {
                        echo "<td>
                <button type='button' class='btn btn-primary' onclick='openModifyModal(\"$id\")'>Modifier</button>
                </td>";
                    }
                    echo "</tr>";
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
            <h2 class="mb-3">Gestion de la base de données</h2>

            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <form method="GET" action="" class="input-group">
                            <label class="input-group-text" for="tableSelect">Sélectionner une table</label>
                            <select class="form-select" id="tableSelect" name="table" onchange="this.form.submit()">
                                <option value="client" <?php if ($tableName === 'client') echo 'selected'; ?>>Utilisateur</option>
                                <option value="travel" <?php if ($tableName === 'travel') echo 'selected'; ?>>Voyage</option>
                                <option value="quiz" <?php if ($tableName === 'quiz') echo 'selected'; ?>>Quiz</option>
                                <option value="friend" <?php if ($tableName === 'friend') echo 'selected'; ?>>Ami</option>
                                <option value="travel_comment" <?php if ($tableName === 'travel_comment') echo 'selected'; ?>>Commentaires</option>
                                <option value="travel_like" <?php if ($tableName === 'travel_like') echo 'selected'; ?>>Likes sur voyage</option>
                                <option value="travel_view" <?php if ($tableName === 'travel_view') echo 'selected'; ?>>Vues sur voyage</option>
                            </select>
                            <?php if (isset($_GET['taille_tableau'])) { ?>
                                <input type="hidden" name="taille_tableau" value="<?php echo $_GET['taille_tableau']; ?>">
                            <?php } ?>
                            <?php if (isset($_GET['n_page'])) { ?>
                                <input type="hidden" name="n_page" value="<?php echo $_GET['n_page']; ?>">
                            <?php } ?>
                        </form>
                    </div>

                    <div class="col-md-6 d-flex justify-content-end">
                        <form method="POST" action="" class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Rechercher dans le tableau" name="recherche" id="recherche">
                        </form>
                    </div>
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
                            <?php if (isset($_GET['table'])) { ?>
                                <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
                            <?php } ?>
                            <?php if (isset($_GET['n_page'])) { ?>
                                <input type="hidden" name="n_page" value="<?php echo $_GET['n_page']; ?>">
                            <?php } ?>
                        </form>
                    </div>

                    <div class="col-md-4 text-center mt-2">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                                    <a class="page-link" href="databaseGestion.php?taille_tableau=<?php echo $taille; ?>&n_page=<?php echo $page - 1; ?><?php if (isset($_GET['table'])) echo '&table=' . $_GET['table']; ?>">Précédent</a>
                                </li>
                                <li class="page-item disabled"><a class="page-link">Page <?php echo $page; ?> / <?php echo $max_page; ?></a></li>
                                <li class="page-item <?php if($page >= $max_page) echo 'disabled'; ?>">
                                    <a class="page-link" href="databaseGestion.php?taille_tableau=<?php echo $taille; ?>&n_page=<?php echo $page + 1; ?><?php if (isset($_GET['table'])) echo '&table=' . $_GET['table']; ?>">Suivant</a>
                                </li>
                            </ul>
                        </nav>
                    </div>

                </div>


                <?php
            }

            html_tableau($tableName,$bdd);

            ?>
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
            const response = await fetch(`databaseTreatment.php?id=${id}&table=<?php echo $tableName; ?>`);
            const data = await response.json();

            const modifyFormContent = document.getElementById('modifyFormContent');
            modifyFormContent.innerHTML = '';

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

            document.getElementById('modifyId').value = id;

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