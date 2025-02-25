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
        const messageElement = $(response).find('message').text();

        console.log(messageElement);

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
                text: messageElement,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "red"
            }).showToast();
        }
    }

    /**
     * Collects the form data and prepares it for submission.
     * @returns {FormData} formData - The collected form data as a FormData object.
     */
    collectFormData() {
        const formData = new FormData();
    
        // Collect data from form inputs
        formData.append("armorName", $("#armorName").val());  // Replace with actual input field ID
        formData.append("armorCapName", $("#armorCapName").val());
        formData.append("armorCapSourceType", $("#armorCapSourceType").val());
        formData.append("armorCapSource", $("#armorCapSource").val());
        formData.append("armorTunicName", $("#armorTunicName").val());
        formData.append("armorTunicSourceType", $("#armorTunicSourceType").val());
        formData.append("armorTunicSource", $("#armorTunicSource").val());
        formData.append("armorTrousersName", $("#armorTrousersName").val());
        formData.append("armorTrousersSourceType", $("#armorTrousersSourceType").val());
        formData.append("armorTrousersSource", $("#armorTrousersSource").val());
        formData.append("armorEffect", $("#armorEffect").val());
        formData.append("armorDescription", $("#armorDescription").val());
    
        // If there's a file, append it
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
    $("#saveButton").on("click", function (event) {
        // Collect form data first
        const data = window.ctrl.collectFormData();

        // Validate required fields
        const requiredFields = [
            "#armorName", "#armorCapName", "#armorCapSource", 
            "#armorTunicName", "#armorTunicSource", "#armorTrousersName",
            "#armorTrousersSource", "#armorEffect", "#armorDescription"
        ];

        let isValid = true;
        requiredFields.forEach(function (field) {
            if ($(field).val().trim() === "") {
                isValid = false;
                $(field).css("border", "2px solid red"); // Visual warning (red border)
            } else {
                $(field).css("border", "1px solid #ccc"); // Reset border color if valid
            }
        });

        // If any field is empty, stop form submission and show toast message
        if (!isValid) {
            event.preventDefault();  // Prevent the form from submitting
            Toastify({
                text: "Please fill out all required fields!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "red"
            }).showToast();
            return;  // Stop further processing
        }

        // If the form is valid, call the function to add the set
        window.ctrl.http.addSet(data, window.ctrl.addSetSuccess, window.ctrl.callbackError);
    });
});
