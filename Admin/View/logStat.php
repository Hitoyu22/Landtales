<?php

require "Structure/Bdd/config.php";

header('Content-Type: application/json');

$connections = [];
$popularPages = [];

try {
    $connectionsQuery = $bdd->query("
        SELECT DATE_FORMAT(log_datetime, '%d/%m/%Y') as log_date, COUNT(*) as connection_count
        FROM log
        WHERE log_type = 'Connexion' AND log_datetime >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
        GROUP BY log_date
    ");
    $connections = $connectionsQuery->fetchAll(PDO::FETCH_ASSOC);

    usort($connections, function($a, $b) {
        $dateA = DateTime::createFromFormat('d/m/Y', $a['log_date']);
        $dateB = DateTime::createFromFormat('d/m/Y', $b['log_date']);
        return $dateA <=> $dateB;
    });

    $popularPagesQuery = $bdd->query("
        SELECT p.page_name, COUNT(*) as visit_count
        FROM log l
        JOIN pages p ON l.idpage = p.id
        WHERE l.idpage NOT IN (1000, 1001)
        GROUP BY p.page_name
        ORDER BY visit_count DESC
    ");
    $popularPages = $popularPagesQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

echo json_encode([
    'connections' => $connections,
    'popularPages' => $popularPages
]);

?>
