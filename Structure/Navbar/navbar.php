<nav class="row navbar position-fixed w-100">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center">
                <button class="toggle-btn" type="button" onmousedown="hideFocusBorder(this)">
                    <i class="lni lni-menu"></i>
                </button>
                <h2 class="sidebar-logo h2-a"><a class="logo-h2-a" href="homeFront.php">Landtales</a></h2>
            </div>
            <form class="searchbar d-none d-lg-block">
                <div class="input-group mb-3 position-relative">
                    <div class="input-group-prepend">
                        <button class="btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Filtre</button>
                        <div class="dropdown-menu" tabindex="0">
                            <a class="dropdown-item" href="#" data-table="Voyage">Voyage</a>
                            <a class="dropdown-item" href="#" data-table="Quiz">Quiz</a>
                            <a class="dropdown-item" href="#" data-table="Utilisateur">Utilisateur</a>
                        </div>
                    </div>
                    <input id="search-input" type="search" class="form-control me-2 search-bar-input" placeholder="Recherche" aria-label="Recherche" oninput="getSuggestions(this.value)" autocomplete="off">
                    <div id="suggestions" class="position-absolute rounded-3 shadow p-2 d-none"></div>
                </div>
            </form>
            <a href="createTravelfirst.php" class="btn-landtales d-none d-lg-block">Créer un voyage</a>
            <a href="#" class="d-lg-none ml-auto loupe-search-link"><i class="fa-solid fa-magnifying-glass loupe-search"></i></a>
        </div>
    </div>
</nav>

<div id="fullscreen-search" class="fullscreen-search d-none">
    <div class="search-container">
        <input id="fullscreen-search-input" type="search" class="form-control mx-2" placeholder="Recherche..." autocomplete="off">
        <button id="close-search" class="close-search">&times;</button>
    </div>
    <div class="container-fluid justify-content-between mt-3">
        <button class="btn-table-selector btn-landtales" data-table="Voyage">Voyage</button>
        <button class="btn-table-selector btn-landtales" data-table="Quiz">Quiz</button>
        <button class="btn-table-selector btn-landtales" data-table="Utilisateur">Utilisateur</button>
    </div>
    <div id="mobile-suggestions" class="rounded-3 p-2 d-none w-100"></div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initializeSearchFunctionality();
    });
</script>
<script src="Structure/Functions/navbar.js"></script>
