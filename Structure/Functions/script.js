
/*document.querySelector('.toggle-btn').addEventListener('click', function() {      //pas trouvé d'utilité : pas de css utilisant la classe 'sidebar-open'
  document.querySelector('.wrapper').classList.toggle('sidebar-open');
});*/

/*function change_couleur_mode(){
  var element = document.body;
  element.dataset.bsTheme = element.dataset.bsTheme == "light" ? "dark" : "light";


  var r = document.querySelector(":root");
  var rs = getComputedStyle(r);

  var couleur_mode = element.dataset.bsTheme;



  var ec = rs.getPropertyValue("--"+couleur_mode+"-ecrit-couleur");
  var eic = rs.getPropertyValue("--"+couleur_mode+"-ecrit-inverse-couleur");
  var ffc = rs.getPropertyValue("--"+couleur_mode+"-footer-fond-couleur");
  var fbc = rs.getPropertyValue("--"+couleur_mode+"-footer-bas-couleur");
  var mc = rs.getPropertyValue("--"+couleur_mode+"-main-couleur");
  var brc = rs.getPropertyValue("--"+couleur_mode+"-barre-recherche-couleur");
  var brec = rs.getPropertyValue("--"+couleur_mode+"-barre-rechercher-ecrit-couleur");
  var bc = rs.getPropertyValue("--"+couleur_mode+"-bouton-couleur");
  var sc = rs.getPropertyValue("--"+couleur_mode+"-sidebar-couleur");
  var hsc = rs.getPropertyValue("--"+couleur_mode+"-hoover-sidebar-couleur");
  var ebc = rs.getPropertyValue("--"+couleur_mode+"-element-back-couleur");
  var ebhc = rs.getPropertyValue("--"+couleur_mode+"-element-back-hoover-couleur");

  r.style.setProperty("--actuel-ecrit-couleur", ec);
  r.style.setProperty("--actuel-ecrit-inverse-couleur", eic);
  r.style.setProperty("--actuel-footer-fond-couleur", ffc);
  r.style.setProperty("--actuel-footer-bas-couleur", fbc);
  r.style.setProperty("--actuel-main-couleur", mc);
  r.style.setProperty("--actuel-barre-recherche-couleur", brc);
  r.style.setProperty("--actuel-barre-rechercher-ecrit-couleur", brec);
  r.style.setProperty("--actuel-bouton-couleur", bc);
  r.style.setProperty("--actuel-sidebar-couleur", sc);
  r.style.setProperty("--actuel-hoover-sidebar-couleur", hsc);
  r.style.setProperty("--actuel-element-back-couleur", ebc);
  r.style.setProperty("--actuel-element-back-hoover-couleur", ebhc);
}*/

//Menu burger
const hamBurger = document.querySelector(".toggle-btn");

hamBurger.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("expand");
  document.querySelector(".main").classList.toggle("not-display");
});

function hideFocusBorder(button) {
  button.style.outline = 'none';
}

// Changer le thème
function applyTheme(theme) {
  var r = document.querySelector(":root");
  var rs = getComputedStyle(r);
  var suffixes = [
    "ecrit-couleur", "ecrit-inverse-couleur", "footer-fond-couleur", "footer-bas-couleur",
    "main-couleur", "barre-recherche-couleur", "barre-rechercher-ecrit-couleur",
    "bouton-couleur", "sidebar-couleur", "hoover-sidebar-couleur",
    "element-back-couleur", "element-back-hoover-couleur"
  ];

  suffixes.forEach(function (suffix) {
    var propValue = rs.getPropertyValue("--" + theme + "-" + suffix);
    r.style.setProperty("--actuel-" + suffix, propValue);
  });

  updateSVGColor();
}

function change_couleur_mode() {
  var element = document.body;
  var newTheme = element.dataset.bsTheme === "light" ? "dark" : "light";
  element.dataset.bsTheme = newTheme;
  applyTheme(newTheme);

  document.cookie = "theme=" + newTheme + "; path=/; max-age=604800";
  updateThemeSwitch(newTheme);
}

function updateThemeSwitch(theme) {
  var themeSwitch = document.getElementById("theme");
  if (theme === "dark") {
    themeSwitch.checked = true;
  } else {
    themeSwitch.checked = false;
  }
}

