function initializeSearchFunctionality() {
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

    function getSuggestions(keyword, table) {
        fetch(`Includes/search.php?keyword=${encodeURIComponent(keyword)}&table=${encodeURIComponent(table)}`)
            .then(response => response.json())
            .then(data => {
                const suggestionsDiv = document.getElementById('suggestions');
                suggestionsDiv.innerHTML = '';

                if (data.length > 0) {
                    suggestionsDiv.classList.remove('d-none');
                    data.forEach((suggestion, index) => {
                        const suggestionElement = document.createElement('div');
                        suggestionElement.classList.add('suggestion-item');
                        suggestionElement.tabIndex = index + 1;

                        const link = document.createElement('a');
                        link.href = suggestion.url;
                        link.textContent = suggestion.title;
                        link.classList.add('suggestion-link');

                        suggestionElement.appendChild(link);
                        suggestionsDiv.appendChild(suggestionElement);
                    });
                } else {
                    suggestionsDiv.classList.remove('d-none');
                    suggestionsDiv.textContent = 'Aucun résultat';
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    function getSuggestionsMobile(keyword, table) {
        fetch(`Includes/search.php?keyword=${encodeURIComponent(keyword)}&table=${encodeURIComponent(table)}`)
            .then(response => response.json())
            .then(data => {
                const suggestionsDiv = document.getElementById('mobile-suggestions');
                suggestionsDiv.innerHTML = '';

                if (data.length > 0) {
                    suggestionsDiv.classList.remove('d-none');
                    data.forEach((suggestion, index) => {
                        const suggestionElement = document.createElement('div');
                        suggestionElement.classList.add('suggestion-item');
                        suggestionElement.tabIndex = index + 1;

                        const link = document.createElement('a');
                        link.href = suggestion.url;
                        link.textContent = suggestion.title;
                        link.classList.add('suggestion-link');

                        suggestionElement.appendChild(link);
                        suggestionsDiv.appendChild(suggestionElement);
                    });
                } else {
                    suggestionsDiv.classList.remove('d-none');
                    suggestionsDiv.textContent = 'Aucun résultat';
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

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
        getSuggestions(this.value, selectedTable);
    });

    document.getElementById('fullscreen-search-input').addEventListener('input', function() {
        getSuggestionsMobile(this.value, selectedTable);
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
}