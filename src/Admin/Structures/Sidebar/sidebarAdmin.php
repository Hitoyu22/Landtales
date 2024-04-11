<aside id="sidebar" class="mt-5">

    <ul class="sidebar-nav position-fixed  ">
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-envelope"></i>
                <span>Newsletter</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-protection"></i>
                <span>Captcha</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-laravel"></i>
                <span>Customisation</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
               data-bs-target="#log" aria-expanded="false" aria-controls="log">
                <i class="lni lni-eye"></i>
                <span>Logs du site</span>
            </a>
            <ul id="log" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Général</a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Voyages</a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Quiz</a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Boutiques</a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Ticket</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-database"></i>
                <span>Base de données</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" data-bs-toggle="collapse"
               data-bs-target="#quiz" aria-expanded="false" aria-controls="quiz">
                <i class="lni lni-ticket-alt"></i>
                <span>Ticket</span>
            </a>
            <ul id="quiz" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Suivi des tickets</a>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link">Créer un ticket</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link">
                <i class="lni lni-user"></i>
                <span>Profil administrateur</span>
            </a>
        </li>
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
            <a href="#" class="sidebar-link position-relative logout-button">
                <div>
                    <i class="lni lni-exit"></i>
                    <span>Déconnexion</span>
                </div>
            </a>

        </li>
    </ul>

</aside>