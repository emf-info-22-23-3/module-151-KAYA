/*
 * Contrôleur de la vue "index.html"
 *
 * @author Olivier Neuhaus
 * @version 1.0 / 20-SEP-2013
 */

/**
 * Méthode appelée lors du retour avec succès du résultat des équipes
 * @param {type} data
 * @param {type} text
 * @param {type} jqXHR
 */
function chargerTeamSuccess(data, text, jqXHR) {
    console.log("charger succeeded");
    
    // Appelé lorsque la liste des équipes est reçue
    var tabEquipes = document.getElementById("tabEquipes"); // Le tableau où on va afficher les équipes
    tabEquipes.innerHTML = ""; // Vide le contenu existant dans le tableau

    // Vérifie si data est un tableau d'équipes
    for (var i = 0; i < data.length; i++) {
        var equipe = data[i];  // Chaque équipe est une chaîne de caractères

        // Créer une nouvelle ligne (tr)
        var row = tabEquipes.insertRow();  // Ajoute une nouvelle ligne dans le tableau

        // Créer la cellule pour l'ID (index + 1)
        var cell1 = row.insertCell(0); 
        cell1.textContent = i + 1;  // Affiche l'ID, qui est l'index + 1

        // Créer la cellule pour le nom de l'équipe
        var cell2 = row.insertCell(1); 
        cell2.textContent = equipe;  // Affiche le nom de l'équipe
    }
}

/**
 * Méthode appelée en cas d'erreur lors de la lecture du webservice
 * @param {type} data
 * @param {type} text
 * @param {type} jqXHR
 */
function chargerTeamError(request, status, error) {
    alert("erreur : " + error + ", request: " + request + ", status: " + status);
}

/**
 * Méthode "start" appelée après le chargement complet de la page
 */
$(document).ready(function () {
    console.log("doc ready");
    var butLoad = $("#displayTeams");
    var equipe = '';

    $.getScript("javascript/services/servicesHttp.js", function () {
        console.log("servicesHttp.js chargé !");
        chargerTeam(chargerTeamSuccess, chargerTeamError);
    });
});