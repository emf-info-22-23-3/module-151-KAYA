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

    getAnnoncesForArmor(id, successCallback, errorCallback) {
        console.log("Fetching armor set with ID:", id); // Add debug logging
        $.ajax({
            type: "GET",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'getAnnoncesForArmor',
                id: id  // Make sure id is being passed correctly
            },
            success: successCallback,
            error: errorCallback
        });
    }

    getArmorNames(successCallback, errorCallback) {
        $.ajax({
            type: "GET",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'getArmorNames',
            },
            success: successCallback,
            error: errorCallback
        });
    }

    getSourceTypes(successCallback, errorCallback) {
        $.ajax({
            type: "GET",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'getSourceTypes',
            },
            success: successCallback,
            error: errorCallback
        });
    }

    addSet(dataGiven, successCallback, errorCallback) {
        const formData = dataGiven;
    
        formData.append("action", "addSet");
    
        $.ajax({
            type: "POST",
            dataType: "xml",
            url: this.BASE_URL,
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Let the browser set the correct content type
            success: successCallback,
            error: errorCallback
        });
    }

    updateSet(successCallback, errorCallback) {
        const formData = dataGiven;
    
        formData.append("action", "updateSet");
    
        $.ajax({
            type: "PUT",
            dataType: "xml",
            url: this.BASE_URL,
            data: formData,
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Let the browser set the correct content type
            success: successCallback,
            error: errorCallback
        });
    }

    deleteSet(id, successCallback, errorCallback) {
        $.ajax({
            type: "DELETE",
            dataType: "xml",
            url: this.BASE_URL,
            data: {
                action: 'delete',
                id: id
            },
            success: successCallback,
            error: errorCallback
        });
    }
}
