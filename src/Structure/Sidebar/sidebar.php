<aside id="sidebar" class="mt-5">

    <ul class="sidebar-nav position-fixed  ">
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
               data-bs-target="#travel" aria-expanded="false" aria-controls="travel">
                <i class="lni lni-map"></i>
                <span>Voyages</span>
            </a>
            <ul id="travel" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Découvrir les voyages</a>
                </li>
                <li class="sidebar-item">
                    <a href="createTravelfirst.php" class="sidebar-link">Créer un voyage</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
               data-bs-target="#quiz" aria-expanded="false" aria-controls="quiz">
                <i class="lni lni-question-circle"></i>
                <span>Quiz</span>
            </a>
            <ul id="quiz" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Découvrir les quiz</a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Créer un quiz</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-popup"></i>
                <span>Messages</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-heart-fill"></i>
                <span>J'aime</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-cart-full"></i>
                <span>Boutique</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
               data-bs-target="#profil" aria-expanded="false" aria-controls="profil">
                <i class="lni lni-user"></i>
                <span>Votre profil</span>
            </a>
            <ul id="profil" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="userProfil.php" class="sidebar-link">Accueil</a>
                </li>
                <li class="sidebar-item">
                    <a href="userTravel.php" class="sidebar-link">Vos voyages</a>
                </li>
                <li class="sidebar-item">
                    <a href="userAbout.php" class="sidebar-link">A propos</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
               data-bs-target="#settings" aria-expanded="false" aria-controls="settings">
                <i class="lni lni-cog"></i>
                <span>Paramètres</span>
            </a>
            <ul id="settings" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="profileSettings.php" class="sidebar-link">Général</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileCustom.php" class="sidebar-link">Modifier le profil</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileTravel.php" class="sidebar-link">Vos voyages</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileConfidentiality.php" class="sidebar-link">Confidentialité</a>
                </li>
                <li class="sidebar-item">
                    <a href="profileReporting.php" class="sidebar-link">Signalement</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-coin"></i>
                <span>103 Pièces</span>
            </a>
        </li>
        <!--<li class="sidebar-item">
            <label id="light-btn" class="switch sidebar-link">
                <i class="lni lni-sun"></i>
                <input type="checkbox" onclick="change_couleur_mode()" id="theme">
            </label>
        </li>-->
        <!--switch light/dark mode-->
        <li class="sidebar-item">
            <div class="switch-theme mt-3">
                <label class="switch">
                    <input id="theme" type="checkbox" onclick="change_couleur_mode()">
                    <span class="slider"></span>
                </label>
            </div>
        </li>
        <!--fin du switch-->
        <li class="mt-5">
            <a href="logout.php" class="sidebar-link position-relative logout-button">
                <div>
                    <i class="lni lni-exit"></i>
                    <span>Déconnexion</span>
                </div>
            </a>

        </li>
    </ul>

</aside>