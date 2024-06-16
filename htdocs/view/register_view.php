<h1>Création de compte</h1>
<?php
if (isset($_GET['error']) && $_GET['error']){
    echo('Vous avez mal saisie une information');
    echo('<br>');
}
?>
<form action="/create-account" method="POST">
    <div>
        <label for="firstname"> Prenom </label>
        <input id="firstname" type="text" name="firstname">
    </div>

    <div>
        <label for="lastname"> Nom </label>
        <input id="lastname" type="text" name="lastname" placeholder="Saisir votre nom">
    </div>

    <!-- EMAIL -->
    <div>
        <label for="email"> Email </label>
        <input id="email" type="email" name="email">
    </div>

    <!-- PASSWORD -->
    <div>
        <label for="password">Mot de passe</label>
        <input id="password" type="password" name="password">

        <!--        <p> --><?php //if ($_GET['password'] === 0) { echo('Mauvais mdp'); } ?><!-- </p>-->

    </div>

    <!-- GENDER (radio) -->
    <div>
        <p> Genre </p>

        <label for="man">Homme</label>
        <input id="man" type="radio" name="gender" value="man">

        <label for="woman">Femme</label>
        <input id="woman" type="radio" name="gender" value="woman">

        <label for="helicopter">Helicopter</label>
        <input id="helicopter" type="radio" name="gender" value="helicopter">
    </div>

    <input type="submit" value="Créer">
</form>