<aside id="sidebar" class="mt-5">

    <ul class="sidebar-nav position-fixed  ">
        <li class="sidebar-item">
            <a href="newsletter.php" title="Newsletter" class="sidebar-link">
                <i class="lni lni-envelope"></i>
                <span>Newsletter</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="captcha.php" title="Captcha" class="sidebar-link">
                <i class="lni lni-protection"></i>
                <span>Captcha</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="custom.php" title="Customisation" class="sidebar-link">
                <i class="lni lni-laravel"></i>
                <span>Customisation</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="logs.php" title="Logs du site" class="sidebar-link">
                <i class="lni lni-eye"></i>
                <span>Logs du site</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="theme.php" title="Thème des voyages" class="sidebar-link">
                <i class="lni lni-pencil-alt"></i>
                <span>Thème des voyages</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="databaseGestion.php" title="Base de données" class="sidebar-link">
                <i class="lni lni-database"></i>
                <span>Base de données</span>
            </a>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed has-dropdown" title="Ticket" data-bs-toggle="collapse"
               data-bs-target="#quiz" aria-expanded="false" aria-controls="quiz">
                <i class="lni lni-ticket-alt"></i>
                <span>Ticket</span>
            </a>
            <ul id="quiz" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="ticketLobby.php" title="Suivi des tickets" class="sidebar-link">Suivi des tickets</a>
                </li>
                <li class="sidebar-item">
                    <a href="createTicket.php" title="Créer un ticket" class="sidebar-link">Créer un ticket</a>
                </li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="userRight.php" title="Droits d'utilisateur" class="sidebar-link">
                <i class="lni lni-user"></i>
                <span>Droits d'utilisateur</span>
            </a>
        </li>
        <li class="sidebar-item">
            <div class="switch-theme mt-3">
                <label class="switch">
                    <input id="theme" type="checkbox" onclick="change_couleur_mode()">
                    <span class="slider"></span>
                </label>
            </div>
        </li>
        <li class="mt-5">
            <a href="../logout.php" title="Déconnexion" class="sidebar-link position-relative logout-button">
                <div>
                    <i class="lni lni-exit"></i>
                    <span>Déconnexion</span>
                </div>
            </a>

        </li>
    </ul>

</aside>