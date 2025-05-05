class AddCtrl {
    constructor() {
        this.http = new servicesHttp();
        this.callbackError = this.callbackError.bind(this); // Binding callbackError method to 'this' context
        this.collectFormData = this.collectFormData.bind(this); // Binding collectFormData method to 'this' context
    }

    /**
     * Success callback function for adding a new armor set.
     * @param {XMLDocument | string} response - The response from the server indicating the result of the operation.
     */
    addSetSuccess(response) {
        const successElement = $(response).find('success').text();
        
        if (successElement === "true") {
            Toastify({
                text: "Armor set added successfully!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "green"
            }).showToast();
            // Reset the form after success
            document.getElementById('addArmorForm').reset();
        } else {
            Toastify({
                text: "Failed to add armor set. Please try again.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "red"
            }).showToast();
        }
    }
    
    // Escape HTML entities function
 escapeHtmlEntities(str) {
    return str.replace(/[&<>"']/g, function(match) {
        const escapeMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };
        return escapeMap[match];
    });
}

    /**
     * Collects the form data and prepares it for submission.
     * @returns {FormData} formData - The collected form data as a FormData object.
     */
    collectFormData() {
    const formData = new FormData();
    
    // Collect data from form inputs and escape HTML entities
    formData.append("armorName", this.escapeHtmlEntities($("#armorName").val()));
    formData.append("armorCapName", this.escapeHtmlEntities($("#armorCapName").val()));
    formData.append("armorCapSourceType", this.escapeHtmlEntities($("#armorCapSourceType").val()));
    formData.append("armorCapSource", this.escapeHtmlEntities($("#armorCapSource").val()));
    formData.append("armorTunicName", this.escapeHtmlEntities($("#armorTunicName").val()));
    formData.append("armorTunicSourceType", this.escapeHtmlEntities($("#armorTunicSourceType").val()));
    formData.append("armorTunicSource", this.escapeHtmlEntities($("#armorTunicSource").val()));
    formData.append("armorTrousersName", this.escapeHtmlEntities($("#armorTrousersName").val()));
    formData.append("armorTrousersSourceType", this.escapeHtmlEntities($("#armorTrousersSourceType").val()));
    formData.append("armorTrousersSource", this.escapeHtmlEntities($("#armorTrousersSource").val()));
    formData.append("armorEffect", this.escapeHtmlEntities($("#armorEffect").val()));
    formData.append("armorDescription", this.escapeHtmlEntities($("#armorDescription").val()));
    
    // If there's a file, append it (file input doesn't need HTML entity escaping)
    const fileInput = $("#armorImage")[0];
    if (fileInput && fileInput.files[0]) {
        formData.append("armorImage", fileInput.files[0]);
    }

    return formData;
}
    

    /**
     * Error callback function for HTTP requests.
     * @param {jqXHR} request - The jqXHR object representing the HTTP request.
     * @param {string} status - The status of the HTTP request.
     * @param {string} error - The error message, if any.
     */
    callbackError(request, status, error) {
        Toastify({
            text: "Error: " + error,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ff3333"
        }).showToast();
    }
    
}

$(document).ready(function () {
    window.ctrl = new AddCtrl();  // Initialize the AddCtrl object

    $("#cancelButton").on("click", function () {
        console.log("Cancel button clicked, navigating to admin.html");
        window.location.href = "../views/admin.html";  // Navigate to the admin page
    });

    /**
     * Event handler for the save button click.
     * It collects form data and calls the addSet method to send the data.
     */
    $("#saveButton").on("click", function () {
        const data = window.ctrl.collectFormData();  // Collect the form data
        window.ctrl.http.addSet(data, window.ctrl.addSetSuccess, window.ctrl.callbackError);  // Send data to addSet
    });
});
