<?php

//Affichage des dates en français
function formatFrenchDate($date) {

    $mois_fr = array(
        1 => "janvier",
        2 => "février",
        3 => "mars",
        4 => "avril",
        5 => "mai",
        6 => "juin",
        7 => "juillet",
        8 => "août",
        9 => "septembre",
        10 => "octobre",
        11 => "novembre",
        12 => "décembre"
    );


    $dateTime = new DateTime($date);

    $num_mois = $dateTime->format('n');

    $jour = $dateTime->format('d');
    $annee = $dateTime->format('Y');

    $frenchDate = $jour . " " . $mois_fr[$num_mois] . " " . $annee;


    return $frenchDate;
}

//Conversion du json (EditorJS) en html pour la newsletter
function convertEditorJsToHtml($data) {
    $html = '';
    foreach ($data['blocks'] as $block) {
        switch ($block['type']) {
            case 'header':
                $html .= "<h{$block['data']['level']}>" . $block['data']['text'] . "</h{$block['data']['level']}>";
                break;
            case 'paragraph':
                $html .= "<p>" . $block['data']['text'] . "</p>";
                break;
            case 'image':

                if (isset($block['data']['file']['url'])) {
                    $html .= "<img src=\"{$block['data']['file']['url']}\" alt=\"{$block['data']['caption']}\">";
                } else {

                    $html .= "<p>Image manquante</p>";
                }
                break;
            case 'list':
                $listType = $block['data']['style'] === 'unordered' ? 'ul' : 'ol';
                $html .= "<$listType>";
                foreach ($block['data']['items'] as $item) {
                    $html .= "<li>{$item}</li>";
                }
                $html .= "</$listType>";
                break;
        }
    }
    return $html;
}

//Fonction PHPMailer pour l'envoi de mail automatique
function smtpmailer($to, $from, $from_name, $subject, $body)
{
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;

    $mail->SMTPSecure = 'ssl';
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->Username = 'landtales.website@gmail.com';
    $mail->Password = 'Secret';

    $mail->IsHTML(true);
    $mail->From = "landtales.website@gmail.com";
    $mail->FromName = $from_name;
    $mail->Sender = $from;
    $mail->AddReplyTo($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);

    $mail->CharSet = 'UTF-8';

    if (!$mail->Send()) {
        $error = "Please try Later, Error Occured while Processing...";
        return $error;
    } else {
        return true;
    }

}

//Fonction de vérification de la possession ou non d'une customisation
function processCustomisationPurchase($userId, $idCustom, $cost, $coins, $picture = null) {
    global $bdd;
    $checkPurchase = $bdd->prepare("SELECT 1 FROM client_customisation WHERE idcustomisation = ? AND idclient = ?");
    $checkPurchase->execute([$idCustom, $userId]);
    $isPurchased = $checkPurchase->fetchColumn();

    if (!$isPurchased) {
        if ($coins >= $cost) {
            $bdd->beginTransaction();
            try {
                $newCustomBuy = $bdd->prepare("INSERT INTO client_customisation (idclient, idcustomisation, purchase_date) VALUES (?,?, NOW())");
                $newCustomBuy->execute([$userId, $idCustom]);
                $updateCoins = $bdd->prepare("UPDATE client SET coin = coin - ? WHERE id = ?");
                $updateCoins->execute([$cost, $userId]);
                $bdd->commit();

                $encodedImageUrl = $picture ? urlencode($picture) : '';
                header("Location: {$_SERVER['REQUEST_URI']}?promo=success&image=$encodedImageUrl");
            } catch (Exception $e) {
                $bdd->rollBack();
                header("Location: {$_SERVER['REQUEST_URI']}?promo=error");
            }
        } else {
            header("Location: {$_SERVER['REQUEST_URI']}?promo=insufficient_funds");
        }
    } else {
        header("Location: {$_SERVER['REQUEST_URI']}?promo=already_purchased");
    }
    exit();
}


