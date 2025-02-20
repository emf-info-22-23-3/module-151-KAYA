/*
 * Couche de services HTTP (worker).
 *
 * @author Olivier Neuhaus
 * @version 1.0 / 20-SEP-2013
 */


class servicesHttp {

    constructor() {
        this.BASE_URL = "http://localhost:8080/projet/server/Main.php";
    }
    /**
     * Fonction permettant de charger les données d'équipe.
     * @param {type} teamid, id de l'équipe dans laquelle trouver les joueurs
     * @param {type} Fonction de callback lors du retour avec succès de l'appel.
     * @param {type} Fonction de callback en cas d'erreur.
     */
    connect(email, password, successCallback, errorCallback) {
        // Log the email and password to make sure they are passed correctly
        console.log("Sending data to server:", { email: email, password: password });
        
        $.ajax({
            type: "POST",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'login',
                email: email,
                password: password
            },
            success: successCallback,
            error: errorCallback
        });
    }
    

    /**
     * Fonction permettant de charger les données d'équipe.
     * @param {type} teamid, id de l'équipe dans laquelle trouver les joueurs
     * @param {type} Fonction de callback lors du retour avec succès de l'appel.
     * @param {type} Fonction de callback en cas d'erreur.
     */
    disconnect(successCallback, errorCallback) {
        $.ajax({
            type: "POST",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'disconnect',
            },
            success: successCallback,
            error: errorCallback
        });
    }

    getAnnonces(successCallback, errorCallback) {
        $.ajax({
            type: "GET",
            dataType: "xml",
            url: this.BASE_URL,
            data: 'action=GET',
            success: successCallback,
            error: errorCallback
        });
    }

    addSet(successCallback, errorCallback) {
        $.ajax({
            type: "POST",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'disconnect',
            },
            success: successCallback,
            error: errorCallback
        });
    }

    modifySet(successCallback, errorCallback) {
        $.ajax({
            type: "POST",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'disconnect',
            },
            success: successCallback,
            error: errorCallback
        });
    }

    deleteSet(successCallback, errorCallback) {
        $.ajax({
            type: "PUT",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'disconnect',
            },
            success: successCallback,
            error: errorCallback
        });
    }
}
