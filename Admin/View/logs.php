<?php
require "Structure/Functions/function.php";
session_start();

if (isset($_SESSION['idclient'])) {
    $userId = $_SESSION['idclient'];
    checkAdminRole();

    $pageTitle = "Suivi des Logs de Landtales";

    require "Structure/Bdd/config.php";

} else {
    header("Location: ../login.php");
    exit;
}

//Collecte des différentes données en BDD

        $numberTravel = $bdd->prepare("SELECT COUNT(id) AS count FROM travel");
        $numberTravel->execute();
        $numberTravel = $numberTravel->fetch(PDO::FETCH_ASSOC);
        $travelCount = $numberTravel['count'];

        $numberCustomBought = $bdd->prepare("SELECT COUNT(idclient) AS count FROM client_customisation");
        $numberCustomBought->execute();
        $numberCustomBought = $numberCustomBought->fetch(PDO::FETCH_ASSOC);
        $customCount = $numberCustomBought['count'];

        $numberFriendship = $bdd->prepare("SELECT COUNT(accepted) AS count FROM friend WHERE accepted = 2");
        $numberFriendship->execute();
        $numberFriendship = $numberFriendship->fetch(PDO::FETCH_ASSOC);
        $friendshipCount = $numberFriendship['count'];

        $numberQuizDid = $bdd->prepare("SELECT COUNT(score) AS count FROM client_answer WHERE coin_added = 1");
        $numberQuizDid->execute();
        $numberQuizDid = $numberQuizDid->fetch(PDO::FETCH_ASSOC);
        $QuizDidCount = $numberQuizDid['count'];

        $numberTravelView = $bdd->prepare("SELECT COUNT(travel_view_date) AS count FROM travel_view");
        $numberTravelView->execute();
        $numberTravelView = $numberTravelView->fetch(PDO::FETCH_ASSOC);
        $travelViewCount = $numberTravelView['count'];

        $numberNewsletter = $bdd->prepare("SELECT COUNT(id) AS count FROM client WHERE news_letter_accepted = 1");
        $numberNewsletter->execute();
        $numberNewsletter = $numberNewsletter->fetch(PDO::FETCH_ASSOC);
        $newsletterCount = $numberNewsletter['count'];

        $numberClient = $bdd->prepare("SELECT COUNT(id) AS count FROM client");
        $numberClient->execute();
        $numberClient = $numberClient->fetch(PDO::FETCH_ASSOC);
        $clientCount = $numberClient['count'];




require "Admin/Structures/Head/headAdmin.php";
$theme = 'light'; // Thème par défaut
if (isset($_COOKIE['theme'])) {
    $theme = $_COOKIE['theme'];
}
?>

