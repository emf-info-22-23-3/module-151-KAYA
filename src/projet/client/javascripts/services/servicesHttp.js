/*
 * HTTP service layer (worker).
 *
 * @author Olivier Neuhaus
 * @version 1.0 / 20-SEP-2013
 */

class servicesHttp {

    constructor() {
        this.BASE_URL = "http://localhost:8080/projet/server/Main.php";
    }
    
    /**
     * Function to connect a user via email and password.
     * @param {string} email - The email of the user.
     * @param {string} password - The password of the user.
     * @param {function} successCallback - The callback function on successful request.
     * @param {function} errorCallback - The callback function in case of an error in the request.
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
     * Function to disconnect a user.
     * @param {function} successCallback - The callback function on successful request.
     * @param {function} errorCallback - The callback function in case of an error in the request.
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

    /**
     * Function to retrieve the armor announcements based on the ID.
     * @param {string} id - The ID of the armor to retrieve announcements for.
     * @param {function} successCallback - The callback function on successful request.
     * @param {function} errorCallback - The callback function in case of an error in the request.
     */
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

    /**
     * Function to retrieve the names of the armors.
     * @param {function} successCallback - The callback function on successful request.
     * @param {function} errorCallback - The callback function in case of an error in the request.
     */
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

    /**
     * Function to retrieve available source types.
     * @param {function} successCallback - The callback function on successful request.
     * @param {function} errorCallback - The callback function in case of an error in the request.
     */
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

    /**
     * Function to add a new armor set.
     * @param {FormData} dataGiven - The FormData object containing the new set's information.
     * @param {function} successCallback - The callback function on successful request.
     * @param {function} errorCallback - The callback function in case of an error in the request.
     */
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

    /**
     * Function to update an existing armor set.
     * @param {string} armorName - The name of the armor.
     * @param {string} armorCapName - The name of the armor cap.
     * @param {string} armorCapSourceType - The source type of the cap.
     * @param {string} armorCapSource - The source of the cap.
     * @param {string} armorTunicName - The name of the armor tunic.
     * @param {string} armorTunicSourceType - The source type of the tunic.
     * @param {string} armorTunicSource - The source of the tunic.
     * @param {string} armorTrousersName - The name of the armor trousers.
     * @param {string} armorTrousersSourceType - The source type of the trousers.
     * @param {string} armorTrousersSource - The source of the trousers.
     * @param {string} armorEffect - The effect of the armor.
     * @param {string} armorDescription - The description of the armor.
     * @param {string} selectedArmorId - The ID of the selected armor.
     * @param {string} idCapSource - The ID of the cap's source.
     * @param {string} idTunicSource - The ID of the tunic's source.
     * @param {string} idTrousersSource - The ID of the trousers' source.
     * @param {function} successCallback - The callback function on successful request.
     * @param {function} errorCallback - The callback function in case of an error in the request.
     */
    updateSet(armorName, armorCapName, armorCapSourceType, armorCapSource, armorTunicName, armorTunicSourceType, armorTunicSource, armorTrousersName, armorTrousersSourceType, armorTrousersSource, armorEffect, armorDescription, selectedArmorId, idCapSource, idTunicSource, idTrousersSource, successCallback, errorCallback) {    

        let xmlData = `
    <request>
        <armorName>${armorName}</armorName>
        <armorCapName>${armorCapName}</armorCapName>
        <armorCapSourceType>${armorCapSourceType}</armorCapSourceType>
        <armorCapSource>${armorCapSource}</armorCapSource>
        <armorTunicName>${armorTunicName}</armorTunicName>
        <armorTunicSourceType>${armorTunicSourceType}</armorTunicSourceType>
        <armorTunicSource>${armorTunicSource}</armorTunicSource>
        <armorTrousersName>${armorTrousersName}</armorTrousersName>
        <armorTrousersSourceType>${armorTrousersSourceType}</armorTrousersSourceType>
        <armorTrousersSource>${armorTrousersSource}</armorTrousersSource>
        <armorEffect>${armorEffect}</armorEffect>
        <armorDescription>${armorDescription}</armorDescription>
        <selectedArmorId>${selectedArmorId}</selectedArmorId>
        <idCapSource>${idCapSource}</idCapSource>
        <idTunicSource>${idTunicSource}</idTunicSource>
        <idTrousersSource>${idTrousersSource}</idTrousersSource>
    </request>
    `;
    
        $.ajax({
            type: "PUT",
            dataType: "xml",
            url: this.BASE_URL,  // Replace with your actual URL
            data: xmlData,  // Send the XML data
            processData: false,
            contentType: 'application/xml',  // Set content type to XML
            success: successCallback,
            error: errorCallback
        });
    }
    
    /**
     * Function to delete an armor set.
     * @param {string} idSet - The ID of the set to delete.
     * @param {string} idCapSource - The ID of the cap's source.
     * @param {string} idTunicSource - The ID of the tunic's source.
     * @param {string} idTrousersSource - The ID of the trousers' source.
     * @param {function} successCallback - The callback function on successful request.
     * @param {function} errorCallback - The callback function in case of an error in the request.
     */
    deleteSet(idSet, idCapSource, idTunicSource, idTrousersSource, successCallback, errorCallback) {
        // Construct the XML request body
        let xmlData = `<deleteRequest>
            <idSet>${idSet}</idSet>
            <idCapSource>${idCapSource}</idCapSource>
            <idTunicSource>${idTunicSource}</idTunicSource>
            <idTrousersSource>${idTrousersSource}</idTrousersSource>
            </deleteRequest>`;
    
        $.ajax({
            type: "DELETE",
            dataType: "xml",
            url: this.BASE_URL,
            data: xmlData,
            processData: false,
            contentType: "application/xml",
            success: successCallback,
            error: errorCallback
        });
    }
}
