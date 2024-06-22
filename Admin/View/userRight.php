<?php
require "Structure/Functions/function.php";
require "Structure/Functions/alerts.php";

session_start();
    if (isset($_SESSION['idclient'])) {
        $userId = $_SESSION['idclient'];

        checkAdminRole();

        $pageTitle = "Gestion des droits des utilisateur";

        require "Structure/Bdd/config.php";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (isset($_POST['banUntilDate']) && isset($_POST['id'])) {
                // Bannissement temporaire
                $id = $_POST['id'];
                $banUntilDate = $_POST['banUntilDate'];
                $sql = "UPDATE client SET tempBan = ? WHERE id = ?";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([$banUntilDate, $id]);

                header("Location: userRight.php?tempBan=success&date=$banUntilDate");

            } elseif (isset($_POST['permBanId'])) {
                $userid = $_POST['permBanId'];
                $sql = "UPDATE client SET permaBan = ?, tempBan = ?, idrank = 1 WHERE id = ?";
                $stmt = $bdd->prepare($sql);
                $stmt->execute([1, null, $userid]);

                deleteUser($bdd,$userId);

                header("Location: userRight.php?permaBan=success");
                exit();


            } else if (isset($_POST['update']) && $_POST['update'] === 'modify') {
                $id = $_POST['id'];
                foreach ($_POST[$id . 'tab'] as $key => $value) {
                    if ($key == 'idrank') { // Vérifie si la colonne à modifier est 'rank'
                        $rankValues = [1 => 1, 3 => 3, 2 => 2];
                        if (array_key_exists($value, $rankValues)) {
                            $value = $rankValues[$value]; // Convertit la valeur textuelle en valeur numérique
                        }
                    }
                    modify_sql('client', $id, $key, $value,$bdd); // Appelle la fonction modify_sql avec la valeur potentiellement convertie
                }
                header("Location: userRight.php?change=success"); // Redirection pour éviter le rechargement du formulaire
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

    $query = 'SELECT id, pseudo, idrank, tempBan, permaBan FROM ' . $nom_table;
    $stmt = $bdd->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                                <input type="text" class="form-control" id="input<?php echo $id . $key; ?>" name="<?php echo $id . 'tab[' . $key . ']'; ?>" value="<?php echo str_replace('&#039;', "'", $value); ?>">
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
    <div class="table-responsive">
        <table class="table table-bordered table-custom-alternative-row-color">
            <thead>
            <tr>
                <?php
                if (!empty($donnees) && is_array($donnees)) {
                    $head = tab_keys($donnees[0]);
                    foreach ($head as $column) {
                        echo "<th scope='col'>".str_replace('&#039;', "'", $column)."</th>";
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
                    echo "<tr><th scope='row'>" . str_replace('&#039;', "'", $objet[$head[0]]) . "</th>";
                    $banned_keys = [$head[0]];
                    $tab2 = tab_without_first_index($banned_keys, $objet, true);
                    foreach ($tab2 as $key => $attribut) {
                        if ($key === 'idrank') {
                            // Switch pour afficher le rôle en texte
                            switch ($attribut) {
                                case 1:
                                    $role = "Utilisateur";
                                    break;
                                case 2:
                                    $role = "Administrateur";
                                    break;
                                case 3:
                                    $role = "Modérateur";
                                    break;
                                default:
                                    $role = "Inconnu";
                            }
                            echo "<td>" . str_replace('&#039;', "'", $role) . "</td>";
                        } else if ($key === 'tempBan') {
                            // Affichage de la date de bannissement ou "aucun" si null
                            $banValue = !is_null($attribut) ? formatFrenchDate($attribut) : "Aucun";
                            echo "<td>" . str_replace('&#039;', "'", $banValue) . "</td>";
                        } else if ($key == 'permaBan') {
                            if ($attribut == 1) {
                                echo "<td>Oui</td>";
                            } else {
                                echo "<td>Non</td>";
                            }
                            $permaBan = $attribut;
                        } else {
                            echo "<td>" . str_replace('&#039;', "'", $attribut) . "</td>";
                        }
                    }
                    $id = $objet[$head[0]];

                    echo "<td>";
                    if ($permaBan == 0) {
                        echo "<button type='button' class='btn btn-primary' onclick='openModifyModalUser($id)'>Modifier</button>
                  <button type='button' class='btn btn-warning' onclick='openTempBanModal($id)'>Bannir temporairement</button>
                  <button type='button' class='btn btn-danger' onclick='openPermBanModal($id)'>Bannir définitivement</button>";
                    }
                    echo "</td></tr>";
                }
            }
            ?>

            </tbody>
        </table>
    </div>
    <?php
}
require "Admin/Structures/Head/headAdmin.php";

dataChange();
permaBanAlert();
tempBanAlert();

$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link rel="stylesheet" href="../../Design/Css/style.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php";?>
<div class="wrapper">

    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php";?>


    <body data-bs-theme="light">

    <div class="main mt-5">
        <div class="container mt-5">
            <h2>Gestion des droits des utilisateurs</h2>

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
                            <h5 class="modal-title" id="modifyModalLabel">Modifier les droits d'utilisateurs</h5>
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

            <div class="modal fade" id="tempBanModal" tabindex="-1" aria-labelledby="tempBanModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="tempBanModalLabel">Bannir temporairement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="">
                                <input type="hidden" name="id" id="tempBanId" value="">
                                <div class="mb-3">
                                    <label for="banUntilDate" class="form-label">Date de fin de bannissement</label>
                                    <input type="date" class="form-control" id="banUntilDate" name="banUntilDate" required>
                                </div>
                                <button type="submit" class="btn btn-warning">Bannir temporairement</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="permBanModal" tabindex="-1" aria-labelledby="permBanModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="permBanModalLabel">Bannir définitivement</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Êtes-vous sûr de vouloir bannir cet utilisateur définitivement ? Cette action sera irreversible.</p>
                        </div>
                        <div class="modal-footer">
                            <form method="POST" action="">
                                <input type="hidden" name="permBanId" id="permBanId" value="">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                <button type="submit" class="btn btn-danger">Bannir définitivement</button>
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
                                    <a class="page-link" href="userRight.php?taille_tableau=<?php echo $taille; ?>&n_page=<?php echo $page - 1; ?>">Précédent</a>
                                </li>
                                <li class="page-item disabled"><a class="page-link">Page <?php echo $page; ?> / <?php echo $max_page; ?></a></li>
                                <li class="page-item <?php if($page >= $max_page) echo 'disabled'; ?>">
                                    <a class="page-link" href="userRight.php?taille_tableau=<?php echo $taille; ?>&n_page=<?php echo $page + 1; ?>">Suivant</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>


                <?php
            }

            html_tableau('client',$bdd);

            ?>

        </div>
    </div>
</div>
<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>
<script src="Structures/Functions/admin.js"></script>

</body>

</html>