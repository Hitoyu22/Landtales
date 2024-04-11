const hamBurger = document.querySelector(".toggle-btn");

hamBurger.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("expand");
  document.querySelector(".main").classList.toggle("not-display");
});


/*document.querySelector('.toggle-btn').addEventListener('click', function() {      //pas trouvé d'utilité : pas de css utilisant la classe 'sidebar-open'
  document.querySelector('.wrapper').classList.toggle('sidebar-open');
});*/

function change_couleur_mode(){
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
}

function hideFocusBorder(button) {
  button.style.outline = 'none';
}