<link rel="stylesheet" href="../Design/Css/style.css">
<link rel="stylesheet" href="Design/Css/home-admin.css">
</head>
<body class="hidden" data-bs-theme="<?php echo $theme; ?>">
<?php require "Admin/Structures/Navbar/navbarAdmin.php"; ?>
<div class="wrapper">
    <?php require "Admin/Structures/Sidebar/sidebarAdmin.php"; ?>

    <div class="main mt-5">
        <div class="container mt-5">
            <h2 class="mb-5">Suivi des Logs de Landtales</h2>


            <div class="row">
                <div class="col-md-6">
                    <h6>Nombre de connexion sur 5 jours</h6>
                    <canvas id="lineChart" width="400" height="400"></canvas>
                    <ul id="lineLegend" class="legend"></ul>
                </div>
                <div class="col-md-6">
                    <h6>Nombres de visite sur les pages sur 7 jours</h6>
                    <canvas id="pieChart" width="400" height="400"></canvas>
                    <ul id="pieLegend" class="legend"></ul>
                </div>
            </div>

            <h5>Statistiques générales de Landtales</h5>
            <div class="table-responsive">
            <table class="table table-bordered table-custom-alternative-row-color">
                <thead>
                    <tr>
                        <th class="text-center" scope="col">Nombre de voyage</th>
                        <th class="text-center" scope="col">Nombre de customisation acheté</th>
                        <th class="text-center" scope="col">Nombre d'amitié</th>
                        <th class="text-center" scope="col">Nombre de Quiz réalisé</th>
                        <th class="text-center" scope="col">Nombre de vue sur des voyages</th>
                        <th class="text-center" scope="col">Nombre d'abonné(e)s à la Newsletter</th>
                        <th class="text-center" scope="col">Nombre de voyageurs</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center"><?php echo $travelCount;?></td>
                        <td class="text-center"><?php echo $customCount;?></td>
                        <td class="text-center"><?php echo $friendshipCount;?></td>
                        <td class="text-center"><?php echo $QuizDidCount; ?></td>
                        <td class="text-center"><?php echo $travelViewCount; ?></td>
                        <td class="text-center"><?php echo $newsletterCount ?></td>
                        <td class="text-center"><?php echo $clientCount; ?></td>

                    </tr>
                </tbody>

            </table>
            </div>

            <div class="mt-3">
                <h3>Liste des fichiers de logs</h3>
                <table class="logs-table table table-bordered table-custom-alternative-row-color">
                    <thead>
                    <tr>
                        <th scope="col">Nom du fichier</th>
                        <th scope="col">Date de modification</th>
                        <th scope="col">Télécharger</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $logDir = 'Admin/Structures/Logs'; // Utilisez le même chemin ici
                    $logs = scandir($logDir);
                    foreach ($logs as $log) {
                        if ($log !== '.' && $log !== '..') {
                            $filePath = $logDir . '/' . $log;
                            if (pathinfo($filePath, PATHINFO_EXTENSION) === 'txt') {
                                $modificationTime = date("d-m-Y H:i:s", filemtime($filePath));
                                echo '<tr>';
                                echo '<td>' . $log . '</td>';
                                echo '<td>' . $modificationTime . '</td>';
                                echo '<td><button class="btn-primary btn" onclick="downloadFile(\'' . urlencode($log) . '\')">Télécharger</button></td>';
                                echo '</tr>';
                            }
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>

    function downloadFile(fileName) {
        fetch(`downloadLogs.php?file=${fileName}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Une erreur est survenue');
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(new Blob([blob]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.parentNode.removeChild(link);
            })
            .catch(error => console.error('Une erreur est survenue :', error));
    }



    function drawPieChart(ctx, data) {
        const total = data.values.reduce((acc, val) => acc + val, 0);
        let startAngle = 0;

        data.values.forEach((value, index) => {
            const sliceAngle = (value / total) * 2 * Math.PI;
            ctx.beginPath();
            ctx.moveTo(200, 200); // Centre du camembert
            ctx.arc(200, 200, 200, startAngle, startAngle + sliceAngle);
            ctx.closePath();
            ctx.fillStyle = data.colors[index];
            ctx.fill();
            startAngle += sliceAngle;
        });
    }

    // Fonction pour dessiner le graphique en courbes avec grille et axes
    function drawLineChart(ctx, data) {
        const maxValue = Math.max(...data.values);
        const canvasHeight = 400;
        const canvasWidth = 400;
        const padding = 40;

        // Dessiner la grille et les axes
        ctx.beginPath();
        ctx.strokeStyle = '#939393';
        ctx.lineWidth = 1;

        // Lignes horizontales (ordonnées)
        for (let i = 0; i <= 5; i++) {
            let y = padding + i * (canvasHeight - 2 * padding) / 5;
            ctx.moveTo(padding, y);
            ctx.lineTo(canvasWidth - padding, y);
            ctx.strokeText((maxValue - i * maxValue / 5).toFixed(0), 5, y + 3);
        }

        // Lignes verticales (abscisses)
        for (let i = 0; i < data.labels.length; i++) {
            let x = padding + i * (canvasWidth - 2 * padding) / (data.labels.length - 1);
            ctx.moveTo(x, padding);
            ctx.lineTo(x, canvasHeight - padding);
            ctx.strokeText(data.labels[i], x - 10, canvasHeight - padding + 20);
        }

        ctx.stroke();
        ctx.closePath();

        // Dessiner la ligne
        ctx.beginPath();
        ctx.moveTo(padding, canvasHeight - padding - (data.values[0] / maxValue) * (canvasHeight - 2 * padding));

        data.values.forEach((value, index) => {
            const x = padding + (index * (canvasWidth - 2 * padding) / (data.values.length - 1));
            const y = canvasHeight - padding - (value / maxValue) * (canvasHeight - 2 * padding);
            ctx.lineTo(x, y);
        });

        // Utiliser la couleur du texte actuel comme couleur de la ligne
        ctx.strokeStyle = '#000000'; // Modifier cette ligne pour utiliser la couleur du texte actuel
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.closePath();

        // Dessiner les points
        data.values.forEach((value, index) => {
            const x = padding + (index * (canvasWidth - 2 * padding) / (data.values.length - 1));
            const y = canvasHeight - padding - (value / maxValue) * (canvasHeight - 2 * padding);
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, 2 * Math.PI);
            ctx.fillStyle = '#000000'; // Modifier cette ligne pour utiliser la couleur du texte actuel
            ctx.fill();
            ctx.closePath();
        });
    }

    // Fonction pour créer les légendes
    function createLegend(containerId, data) {
        const legendContainer = document.getElementById(containerId);
        data.labels.forEach((label, index) => {
            const legendItem = document.createElement('li');
            const colorBox = document.createElement('span');
            colorBox.className = 'color-box'; // Ajout d'une classe pour styler les carrés de couleur
            colorBox.style.backgroundColor = data.colors[index];
            legendItem.appendChild(colorBox);
            // Ajout du nom de la page et du nombre associé dans la légende
            legendItem.appendChild(document.createTextNode(label + ' - ' + data.values[index]));
            legendContainer.appendChild(legendItem);
        });
    }

    // Fonction pour afficher les informations sur le survol du graphique en camembert
    function handlePieHover(evt, ctx, data) {
        const mouseX = evt.offsetX;
        const mouseY = evt.offsetY;
        const x = 200;
        const y = 200;
        const radius = 200;
        const total = data.values.reduce((acc, val) => acc + val, 0);
        let startAngle = 0;

        for (let i = 0; i < data.values.length; i++) {
            const sliceAngle = (data.values[i] / total) * 2 * Math.PI;
            const endAngle = startAngle + sliceAngle;
            ctx.beginPath();
            ctx.moveTo(x, y);
            ctx.arc(x, y, radius, startAngle, endAngle);
            ctx.closePath();

            if (ctx.isPointInPath(mouseX, mouseY)) {
                const tooltip = document.getElementById('tooltip');
                tooltip.style.left = evt.pageX + 'px';
                tooltip.style.top = evt.pageY + 'px';
                tooltip.style.display = 'block';
                tooltip.textContent = data.labels[i];
                return;
            }

            startAngle += sliceAngle;
        }

        document.getElementById('tooltip').style.display = 'none';
    }

    // Fonction pour afficher les informations sur le survol du graphique en courbes
    function handleLineHover(evt, ctx, data) {
        const tooltip = document.getElementById('tooltip');
        if (!tooltip) return; // Vérifier si l'élément existe

        const mouseX = evt.offsetX;
        const mouseY = evt.offsetY;
        const canvasHeight = 400;
        const canvasWidth = 400;
        const padding = 40;
        const maxValue = Math.max(...data.values);

        data.values.forEach((value, index) => {
            const x = padding + (index * (canvasWidth - 2 * padding) / (data.values.length - 1));
            const y = canvasHeight - padding - (value / maxValue) * (canvasHeight - 2 * padding);

            if (Math.abs(mouseX - x) < 10 && Math.abs(mouseY - y) < 10) {
                tooltip.style.left = evt.pageX + 'px';
                tooltip.style.top = evt.pageY + 'px';
                tooltip.style.display = 'block';
                tooltip.textContent = data.labels[index];
                return;
            }
        });

        tooltip.style.display = 'none';
    }

    // Afficher les graphiques et les légendes après avoir récupéré les données
    async function fetchDataAndDrawCharts() {
        try {
            const response = await fetch('logStat.php');
            const data = await response.json();

            const connectionData = {
                labels: data.connections.map(item => item.log_date),
                values: data.connections.map(item => item.connection_count),
                colors: Array(data.connections.length).fill('rgba(54, 162, 235, 0.7)')
            };

            const popularPagesData = {
                labels: data.popularPages.map(item => item.page_name),
                values: data.popularPages.map(item => item.visit_count),
                colors: [
                    'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)', 'rgba(83, 102, 255, 0.7)',
                    'rgba(255, 99, 71, 0.7)', 'rgba(154, 205, 50, 0.7)',
                    'rgba(0, 191, 255, 0.7)', 'rgba(255, 69, 0, 0.7)'
                ]
            };

            const lineCtx = document.getElementById('lineChart').getContext('2d');
            drawLineChart(lineCtx, connectionData);
            createLegend('lineLegend', connectionData);

            const pieCtx = document.getElementById('pieChart').getContext('2d');
            drawPieChart(pieCtx, popularPagesData);
            createLegend('pieLegend', popularPagesData);

            document.getElementById('pieChart').addEventListener('mousemove', (evt) => {
                handlePieHover(evt, pieCtx, popularPagesData);
            });

            document.getElementById('lineChart').addEventListener('mousemove', (evt) => {
                handleLineHover(evt, lineCtx, connectionData);
            });
        } catch (error) {
            console.error('Error fetching data:', error);
        }
    }

    window.onload = fetchDataAndDrawCharts;

</script>
<script src="../Structure/Functions/bootstrap.js"></script>
<script src="../Structure/Functions/script.js"></script>
</body>
</html>
