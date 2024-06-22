<?php

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