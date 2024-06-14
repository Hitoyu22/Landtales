<?php
session_start();

require "Structure/Functions/function.php";

if (!isset($_SESSION['idclient'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = "Liste des utilisateurs du site";

require "Structure/Bdd/config.php";
$userId = $_SESSION['idclient'];
$pseudo = $_SESSION['pseudo'];
checkUserRole();

// Pagination
$usersPerPage = 15;
$totalUsers = $bdd->query('SELECT COUNT(*) FROM client WHERE visibility = 1 AND idrank != 2 AND permaBan != 1')->fetchColumn();
$totalPages = ceil($totalUsers / $usersPerPage);

$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Vérification de la validité de la page demandée
if ($page < 1 || $page > $totalPages) {
    header("Location: ?page=1"); // Rediriger vers la première page
    exit();
}

$offset = ($page - 1) * $usersPerPage;

$publicUserProfile = $bdd->prepare('SELECT * FROM client WHERE visibility = 1 AND idrank != 2 AND permaBan != 1 LIMIT :offset, :limit');
$publicUserProfile->bindParam(':offset', $offset, PDO::PARAM_INT);
$publicUserProfile->bindParam(':limit', $usersPerPage, PDO::PARAM_INT);
$publicUserProfile->execute();
$users = $publicUserProfile->fetchAll(PDO::FETCH_ASSOC);

require "Structure/Head/head.php";

searchNothing();

$theme = 'light';
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
<link rel="stylesheet" href="Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-front.css">

</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Structure/Navbar/navbar.php";?>
<div class="wrapper">
    <?php require "Structure/Sidebar/sidebar.php";?>
    <div class="main mt-5">

        <div class="container">
            <h1 class="mt-5 mb-3">Liste de l'ensemble des utilisateurs de Landtales</h1>

            <?php if (!empty($users)): ?>
                <div class="row">
                    <?php foreach ($users as $user):
                        $userIconPath = isset($user['profil_picture']) ? $user['profil_picture'] : "https://landtales.freeddns.org/Design/Pictures/banner_base.png";
                        ?>
                        <div class="col-12 mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="userProfil.php?id=<?php echo $user['id']; ?>">
                                    <div class="d-flex align-items-center">
                                        <div class="profile-img-container">
                                            <img src="<?php echo $userIconPath; ?>" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                        </div>
                                        <div class="ml-auto">
                                            <p class="text-center"><?php echo $user['pseudo']; ?></p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="mb-5">Il n'y a personne on dirait, vous êtes seul au monde.</p>
            <?php endif; ?>

            <div class="row justify-content-end">
                <div class="col-md-4 text-center mt-2">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php if($page <= 1) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Précédent</a>
                            </li>
                            <li class="page-item disabled">
                                <span class="page-link">Page <?php echo $page; ?> / <?php echo $totalPages; ?></span>
                            </li>
                            <li class="page-item <?php if($page >= $totalPages) echo 'disabled'; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="Structure/Functions/bootstrap.js"></script>
<script src="Structure/Functions/script.js"></script>
</body>
</html>