function captureLogInfo($userId, $pageId, $pseudo, $logType) {
    $time = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $method = $_SERVER['REQUEST_METHOD'];  // Méthode HTTP comme GET ou POST
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    $logData = [
        'timestamp' => $time,
        'pseudo' => $pseudo,
        'userId' => $userId,
        'pageId' => $pageId,
        'logType' => $logType,
        'ip' => $ip,
        'method' => $method,
        'userAgent' => $userAgent
    ];

    return $logData;
}


function writeToFile($logData, $logPath) {
    $logMessage = implode(" - ", $logData) . "\n";
    file_put_contents($logPath, $logMessage, FILE_APPEND);
}
function saveToDatabase($logData) {
    global $bdd; // Assurez-vous que votre variable $bdd est correctement configurée et accessible
    $insertLog = $bdd->prepare("INSERT INTO log (log_datetime, idclient, idpage, log_type, ipclient, pseudoclient, method, userAgent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $insertLog->execute([
        $logData['timestamp'],
        $logData['userId'],
        $logData['pageId'],
        $logData['logType'],
        $logData['ip'],
        $logData['pseudo'],
        $logData['method'],
        $logData['userAgent']
    ]);
}

function logActivity($userId, $pseudo, $pageId, $logType, $logPath) {

    if (!$userId || !$pseudo || !$pageId || !$logType || !$logPath) {
        error_log("Information manquante pour la fonction logActivity");
        return;
    }

    $userIdEncoded = urlencode($userId);
    $pageIdEncoded = urlencode($pageId);
    $cookieKey = 'log_' . $userIdEncoded . '_' . $pageIdEncoded;

    $lastVisit = $_COOKIE[$cookieKey] ?? null;
    $currentTime = time();
    $interval = 60;

    if ($lastVisit === null || ($currentTime - $lastVisit) > $interval) {
        $logData = captureLogInfo($userId, $pageId, $pseudo, $logType);
        writeToFile($logData, $logPath);
        saveToDatabase($logData);
        setcookie($cookieKey, $currentTime, $currentTime + 86400, "/");
    }
}


function checkUserRole() {

    if(isset($_SESSION['rank'])) {

        $userRank = $_SESSION['rank'];

        if($userRank !=1 && $userRank != 3) {

            header("Location: Admin/homeBack.php");
            exit();
        }
    } else {

        header("Location: votre_page_de_connexion.php");
        exit();
    }
}

