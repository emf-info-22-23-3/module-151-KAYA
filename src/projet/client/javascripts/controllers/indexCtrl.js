class IndexCtrl {
    constructor() {
        this.http = new servicesHttp();
        this.connectSuccess = this.connectSuccess.bind(this);
        this.CallbackError = this.CallbackError.bind(this);
        this.getArmorNamesSuccess = this.getArmorNamesSuccess.bind(this);
        this.getAnnoncesSuccess = this.getAnnoncesSuccess.bind(this);
    }

    connectSuccess(data, text, jqXHR) {
        console.log("connectSuccess called");
        if ($(data).find("success").text() === 'true') {
            console.log($(data));
            console.log(data);
            Toastify({
                text: "Login successful",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#33cc33"
            }).showToast();

            if($(data).find("isAdmin").text() === 'true'){
                window.location.href = "views/admin.html"; 
            } else {
                window.location.href = "views/client.html"; 
            }
        } else {
            console.log($(data));
            console.log(data);
            Toastify({
                text: "Login failed. Incorrect email or password.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ff3333"
            }).showToast();
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

// Start method called after page load
$(document).ready(function () {
    window.ctrl = new IndexCtrl();

    $("#loginForm").on("submit", function(event) {
        event.preventDefault();
        var email = $("#email").val();
        var password = $("#password").val();
        console.log("Form submitted");
        console.log("Sending email:", email, "and password:", password);
        window.ctrl.http.connect(email, password, window.ctrl.connectSuccess, window.ctrl.CallbackError);
    });

    // If we're on the admin/client page with the armor select, load the armor names
    if ($("#armorNameSelect").length) {
        console.log("Loading armor names");
        window.ctrl.http.getArmorNames(window.ctrl.getArmorNamesSuccess, window.ctrl.CallbackError);
    }

    $("#addButton").on("click", function () {
        console.log("Add button clicked, navigating to add.html");
        window.location.href = "../views/add.html"; 
    });

    $("#modifyButton").on("click", function () {
        console.log("Add button clicked, navigating to modify.html");
        window.location.href = "../views/modify.html"; 
    });
});