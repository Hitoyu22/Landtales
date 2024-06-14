<?php
require "Structure/Bdd/config.php";
session_start();

header('Content-Type: application/json'); // Ensure the content type is JSON

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
} else {
    echo json_encode(['error' => 'Erreur dans la récupération de l\'administrateur']);
    exit;
}

try {
    // Tickets assignés mais non réalisés, triés par date limite et priorité
    $assignedNotCompleted = $bdd->prepare("
        SELECT id, title, target_resolution_date, priority 
        FROM ticket 
        WHERE idassigned = ? AND ticket_status = 0 
        ORDER BY target_resolution_date ASC, priority DESC
    ");
    $assignedNotCompleted->execute([$userId]);
    $assignedNotCompletedTickets = $assignedNotCompleted->fetchAll(PDO::FETCH_ASSOC);

    // Tickets réalisés, triés par date limite et priorité
    $assignedCompleted = $bdd->prepare("
        SELECT id, title, target_resolution_date, priority 
        FROM ticket 
        WHERE idassigned = ? AND ticket_status = 1 
        ORDER BY target_resolution_date ASC, priority DESC
    ");
    $assignedCompleted->execute([$userId]);
    $assignedCompletedTickets = $assignedCompleted->fetchAll(PDO::FETCH_ASSOC);

    // Tickets non attribués
    $notAssigned = $bdd->prepare("
        SELECT id, title, target_resolution_date, priority 
        FROM ticket 
        WHERE idassigned != ? OR idassigned IS NULL 
    ");
    $notAssigned->execute([$userId]);
    $notAssignedTickets = $notAssigned->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'assignedNotCompleted' => $assignedNotCompletedTickets,
        'assignedCompleted' => $assignedCompletedTickets,
        'notAssigned' => $notAssignedTickets
    ];

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
