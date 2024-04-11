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
    $mail->Password = 'iqiq oqjd yazs geta';

    $mail->IsHTML(true);
    $mail->From = "landtales.website@gmail.com";
    $mail->FromName = $from_name;
    $mail->Sender = $from;
    $mail->AddReplyTo($from, $from_name);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->AddAddress($to);
    if (!$mail->Send()) {
        $error = "Please try Later, Error Occured while Processing...";
        return $error;
    } else {
        return true;
    }

}