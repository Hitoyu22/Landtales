<?php

include_once 'Structure/Bdd/config.php';


$slug =  explode('?', $_SERVER['REQUEST_URI'])[0];



$admin_slug = '/Admin';

if (str_starts_with($slug, $admin_slug)) {
    $admin_route = substr($slug, strlen($admin_slug));
    switch ($admin_route) {
        case '/captcha':
            include 'Admin/Controller/captcha_controller.php';
            break;
        case '/homeBack':
            include 'Admin/Controller/homeBack_controller.php';
            break;
        case '/custom':
            include 'Admin/Controller/custom_controller.php';
            break;
        case '/downloadLogs':
            include 'Admin/Controller/downloadLogs_controller.php';
            break;
        case '/logs':
            include 'Admin/Controller/logs_controller.php';
            break;
        case '/newsletter':
            include 'Admin/Controller/newsletter_controller.php';
            break;
        case '/newsletterError':
            include 'Admin/Controller/newsletterError_controller.php';
            break;
        case '/newsletterStepTwo':
            include 'Admin/Controller/newsletterStepTwo_controller.php';
            break;
        case '/newsletterSuccess':
            include 'Admin/Controller/newsletterSuccess_controller.php';
            break;
        case '/newsletterToHtml':
            include 'Admin/Controller/newsletterToHtml_controller.php';
            break;
        case '/userRight':
            include 'Admin/Controller/userRight_controller.php';
            break;
        case '/theme':
            include 'Admin/Controller/theme_controller.php';
            break;
        case '/logStat':
            include 'Admin/Controller/logStat_controller.php';
            break;
        case '/ticketLobby':
            include 'Admin/Controller/ticketLobby_controller.php';
            break;
        case '/createTicket':
            include 'Admin/Controller/createTicket_controller.php';
            break;
        case '/ticket':
            include 'Admin/Controller/ticket_controller.php';
            break;
        case '/downloadDocumentTicket':
            include 'Admin/Controller/downloadDocumentTicket_controller.php';
            break;
        case '/modifyTicket':
            include 'Admin/Controller/modifyTicket_controller.php';
            break;
        case '/fetchTicket':
            include 'Admin/Controller/fetchTicket_controller.php';
            break;
        case '/databaseTreatment':
            include 'Admin/Controller/databaseTreatment_controller.php';
            break;
        case '/databaseGestion':
            include 'Admin/Controller/databaseGestion_controller.php';
            break;
            default : include 'Error/Erreur404.php';
    }
} else {

// Router
    switch ($slug) {
        // url
        case '/':
            include 'Controller/landing_controller.php';
            break;
        case '/accountConfirm':
            include 'Controller/accountConfirm_controller.php';
            break;
        case '/createQuiz':
            include 'Controller/createQuiz_controller.php';
            break;
        case '/createTravelfirst':
            include 'Controller/createTravelfirst_controller.php';
            break;
        case '/createTravelsecond':
            include 'Controller/createTravelsecond_controller.php';
            break;
        case '/customisation':
            include 'Controller/customisation_controller.php';
            break;
        case '/deleteAccountConfirm':
            include 'Controller/deleteAccountConfirm_controller.php';
            break;
        case '/forgotPassword':
            include 'Controller/forgotPassword_controller.php';
            break;
        case '/homeFront':
            include 'Controller/homeFront_controller.php';
            break;
        case '/homeQuiz':
            include 'Controller/homeQuiz_controller.php';
            break;
        case '/login':
            include 'Controller/login_controller.php';
            break;
        case '/logout':
            include 'Controller/logout_controller.php';
            break;
        case '/logoutConfirmation':
            include 'Controller/logoutConfirmation_controller.php';
            break;
        case '/mailConfirmation':
            include 'Controller/mailConfirmation_controller.php';
            break;
        case '/messages':
            include 'Controller/messages_controller.php';
            break;
        case '/modificationSuccess':
            include 'Controller/modificationSuccess_controller.php';
            break;
        case '/modifyTravel':
            include 'Controller/modifyTravel_controller.php';
            break;
        case '/myFriends':
            include 'Controller/myFriends_controller.php';
            break;
        case '/newPassword':
            include 'Controller/newPassword_controller.php';
            break;
        case '/passwordChangeConfirmation':
            include 'Controller/passwordChangeConfirmation_controller.php';
            break;
        case '/passwordRequestConfirmation':
            include 'Controller/passwordRequestConfirmation_controller.php';
            break;
        case '/profileConfidentiality':
            include 'Controller/profileConfidentiality_controller.php';
            break;
        case '/profileCustom':
            include 'Controller/profileCustom_controller.php';
            break;
        case '/profileDrawing':
            include 'Controller/profileDrawing_controller.php';
            break;
        case '/profileReporting':
            include 'Controller/profileReporting_controller.php';
            break;
        case '/profileSettings':
            include 'Controller/profileSettings_controller.php';
            break;
        case '/profileTravel':
            include 'Controller/profileTravel_controller.php';
            break;
        case '/publicationSuccess':
            include 'Controller/publicationSuccess_controller.php';
            break;
        case '/quizCreationConfirmation':
            include 'Controller/quizCreationConfirmation_controller.php';
            break;
        case '/quizLobby':
            include 'Controller/quizLobby_controller.php';
            break;
        case '/quizGame':
            include 'Controller/quizGame_controller.php';
            break;
        case '/register':
            include 'Controller/register_controller.php';
            break;
        case '/results':
            include 'Controller/results_controller.php';
            break;
        case '/saveSuccess':
            include 'Controller/saveSuccess_controller.php';
            break;
        case '/travel':
            include 'Controller/travel_controller.php';
            break;
        case '/travelLobby':
            include 'Controller/travelLobby_controller.php';
            break;
        case '/userLobby':
            include 'Controller/userLobby_controller.php';
            break;
        case '/updateQuiz':
            include 'Controller/updateQuiz_controller.php';
            break;
        case '/userAbout':
            include 'Controller/userAbout_controller.php';
            break;
        case '/userLikes':
            include 'Controller/userLikes_controller.php';
            break;
        case '/userProfil':
            include 'Controller/userProfil_controller.php';
            break;
        case '/userTravel':
            include 'Controller/userTravel_controller.php';
            break;
        case '/verification':
            include 'Controller/verification_controller.php';
            break;
        case '/Includes/search':
            include 'Includes/search.php';
            break;
        case '/Includes/genpdf':
            include 'Includes/genpdf.php';
            break;
        case '/Includes/loadMessages':
            include 'Includes/loadMessages.php';
            break;
        case '/Includes/getComments':
            include 'Includes/getComments.php';
            break;
        case '/Includes/likesTreatment':
            include 'Includes/likesTreatment.php';
            break;
        case '/Endpoint/upload':
            include 'Endpoint/upload.php';
            break;
        case '/Endpoint/uploadNewsletter':
            include 'Endpoint/uploadNewsletter.php';
            break;
        case '/Includes/emailCron':
            include 'Includes/emailCron.php';
            break;
        case '/Includes/logArchive':
            include 'Includes/logArchive.php';
            break;
        case '/Includes/purgeLogs':
            include 'Includes/purgeLogs.php';
            break;

        default : include 'Error/Erreur404.php';
    }
}
