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
            <!-- Loupe icon for mobile screens -->
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

    let selectedTable = "Voyage";


    document.getElementById('search-input').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            handleSearchEnter('search-input', 'suggestions');
        }
    });

    document.getElementById('fullscreen-search-input').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            handleSearchEnter('fullscreen-search-input', 'mobile-suggestions');
        }
    });

    function handleSearchEnter(inputId, suggestionsId) {
        const input = document.getElementById(inputId);
        const suggestionsDiv = document.getElementById(suggestionsId);
        const suggestions = suggestionsDiv.querySelectorAll('.suggestion-item');

        if (suggestions.length > 0) {
            const firstSuggestionLink = suggestions[0].querySelector('a');
            if (firstSuggestionLink) {
                window.location.href = firstSuggestionLink.href;
            }
        } else {
            let searchUrl;
            switch (selectedTable) {
                case 'Voyage':
                    searchUrl = `travelLobby.php?search=nothing`;
                    break;
                case 'Quiz':
                    searchUrl = `quizLobby.php?search=nothing`;
                    break;
                case 'Utilisateur':
                    searchUrl = `userLobby.php?search=nothing`;
                    break;
                default:
                    searchUrl = `travelLobby.php?search=nothing`;
            }
            window.location.href = searchUrl;
        }
    }

    document.querySelectorAll('.btn-table-selector').forEach(button => {
        button.addEventListener('click', function() {
            selectedTable = this.getAttribute('data-table');
            document.querySelectorAll('.btn-table-selector').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            getSuggestionsMobile(document.getElementById('fullscreen-search-input').value);
        });
    });

    document.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(event) {
            event.preventDefault();
            selectedTable = this.getAttribute('data-table');
            document.querySelector('.dropdown-toggle').textContent = selectedTable.charAt(0).toUpperCase() + selectedTable.slice(1);
            getSuggestions(document.getElementById('search-input').value);
        });
    });

    function getSuggestions(keyword) {
        fetch('Includes/search.php?keyword=' + encodeURIComponent(keyword) + '&table=' + encodeURIComponent(selectedTable))
            .then(response => response.json())
            .then(data => {
                const suggestionsDiv = document.getElementById('suggestions');
                suggestionsDiv.innerHTML = '';

                if (data.length > 0) {
                    suggestionsDiv.classList.remove('d-none');
                    data.forEach((suggestion, index) => {
                        const suggestionElement = document.createElement('div');
                        suggestionElement.classList.add('suggestion-item');
                        suggestionElement.tabIndex = index + 1; // Index commence à 0, donc on commence à 1 pour la navigation

                        const link = document.createElement('a');
                        link.href = suggestion.url;
                        link.textContent = suggestion.title;
                        link.classList.add('suggestion-link');

                        suggestionElement.appendChild(link);
                        suggestionsDiv.appendChild(suggestionElement);
                    });
                } else {
                    // Si aucun résultat n'est retourné, affiche "Aucun résultat"
                    suggestionsDiv.classList.remove('d-none');
                    suggestionsDiv.textContent = 'Aucun résultat';
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    function getSuggestionsMobile(keyword) {
        fetch('Includes/search.php?keyword=' + encodeURIComponent(keyword) + '&table=' + encodeURIComponent(selectedTable))
            .then(response => response.json())
            .then(data => {
                const suggestionsDiv = document.getElementById('mobile-suggestions');
                suggestionsDiv.innerHTML = '';

                if (data.length > 0) {
                    suggestionsDiv.classList.remove('d-none');
                    data.forEach((suggestion, index) => {
                        const suggestionElement = document.createElement('div');
                        suggestionElement.classList.add('suggestion-item');
                        suggestionElement.tabIndex = index + 1; // Index commence à 0, donc on commence à 1 pour la navigation

                        const link = document.createElement('a');
                        link.href = suggestion.url;
                        link.textContent = suggestion.title;
                        link.classList.add('suggestion-link');

                        suggestionElement.appendChild(link);
                        suggestionsDiv.appendChild(suggestionElement);
                    });
                } else {
                    // Si aucun résultat n'est retourné, affiche "Aucun résultat"
                    suggestionsDiv.classList.remove('d-none');
                    suggestionsDiv.textContent = 'Aucun résultat';
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Écouteur d'événements pour la navigation avec les touches directionnelles
    document.addEventListener('keydown', function(event) {
        const suggestions = document.querySelectorAll('.suggestion-item');
        if (suggestions.length > 0) {
            let currentIndex = Array.from(suggestions).findIndex(suggestion => suggestion === document.activeElement);
            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    if (currentIndex < suggestions.length - 1) {
                        suggestions[currentIndex + 1].focus();
                    }
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    if (currentIndex > 0) {
                        suggestions[currentIndex - 1].focus();
                    }
                    break;
                case 'Enter':
                    event.preventDefault();
                    if (currentIndex >= 0) {
                        const link = suggestions[currentIndex].querySelector('a');
                        if (link) {
                            window.location.href = link.href;
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    });

    document.getElementById('search-input').addEventListener('focus', function() {
        getSuggestions(this.value);
    });

    document.getElementById('fullscreen-search-input').addEventListener('input', function() {
        getSuggestionsMobile(this.value);
    });

    document.querySelector('.loupe-search-link').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('fullscreen-search').classList.remove('d-none');
        document.getElementById('fullscreen-search-input').focus();
    });

    document.getElementById('close-search').addEventListener('click', function() {
        document.getElementById('fullscreen-search').classList.add('d-none');
    });

    document.addEventListener('click', function(event) {
        const searchInput = document.getElementById('search-input');
        const suggestionsContainer = document.getElementById('suggestions');
        const fullscreenSearchInput = document.getElementById('fullscreen-search-input');
        const mobileSuggestionsContainer = document.getElementById('mobile-suggestions');

        if (event.target !== searchInput && event.target !== suggestionsContainer) {
            suggestionsContainer.classList.add('d-none');
        }
        if (event.target !== fullscreenSearchInput && event.target !== mobileSuggestionsContainer) {
            mobileSuggestionsContainer.classList.add('d-none');
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        dropdownToggle.addEventListener('click', function() {
            const dropdownMenu = dropdownToggle.nextElementSibling;
            dropdownMenu.classList.toggle('show');
        });
    });

    document.addEventListener('click', function(event) {
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        const dropdownMenu = dropdownToggle.nextElementSibling;
        if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.classList.remove('show');
        }
    });

    function setTable(table) {
        selectedTable = table;
        const dropdownToggle = document.querySelector('.dropdown-toggle');
        dropdownToggle.textContent = table.charAt(0).toUpperCase() + table.slice(1);
    }

</script>
