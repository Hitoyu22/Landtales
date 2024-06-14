<aside id="sidebar" class="mt-5">

    <ul class="sidebar-nav position-fixed  ">
        <li class="sidebar-item">
            <a href="travelLobby.php" class="sidebar-link collapsed has-dropdown" title="Voyages" data-bs-toggle="collapse"
               data-bs-target="#travel" aria-expanded="false" aria-controls="travel">
                <i class="lni lni-map"></i>
                <span>Voyages</span>
            </a>
            <ul id="travel" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="travelLobby.php" title="Découvrir les voyages" class="sidebar-link">Découvrir les voyages</a>
                </li>
                <li class="sidebar-item">
                    <a href="createTravelfirst.php" title="Créer un voyage" class="sidebar-link">Créer un voyage</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="quizLobby.php" class="sidebar-link collapsed has-dropdown" title="Quiz" data-bs-toggle="collapse"
               data-bs-target="#quiz" aria-expanded="false" aria-controls="quiz">
                <i class="lni lni-question-circle"></i>
                <span>Quiz</span>
            </a>
            <ul id="quiz" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="quizLobby.php" title="Découvrir les quiz" class="sidebar-link">Découvrir les quiz</a>
                </li>
                <li class="sidebar-item">
                    <a href="createQuiz.php" title="Créer un Quiz" class="sidebar-link">Créer un quiz</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="myFriends.php" title="Messages" class="sidebar-link">
                <i class="lni lni-popup"></i>
                <span>Messages</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="userLobby.php" title="Utilisateurs" class="sidebar-link">
                <i class="lni lni-users"></i>
                <span>Utilisateurs</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="userLikes.php" title="J'aime" class="sidebar-link">
                <i class="lni lni-heart-fill"></i>
                <span>J'aime</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="customisation.php" title="Boutique" class="sidebar-link">
                <i class="lni lni-cart-full"></i>
                <span>Boutique</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="userProfil.php" class="sidebar-link collapsed has-dropdown" title="Votre profil" data-bs-toggle="collapse"
               data-bs-target="#profil" aria-expanded="false" aria-controls="profil">
                <i class="lni lni-user"></i>
                <span>Votre profil</span>
            </a>
            <ul id="profil" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="userProfil.php" title="Accueil" class="sidebar-link">Accueil</a>
                </li>
                <li class="sidebar-item">
                    <a href="userTravel.php" title="Vos voyages" class="sidebar-link">Vos voyages</a>
                </li>
                <li class="sidebar-item">
                    <a href="userAbout.php" title="A propos" class="sidebar-link">A propos</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="profileSettings.php" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
               data-bs-target="#settings" aria-expanded="false" title="Paramètres" aria-controls="settings">
                <i class="lni lni-cog"></i>
                <span>Paramètres</span>
            </a>
            <ul id="settings" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="profileSettings.php" title="Général" class="sidebar-link">Général</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileCustom.php" title="Modifier le profil" class="sidebar-link">Modifier le profil</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileDrawing.php" title="Votre dessin" class="sidebar-link">Votre dessin</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileTravel.php" title="Vos voyages" class="sidebar-link">Vos voyages</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileConfidentiality.php" title="Confidentialité" class="sidebar-link">Confidentialité</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileReporting.php" title="Signalement" class="sidebar-link">Signalement</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a class="sidebar-link">
                <i class="lni lni-coin"></i>
                <span><?php
                    $coin = $bdd->prepare('SELECT coin FROM client WHERE id = ?');
                    $coin->execute([$userId]);
                    $userCoin = $coin->fetch();

                    echo $userCoin['coin'] . ' ' . ($userCoin['coin'] > 1 ? 'pièces' : 'pièce');
                    ?></span>
            </a>
        </li>
        <li class="sidebar-item">
            <div class="switch-theme mt-3">
                <label class="switch">
                    <input id="theme" type="checkbox" title="Changer de thème" onclick="change_couleur_mode()">
                    <span class="slider"></span>
                </label>
            </div>
        </li>
        <li class="mt-5">
            <a href="logout.php" title="Déconnexion" class="sidebar-link position-relative logout-button">
                <div>
                    <i class="lni lni-exit"></i>
                    <span>Déconnexion</span>
                </div>
            </a>
        </li>
    </ul>

</aside>