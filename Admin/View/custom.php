<?php
session_start();

require "Structure/Functions/function.php";
require "Structure/Functions/alerts.php";

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkAdminRole();

    $pageTitle = "Customisation de profil";

    require "Structure/Bdd/config.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['newCustom'])) {
            $name = htmlspecialchars($_POST['name']);
            $cost = htmlspecialchars($_POST['cost']);
            $promoCode = !empty($_POST['promoCode']) ? htmlspecialchars($_POST['promoCode']) : null;
            $promoEndDate = !empty($_POST['promoEndDate']) ? htmlspecialchars($_POST['promoEndDate']) : null;

            $insertCustom = $bdd->prepare("INSERT INTO customisation (picture_name, price, promo_code, end_date_code) VALUES (?, ?, ?, ?)");
            $insertCustom->execute([$name, $cost, $promoCode, $promoEndDate]);
            $customId = $bdd->lastInsertId();

            $targetDir = $_SERVER['DOCUMENT_ROOT'] . "/Ressources/Custom/$customId/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = basename($_FILES["customImage"]["name"]);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["customImage"]["tmp_name"], $targetFile)) {
                $customURL = "https://landtales.freeddns.org/Ressources/Custom/$customId/" . $fileName;

                $updateCustom = $bdd->prepare("UPDATE customisation SET picture = ? WHERE id = ?");
                $updateCustom->execute([$customURL, $customId]);
            }

            header("Location: custom.php?add=success");
            exit;
        }

        if (isset($_POST['change'])) {
            $customId = $_POST['id'];

            $name = htmlspecialchars($_POST['name']);
            $cost = htmlspecialchars($_POST['cost']);
            $promoCode = !empty($_POST['promoCode']) ? htmlspecialchars($_POST['promoCode']) : null;
            $promoEndDate = !empty($_POST['promoEndDate']) ? htmlspecialchars($_POST['promoEndDate']) : null;

            $updateCustom = $bdd->prepare("UPDATE customisation SET picture_name = ?, price = ?, promo_code = ?, end_date_code = ? WHERE id = ?");
            $updateCustom->execute([$name, $cost, $promoCode, $promoEndDate, $customId]);

            header("Location: custom.php?change=success");
            exit;
        }

        if (isset($_POST['delete'])) {
            $customId = $_POST['id'];

            $updateUsers = $bdd->prepare("UPDATE client SET idcustomisation = NULL WHERE idcustomisation = ?");
            $updateUsers->execute([$customId]);

            $deleteBuy = $bdd->prepare("DELETE FROM client_customisation WHERE idcustomisation = ?");
            $deleteBuy->execute([$customId]);

            $selectImage = $bdd->prepare("SELECT picture FROM customisation WHERE id = ?");
            $selectImage->execute([$customId]);
            $custom = $selectImage->fetch();

            if ($custom) {
                $customPath = $_SERVER['DOCUMENT_ROOT'] . parse_url($custom['picture'], PHP_URL_PATH);

                if (file_exists($customPath)) {
                    unlink($customPath);
                    rmdir(dirname($customPath));
                }

                $deleteCustom = $bdd->prepare("DELETE FROM customisation WHERE id = ?");
                $deleteCustom->execute([$customId]);

                header("Location: custom.php?delete=success");
                exit;
            }
        }
    }
}

require "Admin/Structures/Head/headAdmin.php";

dataDelete();
dataChange();
addData();

$theme = 'light';
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>
<link rel="stylesheet" href="../../Design/Css/style.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php"; ?>
<div class="wrapper">
    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php"; ?>
    <div class="main mt-5 ">
        <div class="container mt-5">
            <div class="col-12">
                <h2 class="mb-4">Ajouter une nouvelle customisation</h2>
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="form-group mb-4 col-md-6 px-1">
                            <label for="customizationName" class="col-12">Nom de la customisation :</label>
                            <input type="text" class="form-control" id="customizationName" name="name" required>
                        </div>
                        <div class="form-group mb-4 col-md-6 px-1">
                            <label for="virtualCost" class="col-12">Prix :</label>
                            <input type="number" class="form-control" id="virtualCost" name="cost" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group mb-4 col-md-6 px-1">
                            <label for="promoCode" class="col-12">Code promo (facultatif) :</label>
                            <input type="text" class="form-control" id="promoCode" name="promoCode">
                        </div>
                        <div class="form-group mb-4 col-md-6 px-1">
                            <label for="promoEndDate">Date de fin du code :</label>
                            <input type="date" class="form-control" id="promoEndDate" name="promoEndDate">
                        </div>
                    </div>
                    <div class="form-group mb-5">
                        <label for="customImage" class="col-12">Importer une image (500x500 max, 500ko max) :</label><br>
                        <input type="file" class="form-control-file" id="customImage" name="customImage" accept="image/*" required>
                        <img id="previewImage" src="#" alt="Image sélectionnée" style="display:none; max-width: 200px; max-height: 200px;"/>
                    </div>
                    <button type="submit" name="newCustom" class="btn btn-primary">Ajouter</button>
                </form>
            </div>
            <div class="col-12">
                <h3>Liste des Customisations</h3>
                <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Code Promo</th>
                        <th>Date Fin du Code</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $query = "SELECT picture, picture_name, price, id, promo_code, end_date_code FROM customisation";
                    $stmt = $bdd->prepare($query);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>";
                            echo "<td><img src='" . str_replace('&#039;', "'",$row['picture']) . "' style='width:100px; height:100px;'></td>";
                            echo "<td>" . str_replace('&#039;', "'",$row['picture_name']) . "</td>";
                            echo "<td>" . str_replace('&#039;', "'",$row['price']) . "</td>";
                            echo "<td>" . (!empty($row['promo_code']) ? str_replace('&#039;', "'",$row['promo_code']) : "Aucun") . "</td>";
                            echo "<td>" . (!empty($row['end_date_code']) ? formatFrenchDat(str_replace('&#039;', "'",$row['end_date_code'])) : "Aucune") . "</td>";
                            echo "<td>";
                            echo '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-custom-id="' . $row['id'] . '"
                                    data-name="' . $row['picture_name'] . '"
                                    data-cost="' . $row['price'] . '"
                                    data-promo-code="' . $row['promo_code'] . '"
                                    data-end-date="' . $row['end_date_code'] . '">Modifier</button>';
                            echo '<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-custom-id="' . $row['id'] . '">Supprimer</button>';
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>Aucune customisation trouvée</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
                </div>
            </div>
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="" method="post">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Modifier la customisation</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" id="editCustomId">
                                <div class="mb-3">
                                    <label for="editName" class="form-label">Nom de la customisation :</label>
                                    <input type="text" class="form-control" id="editName" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editCost" class="form-label">Prix :</label>
                                    <input type="number" class="form-control" id="editCost" name="cost" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editPromoCode" class="form-label">Code promo (facultatif) :</label>
                                    <input type="text" class="form-control" id="editPromoCode" name="promoCode">
                                </div>
                                <div class="mb-3">
                                    <label for="editEndDate" class="form-label">Date de fin du code :</label>
                                    <input type="date" class="form-control" id="editEndDate" name="promoEndDate">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="change" class="btn btn-primary">Enregistrer les modifications</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="" method="post">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Êtes-vous sûr de vouloir supprimer cette customisation ?
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="id" id="deleteCustomId">
                                <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../Structure/Functions/bootstrap.js"></script>
    <script src="../Structure/Functions/script.js"></script>
    <script src="Structures/Functions/admin.js"></script>
</body>
</html>