function checkAdminRole() {
    if(isset($_SESSION['rank'])) {

        $userRank = $_SESSION['rank'];


        if($userRank !=2) {

            header("Location: ../homeFront.php");
            exit();
        }

    } else {
        header("Location: votre_page_de_connexion.php");
        exit();
    }
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

function decodeHtmlEntities($array) {
    foreach ($array as $key => $value) {
        if (is_string($value)) {
            $array[$key] = html_entity_decode($value);
        }
    }
    return $array;
}

function executeQuery($bdd, $sql, $params, $successMessage) {
    $stmt = $bdd->prepare($sql);
    $stmt->execute($params);
    echo $successMessage . "<br>";
}

function deleteComments($bdd, $parentId) {
    $findSubComments = $bdd->prepare('SELECT id FROM travel_comment WHERE idcomment = ?');
    $findSubComments->execute([$parentId]);

    while ($subComment = $findSubComments->fetch(PDO::FETCH_ASSOC)) {
        deleteComments($bdd, $subComment['id']);
    }

    $deleteComment = $bdd->prepare('DELETE FROM travel_comment WHERE id = ?');
    $deleteComment->execute([$parentId]);
}
function deleteUser($bdd, $userId) {
    try {
        $bdd->beginTransaction();

        executeQuery($bdd, "UPDATE ticket SET idsubmitter = NULL WHERE idsubmitter = ?", [$userId], "Tickets mis à jour avec succès (idsubmitter).");
        executeQuery($bdd, "UPDATE ticket SET idassigned = NULL WHERE idassigned = ?", [$userId], "Tickets mis à jour avec succès (idassigned).");

        executeQuery($bdd, "DELETE FROM client_customisation WHERE idclient = ?", [$userId], "Personnalisation du client supprimée avec succès.");
        executeQuery($bdd, "DELETE FROM follower WHERE idclientfollowed = ? OR idclientfollower = ?", [$userId, $userId], "Relations de suivi supprimées avec succès.");
        executeQuery($bdd, "DELETE FROM friend WHERE idclient1 = ? OR idclient2 = ?", [$userId, $userId], "Amis supprimés avec succès.");
        executeQuery($bdd, "DELETE FROM message WHERE idclientfollowed = ? OR idclientfollower = ?", [$userId, $userId], "Messages supprimés avec succès.");
        executeQuery($bdd, "DELETE FROM log WHERE idclient = ?", [$userId], "Logs supprimés avec succès.");
        executeQuery($bdd, "DELETE FROM captcha WHERE idcreator = ?", [$userId], "Captcha supprimé avec succès.");
        executeQuery($bdd, "DELETE FROM client_answer WHERE idclient = ?", [$userId], "Réponses client supprimées avec succès.");



        $findMainComments = $bdd->prepare('SELECT id FROM travel_comment WHERE idclient = ?');
        $findMainComments->execute([$userId]);

        while ($mainComment = $findMainComments->fetch(PDO::FETCH_ASSOC)) {
            deleteComments($bdd, $mainComment['id']);
        }

        $recupIdTravel = $bdd->prepare("SELECT * FROM travel WHERE idclient = ?");
        $recupIdTravel->execute([$userId]);
        $travelId = $recupIdTravel->fetchAll();

        foreach ($travelId as $travel) {
            executeQuery($bdd, "DELETE FROM travel_view WHERE idtravel = ?", [$travel['id']], "Vues de voyage supprimées avec succès.");
            executeQuery($bdd, "DELETE FROM travel_like WHERE idtravel = ?", [$travel['id']], "Likes de voyage supprimés avec succès.");
            executeQuery($bdd, "DELETE FROM travel_comment WHERE idtravel = ?", [$travel['id']], "Commentaires de voyage supprimés avec succès.");
            executeQuery($bdd, "DELETE FROM travel WHERE id = ?", [$travel['id']], "Voyage supprimé avec succès.");

            $directoryPath = 'Ressources/Travel/' . $travel['id'];
            deleteDirectory($directoryPath);
            echo "Répertoire de voyage supprimé avec succès.<br>";
        }

        executeQuery($bdd, "DELETE FROM travel_like WHERE idclient = ?", [$userId], "Likes sur voyages aimés supprimés avec succès.");
        executeQuery($bdd, "DELETE FROM travel_view WHERE idclient = ?", [$userId], "Vues de voyage supprimées avec succès.");

        $directoryPath = 'Ressources/User/' . $userId;
        deleteDirectory($directoryPath);
        echo "Répertoire utilisateur supprimé avec succès.<br>";

        $bdd->commit();
        ;
    } catch (Exception $e) {
        $bdd->rollBack();
        echo "Erreur lors de la suppression des données : " . $e->getMessage();
    }
}

function deleteTravel($bdd, $travelIdToDelete) {
    try {

        $findMainComments = $bdd->prepare('SELECT id FROM travel_comment WHERE idtravel = ?');
        $findMainComments->execute([$travelIdToDelete]);

        while ($mainComment = $findMainComments->fetch(PDO::FETCH_ASSOC)) {
            deleteComments($bdd, $mainComment['id']);
        }

        $deleteView = $bdd->prepare('DELETE FROM travel_view WHERE idtravel = ?');
        $deleteView->execute([$travelIdToDelete]);

        $deleteLike = $bdd->prepare('DELETE FROM travel_like WHERE idtravel = ?');
        $deleteLike->execute([$travelIdToDelete]);

        $deleteQuery = $bdd->prepare('DELETE FROM travel WHERE id = ?');
        $deleteQuery->execute([$travelIdToDelete]);

        $directoryPath = 'Ressources/Travel/' . $travelIdToDelete;
        deleteDirectory($directoryPath);

    } catch (Exception $e) {
        echo "Erreur lors de la suppression du voyage : " . $e->getMessage();
    }
}

function deleteQuiz($bdd, $quizId) {
    try {
        $sql_delete_client_answers = "DELETE FROM client_answer WHERE idquiz = :id_quiz";
        $deleteClientAnswers = $bdd->prepare($sql_delete_client_answers);
        $deleteClientAnswers->bindParam(':id_quiz', $quizId, PDO::PARAM_INT);
        $deleteClientAnswers->execute();

        $sql_select_questions = "SELECT id FROM question WHERE idquiz = :id_quiz";
        $selectQuestions = $bdd->prepare($sql_select_questions);
        $selectQuestions->bindParam(':id_quiz', $quizId, PDO::PARAM_INT);
        $selectQuestions->execute();
        $questions = $selectQuestions->fetchAll(PDO::FETCH_ASSOC);

        foreach ($questions as $question) {
            $id_question = $question['id'];

            $sql_delete_answers = "DELETE FROM answer WHERE idquestion = :id_question";
            $deleteAnswers = $bdd->prepare($sql_delete_answers);
            $deleteAnswers->bindParam(':id_question', $id_question, PDO::PARAM_INT);
            $deleteAnswers->execute();

            $sql_delete_question = "DELETE FROM question WHERE id = :id_question";
            $deleteQuestion = $bdd->prepare($sql_delete_question);
            $deleteQuestion->bindParam(':id_question', $id_question, PDO::PARAM_INT);
            $deleteQuestion->execute();
        }

        $sql_delete_quiz = "DELETE FROM quiz WHERE id = :id_quiz";
        $deleteQuiz = $bdd->prepare($sql_delete_quiz);
        $deleteQuiz->bindParam(':id_quiz', $quizId, PDO::PARAM_INT);
        $deleteQuiz->execute();

        $directoryPath = 'Ressources/Quiz/' . $quizId;
        deleteDirectory($directoryPath);

    } catch (Exception $e) {
        echo "Erreur lors de la suppression du quiz : " . $e->getMessage();
    }
}

function deleteLike($bdd,$idclient,$idtravel) {
    $deleteLike = $bdd->prepare('DELETE FROM travel_like WHERE idclient = ? AND idtravel = ?');
    $deleteLike->execute([$idclient, $idtravel]);
}

function deleteView($bdd,$idclient,$idtravel,$travel_view_date) {
    $deleteView = $bdd->prepare('DELETE FROM travel_view WHERE idtravel = ? AND idclient = ? AND travel_view_date = ?');
    $deleteView->execute([$idtravel, $idclient, $travel_view_date]);
}


// Alerte de confirmation de suppression, modification et bannissement

function travelSupp()
{
    if (isset($_GET['delete']) && $_GET['delete'] === 'success') {
        // Afficher l'alerte Bootstrap pour confirmer la suppression
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Votre voyage a été supprimé avec succès !
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Votre voyage a été supprimé avec succès !
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}

function quizSupp()
{
    if (isset($_GET['delete']) && $_GET['delete'] === 'success') {
        // Afficher l'alerte Bootstrap pour confirmer la suppression
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Le quiz a été supprimé avec succès !
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Le quiz a été supprimé avec succès !
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}

function dataChange()
{
    if (isset($_GET['change']) && $_GET['change'] === 'success') {
        // Afficher l'alerte Bootstrap pour confirmer la suppression
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Les modifications ont bien été apportées.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Les modifications ont bien été apportées.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}


function ticketCreate()
{
    if (isset($_GET['ticket']) && $_GET['ticket'] === 'success') {
        // Afficher l'alerte Bootstrap pour confirmer la suppression
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Votre signalement a bien été envoyé aux administrateurs.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Votre signalement a bien été envoyé aux administrateurs.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}

function createTicket()
{
    if (isset($_GET['ticket']) && $_GET['ticket'] === 'success') {
        // Afficher l'alerte Bootstrap pour confirmer la suppression
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Votre ticket a bien été créé et assigné.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        Votre ticket a bien été créé et assigné.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}

function permaBanAlert()
{
    if (isset($_GET['permaBan']) && $_GET['permaBan'] === 'success') {
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        L\'utilisateur a bien été banni définitivement.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        L\'utilisateur a bien été banni définitivement.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }

}
function dataDelete()
{
    if (isset($_GET['delete']) && $_GET['delete'] === 'success') {
        // Afficher l'alerte Bootstrap pour confirmer la suppression
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        La suppression a été effectuée avec succès.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        La suppression a été effectuée avec succès.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}
function tempBanAlert()
{
    if (isset($_GET['tempBan']) && $_GET['tempBan'] === 'success' && isset($_GET['date']) && preg_match('/\d{4}-\d{2}-\d{2}/', $_GET['date'])) {
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        L\'utilisateur a bien été banni jusqu\'au ' . formatFrenchDate($_GET['date']) . '.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        L\'utilisateur a bien été banni définitivement.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}

function addData()
{
    if (isset($_GET['add']) && $_GET['add'] === 'success') {
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        L\'ajout a été fait avec succès.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        L\'ajout a été fait avec succès.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}

function emailCronChange()
{
    if (isset($_GET['change']) && $_GET['change'] === 'success') {
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        La date avant envoi d\'un mail automatique a été modifiée avec succès.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-success alert-dismissible fade show" role="alert" style="width: auto;">
                        La date avant envoi d\'un mail automatique a été modifiée avec succès.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}

function tempBan()
{
    if (isset($_GET['tempBan']) && $_GET['tempBan'] === 'true' && isset($_GET['date']) && preg_match('/\d{4}-\d{2}-\d{2}/', $_GET['date'])) {
        $formattedDate = formatFrenchDate($_GET['date']);
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: auto;">
                        Vous êtes banni de Landtales jusqu\'au ' .$formattedDate.'
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: auto;">
                        Vous êtes banni de Landtales jusqu\'au ' .$formattedDate.'
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}
function permaBan()
{
    if (isset($_GET['permaBan']) && $_GET['permaBan'] === 'true' ) {
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: auto;">
                            Vous êtes banni indéfiniment. C\'est ciao !
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: auto;">
                        Vous êtes banni indéfiniment. C\'est ciao !
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}


function searchNothing()
{
    if (isset($_GET['search']) && $_GET['search'] === 'nothing' ) {
        echo '<div class="position-fixed top-0 start-0 d-none d-sm-block" style="z-index: 2000;">
                    <div class="alert alert-info   alert-dismissible fade show" role="alert" style="width: auto;">
                            Nous n\'avons pas pu trouver de résultat.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';

        echo '<div class="position-fixed top-50 start-50 translate-middle d-sm-none" style="z-index: 2000;">
                    <div class="alert alert-info  alert-dismissible fade show" role="alert" style="width: auto;">
                            Nous n\'avons pas pu trouver de résultat.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>';
    }
}



function recommendationAlgorithm($bdd, $idclient){

    $travels = [];

    $poids = [
        "mostViewAllTime" => 20,
        "mostViewAllYear" => 20+15,
        "mostViewAllMonth" => 35+10,
        "mostViewAllWeek" => 45+10,
        "fromPopularClient" => 55+25,
        "fromNonPopularClient" => 80+20,

        "fromFollowedClient" => 40,
        "fromFriend" => 40+15,
        "recentfromPopularClient" => 55+15,
        "recentWithSimilarThemes" => 70+30
    ];


    $query = $bdd->prepare('SELECT COUNT(idclient) AS view_count FROM travel_view WHERE idclient = ?');
    $query->execute([$idclient]);
    $progress = $query->fetch(PDO::FETCH_ASSOC);


//Recupération des informations de la bdd

//$query = $dbh->query('SELECT * FROM client');

//$result = $query ->fetchAll();

//Recuperation des voyages les plus vus
//ROUND(RAND()*9)+1 pour obtenir un nombre entre 1 et 10 compris

    function newTravelArray($array){
        $maxelement = count($array);
        $rnd = rand(0,$maxelement-1);
        $travel = $array[$rnd];
        $array[$rnd] = array_pop($array);
        return ['arrayTravels' => $array, 'travel' => $travel];
    }



    function exclusion($excludedTravels)
    {
        // Vérifie si le tableau est vide
        if (empty($tableau)) {
            return "('')";
        }
        // Utilise la fonction implode pour joindre les éléments du tableau avec une virgule
        $str = "(".implode(', ', $excludedTravels).")";
        return $str;
    }


    $SQLcommand = [
        'getTravelsFromNonPopularClients' =>
            "WITH eligible_clients AS (
    SELECT id
    FROM client
    WHERE (SELECT COUNT(id) FROM travel WHERE idclient = client.id AND visibility = 1) > 0
),
clients_with_follower_count AS (
    SELECT id,
           (SELECT COUNT(idclientfollowed) FROM follower WHERE idclientfollowed = client.id) AS follower_count
    FROM eligible_clients AS client
),
sorted_clients AS (
    SELECT id
    FROM clients_with_follower_count
    ORDER BY follower_count ASC
    LIMIT 40
),
selected_travels AS (
    SELECT id AS idtravel, idclient,
           ROW_NUMBER() OVER (PARTITION BY idclient ORDER BY RAND()) AS rn,
           COUNT(id) OVER (PARTITION BY idclient) AS total_travels
    FROM travel
    WHERE idclient IN (SELECT id FROM sorted_clients) AND visibility = 1
)
SELECT idtravel
FROM selected_travels
WHERE rn <= ROUND(total_travels * 0.2)
ORDER BY rn",
        'getTravelsFromPopularClients' =>
            "WITH eligible_clients AS (
    SELECT id
    FROM client
    WHERE (SELECT COUNT(id) FROM travel WHERE idclient = client.id AND visibility = 1) > 0
),
sorted_clients AS (
    SELECT id
    FROM eligible_clients AS client
    ORDER BY (SELECT COUNT(idclientfollowed) FROM follower WHERE idclientfollowed = client.id) DESC
    LIMIT 40
),
client_travels AS (
    SELECT t.id as idtravel, t.idclient,
           ROW_NUMBER() OVER (PARTITION BY t.idclient ORDER BY RAND()) AS rn,
           COUNT(t.id) OVER (PARTITION BY t.idclient) AS total_travels
    FROM travel t
    JOIN sorted_clients c ON t.idclient = c.id
    WHERE t.visibility = 1
)
SELECT idtravel
FROM client_travels
WHERE rn <= ROUND(total_travels * 0.2)
ORDER BY rn",
        'getMostViewedTravelsOfWeek' =>
            "WITH eligible_travel AS (
    SELECT id
    FROM travel
    WHERE visibility != 0 AND travel_status = 1 AND DATEDIFF(NOW(), travel_date) <= 7
),
travel_count AS (
    SELECT idtravel, COUNT(idtravel) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM eligible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM travel_count
ORDER BY cnt DESC
LIMIT 40",
        'getMostViewedTravelsOfMonth' =>
            "WITH eligible_travel AS (
    SELECT id
    FROM travel
    WHERE visibility != 0 AND travel_status = 1 AND DATEDIFF(NOW(), travel_date) <= 30
),
travel_count AS (
    SELECT idtravel, COUNT(*) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM eligible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM travel_count
ORDER BY cnt DESC
LIMIT 40",
        'getMostViewedTravelsOfYear' =>
            "WITH eligible_travel AS (
    SELECT id
    FROM travel
    WHERE visibility != 0 AND travel_status = 1 AND DATEDIFF(NOW(), travel_date) <= 365
),
travel_count AS (
    SELECT idtravel, COUNT(*) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM eligible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM travel_count
ORDER BY cnt DESC
LIMIT 40",
        'getMostViewedTravelsOfAllTime'=>
            "WITH visible_travel AS (
    SELECT id
    FROM travel
    WHERE visibility = 1 AND travel_status = 1
),
visible_travel_count AS (
    SELECT idtravel, COUNT(idtravel) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM visible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM visible_travel_count
ORDER BY cnt
LIMIT 40",
        'getTravelsFromFollowedClients'=>
            "WITH eligibles_clients AS (
    SELECT id
    FROM client
    WHERE (id IN (SELECT idclient1 FROM friend WHERE idclient2 = ".$idclient." AND accepted = 2)
    OR id IN (SELECT idclient2 FROM friend WHERE idclient1 = ".$idclient." AND accepted = 2))
)
SELECT travel.id
FROM travel
JOIN eligibles_clients ON travel.idclient = eligibles_clients.id
WHERE travel.visibility = 1 ORDER BY travel_date DESC",
        'getTravelsFromFriendClients'=>
            "WITH eligibles_clients AS (
    SELECT id
    FROM client
    WHERE (
    SELECT COUNT(id) FROM travel WHERE idclient = client.id AND visibility = 1) > 0
      AND (id IN (SELECT idclient1 FROM friend WHERE idclient2 = ".$idclient." AND accepted = 2) OR id IN (SELECT idclient2 FROM friend WHERE idclient1 = ".$idclient." AND accepted = 2))
)
SELECT travel.id
FROM travel JOIN eligibles_clients ON travel.idclient = eligibles_clients.id
WHERE visibility = 1 ORDER BY travel.travel_date DESC",
        'getRecentTravelsFromPopularClients'=>
            "WITH eligibles_clients AS (
    SELECT id
    FROM client
    WHERE (SELECT COUNT(id) FROM travel WHERE visibility = 1) > 0
),
sorted_clients AS (
    SELECT id
    FROM eligibles_clients
    ORDER BY (SELECT COUNT(idclientfollowed) FROM follower WHERE idclientfollowed = eligibles_clients.id) DESC
    LIMIT 40
),
client_travels AS (
    SELECT t.id as idtravel, t.idclient,
           ROW_NUMBER() OVER (PARTITION BY t.idclient ORDER BY RAND()) AS rn,
           COUNT(t.id) OVER (PARTITION BY t.idclient) AS total_travels
    FROM travel t
    JOIN sorted_clients c ON t.idclient = c.id
    WHERE t.visibility = 1 AND t.travel_status = 1 AND DATEDIFF(NOW(), t.travel_date) <= 14
)
SELECT idtravel
FROM client_travels
WHERE rn <= ROUND(total_travels * 0.2)
ORDER BY rn",
        'getRecentPopularTravelsWithSimilarThemes'=>
            "WITH theme_popularity AS (
    SELECT travel.idtheme AS idtheme, COUNT(travel.idtheme) AS theme_count
    FROM travel 
    JOIN travel_view ON travel.id = travel_view.idtravel
    WHERE travel_view.idclient = ".$idclient." AND travel.visibility = 1
    GROUP BY travel.idtheme
),
good_themes AS (
    SELECT idtheme,theme_count
    FROM theme_popularity
    ORDER BY theme_count DESC
    LIMIT 5
),
eligible_travel AS (
    SELECT id
    FROM travel JOIN good_themes ON travel.idtheme = good_themes.idtheme
    WHERE visibility != 0 AND travel_status = 1 AND DATEDIFF(NOW(), travel_date) <= 14
),
travel_count AS (
    SELECT idtravel, COUNT(idtravel) AS cnt
    FROM travel_view
    WHERE idtravel IN (SELECT id FROM eligible_travel)
    GROUP BY idtravel
)
SELECT idtravel
FROM travel_count
ORDER BY cnt DESC
LIMIT 40"
    ];
//$query = $dbh->query($SQLcommand['getMostViewedTravelsOfAllTime']);

    $SQLcommandResults = [];
    foreach ($SQLcommand as $goal => $command){
        $query = $bdd->query($command);
        $results = $query->fetchAll();
        $commandResult = [];
        foreach ($results as $result){
            foreach ($result as $idtravel) {
                array_push($commandResult, $idtravel);
            }
        }
        $SQLcommandResults[$goal] = $commandResult;
    }

// Récupération du nombre de vues de voyages
    $query = $bdd->prepare('SELECT COUNT(idclient) AS view_count FROM travel_view WHERE idclient = ?');
    $query->execute([$idclient]);
    $progress = $query->fetch(PDO::FETCH_ASSOC);

    if (!$progress) {
        $progress = ['view_count' => 0]; // Ajustement par défaut si aucune vue n'est trouvée
    }

    $choiceMax = intdiv($progress['view_count'], 10) + 20;
    if ($choiceMax > 100) {
        $choiceMax = 100;
    }

    $travelsTotal = 0;
    foreach ($SQLcommandResults as $commandResult){
        $travelsTotal += count($commandResult);
    }

    while ($travelsTotal > 0){
        $choice = rand(1, $choiceMax);
        if ($choice <= 20){
            // Partie aléatoire
            $choice = rand(1, 100);
            if ($choice <= $poids["mostViewAllTime"]){
                $categorie = "getMostViewedTravelsOfAllTime";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["mostViewAllYear"]){
                $categorie = "getMostViewedTravelsOfYear";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["mostViewAllMonth"]){
                $categorie = "getMostViewedTravelsOfMonth";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["mostViewAllWeek"]){
                $categorie = "getMostViewedTravelsOfWeek";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["fromPopularClient"]){
                $categorie = "getTravelsFromPopularClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["fromNonPopularClient"]){
                $categorie = "getTravelsFromNonPopularClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            }
        } else {
            // Partie recommandée
            $choice = rand(1, 100);
            if ($choice <= $poids["fromFollowedClient"]){
                $categorie = "getTravelsFromFollowedClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["fromFriend"]){
                $categorie = "getTravelsFromFriendClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                $travel = newTravelArray($SQLcommandResults[$categorie]);
                $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                $travel = $travel['travel'];
                array_push($travels, $travel);
                $travelsTotal -= 1;
            }
            } elseif ($choice <= $poids["recentfromPopularClient"]){
                $categorie = "getRecentTravelsFromPopularClients";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            } elseif ($choice <= $poids["recentWithSimilarThemes"]){
                $categorie = "getRecentPopularTravelsWithSimilarThemes";
                $nbTravel = count($SQLcommandResults[$categorie]);
                if ($nbTravel > 0){
                    $travel = newTravelArray($SQLcommandResults[$categorie]);
                    $SQLcommandResults[$categorie] = $travel['arrayTravels'];
                    $travel = $travel['travel'];
                    array_push($travels, $travel);
                    $travelsTotal -= 1;
                }
            }
        }
    }

    /*$choice = rand(1,$choiceMax);
    if ($choice<=20){
        //on va partir sur la partie aleatoire
        $choice = rand(1,100);
        if ($choice)
    } else {
        //on part sur la partie recommandée
    }*/

    /*
        foreach ($SQLcommandResults as $travels){
            foreach($travels as $travel){
                array_push($travels,$travel);
            }
        }
    */


    $travels = array_unique($travels);
    $_SESSION["excludedTravels"] = $travels;

    return $travels;
}