function updateSVGColor() {
  var svgPaths = document.querySelectorAll('svg path');
  var color = getComputedStyle(document.documentElement).getPropertyValue('--actuel-ecrit-couleur');

  svgPaths.forEach(function(path) {
    path.style.fill = color;
  });
}

document.addEventListener("DOMContentLoaded", function () {
  var theme = document.body.dataset.bsTheme;
  applyTheme(theme);
  updateThemeSwitch(theme);

  setTimeout(function() {
    document.body.classList.remove("hidden");
    updateSVGColor();
  }, 500);
});

// Affiche searchbar sur mobile

const loupeSearchLink = document.querySelector('.loupe-search-link');
const fullscreenSearch = document.getElementById('fullscreen-search');
const closeSearch = document.getElementById('close-search');

loupeSearchLink.addEventListener('click', function (event) {
  event.preventDefault();
  fullscreenSearch.classList.add('expand');
  fullscreenSearch.classList.remove('d-none');
  document.getElementById('fullscreen-search-input').focus();
});

closeSearch.addEventListener('click', function () {
  fullscreenSearch.classList.remove('expand');
  fullscreenSearch.classList.add('d-none');
});

window.addEventListener('pageshow', function(event) {
  if (event.persisted) {
    window.location.reload();
  }
});


// Afficher/Masquer le mot de passe
function togglePassword(targetId) {
  var passwordField = document.getElementById(targetId);
  var passwordToggleBtn = passwordField.nextElementSibling;

  if (passwordField.type === "password") {
    passwordField.type = "text";
    passwordToggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
  } else {
    passwordField.type = "password";
    passwordToggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
  }
}


// Taille image
function validateImage(event, elementId, maxSizeMB) {
  const fileInput = event.target;
  const file = fileInput.files[0];
  const previewImage = document.getElementById(elementId);

  if (file) {
    const fileSizeMB = file.size / 1024 / 1024;
    const fileType = file.type;

    if (fileSizeMB > maxSizeMB) {
      alert(`Le fichier dépasse la taille maximale de ${maxSizeMB} Mo.`);
      clearFileInput(fileInput, previewImage);
      return;
    }

    if (!['image/jpeg', 'image/jpg'].includes(fileType)) {
      alert("Seuls les formats JPG et JPEG sont autorisés.");
      clearFileInput(fileInput, previewImage);
      return;
    }

    displaySelectedImage(event, elementId);
  }
}

function clearFileInput(fileInput, previewImage) {
  fileInput.value = '';
  previewImage.src = previewImage.getAttribute('data-initial-src');
}

function displaySelectedImage(event, elementId) {
  const selectedImage = document.getElementById(elementId);
  const fileInput = event.target;

  if (fileInput.files && fileInput.files[0]) {
    const reader = new FileReader();

    reader.onload = function(e) {
      selectedImage.src = e.target.result;
    };

    reader.readAsDataURL(fileInput.files[0]);
  }
}

//Customisation
function showPromoModal(promoStatus) {
  if (promoStatus === 'success') {
    new bootstrap.Modal(document.getElementById('promoSuccessModal')).show();
  } else if (promoStatus === 'error' || promoStatus === 'already_purchased') {
    new bootstrap.Modal(document.getElementById('promoErrorModal')).show();
  }

  if (window.history.replaceState && promoStatus) {
    window.history.replaceState(null, null, window.location.pathname);
  }
}

document.addEventListener('DOMContentLoaded', function () {
  var promoStatusElement = document.getElementById('promoStatus');
  if (promoStatusElement) {
    showPromoModal(promoStatusElement.value);
  }
});

// Messagerie
function loadMessages(idFriend) {
  let messageArea = document.getElementById("messageArea");

  function scrollToBottom() {
    messageArea.scrollTop = messageArea.scrollHeight;
  }

  window.onload = scrollToBottom;

  setInterval(function() {
    fetch("Includes/loadMessages.php?id=" + idFriend)
        .then(response => {
          if (!response.ok) {
            throw new Error('Erreur lors du chargement des messages');
          }
          return response.text();
        })
        .then(data => {
          let shouldScrollToBottom = messageArea.scrollTop + messageArea.clientHeight === messageArea.scrollHeight;
          messageArea.innerHTML = data;
          if (shouldScrollToBottom) {
            scrollToBottom();
          }
        })
        .catch(error => {
          console.error(error);
        });
  }, 500);
}

// Page d'erreur
function goBack() {
  window.history.back();
}

