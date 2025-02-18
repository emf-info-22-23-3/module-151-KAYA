/*
 * Contrôleur de la vue "index.html"
 *
 * @author Olivier Neuhaus
 * @version 1.0 / 20-SEP-2013
 */


class indexCtrl {
	connectSuccess(data, text, jqXHR) {
		console.log("chargerNotesSuccess called");

		if ($(data).find("result").text() == 'true') {
			alert("Login successful");
			// You can redirect or load the user dashboard after login
			if($(data).find("isAdmin").text() == 'true'){
				window.location.href = "views/admin.html"; 
			} else {
				window.location.href = "views/client.html"; 
			}
		} else {
			alert("Login failed. Incorrect email or password.");
		}
	}

	disconnectSuccess(data, text, jqXHR) {
		alert("Utilisateur déconnecté");
		window.location.href = "../login.html"; 
		chargerPersonnel(chargerPersonnelSuccess, CallbackError);
	}


	/**
	 * Méthode appelée en cas d'erreur lors de la lecture du webservice
	 * @param {type} data
	 * @param {type} text
	 * @param {type} jqXHR
	 */
	CallbackError(request, status, error) {
		console.log("Error login:", error); // Debug log
		alert("Error login: " + error);
	}
}

/**
 * Méthode "start" appelée après le chargement complet de la page
 */
$(document).ready(function () {
	window.ctrl = new indexCtrl();
	this.http = new httpService();
	this.http.centraliserErreurHttp(indexCtrl.afficherErreurHttp);

	var submitLogin = $("#submitLogin");

	submitLogin.click(function (event) {
		var email = document.getElementById("email").val();
		var password = document.getElementById("password").val();
		console.log("Sending email:", email, "and password:", password);
		connect(email, password, connectSuccess, CallbackError);
	});
});

