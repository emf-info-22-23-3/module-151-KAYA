class IndexCtrl {
    /**
     * Constructor initializes the http service and binds functions.
     */
    constructor() {
        this.http = new servicesHttp();  // Initializes the HTTP service
        this.connectSuccess = this.connectSuccess.bind(this);  // Binds the success callback for connection
        this.callbackError = this.callbackError.bind(this);  // Binds the error callback
        this.getArmorNamesSuccess = this.getArmorNamesSuccess.bind(this);  // Binds the success callback for fetching armor names
        this.getAnnoncesSuccess = this.getAnnoncesSuccess.bind(this);  // Binds the success callback for fetching announcements
    }

    /**
     * Success callback for handling successful connection response.
     * @param {XMLDocument} data - The XML response data.
     * @param {string} text - The text status of the response.
     * @param {jqXHR} jqXHR - The jqXHR object for the request.
     */
    connectSuccess(data, text, jqXHR) {
        console.log("connectSuccess called");
        if ($(data).find("success").text() === 'true') {
            console.log($(data));
            Toastify({
                text: "Login successful",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#33cc33"
            }).showToast();

            // Redirect based on user role (admin or client)
            if ($(data).find("isAdmin").text() === 'true') {
                window.location.href = "views/admin.html";
            } else {
                window.location.href = "views/client.html";
            }
        } else {
            console.log($(data));
            Toastify({
                text: "Login failed. Incorrect email or password.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ff3333"
            }).showToast();
        }
    }

    /**
     * Success callback for handling successful logout response.
     * @param {XMLDocument} data - The XML response data.
     * @param {string} text - The text status of the response.
     * @param {jqXHR} jqXHR - The jqXHR object for the request.
     */
    disconnectSuccess(data, text, jqXHR) {
        Toastify({
            text: "User disconnected",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#33cc33"
        }).showToast();

        window.location.href = "../login.html";  // Redirect to login page after disconnect
    }

    /**
     * Success callback for fetching armor names from the server.
     * @param {XMLDocument} data - The XML response containing armor names.
     * @param {string} text - The text status of the response.
     * @param {jqXHR} jqXHR - The jqXHR object for the request.
     */
    getArmorNamesSuccess(data, text, jqXHR) {
        console.log("getArmorNamesSuccess called");
        console.log("Received data:", data);

        const $select = $('#armorNameSelect');
        $select.empty();  // Clear the select dropdown
        $select.append('<option value="">Select an armor set...</option>');  // Default option

        $(data).find("armor").each(function () {
            const id = $(this).find("id").text();  // Get armor ID
            const name = $(this).find("name").text();  // Get armor name
            console.log("Adding armor:", id, name);
            $select.append(`<option value="${id}">${name}</option>`);  // Append armor options to select dropdown
        });

        // Add change event listener to update selected armor ID in localStorage and fetch announcements
        $select.off('change').on('change', function () {
            const selectedId = $(this).val();
            if (selectedId) {
                console.log("Selected armor set ID:", selectedId);
                localStorage.setItem('selectedArmorId', selectedId);  // Store selected armor ID in localStorage
                window.ctrl.http.getAnnoncesForArmor(selectedId, window.ctrl.getAnnoncesSuccess, window.ctrl.callbackError);  // Fetch armor announcements
            } else {
                $('#addArmorForm')[0].reset();  // Clear the form if no armor is selected
            }
        });
    }

    /**
     * Success callback for fetching announcements related to a specific armor set.
     * @param {XMLDocument} data - The XML response containing armor announcements.
     */
    getAnnoncesSuccess(data) {
        console.log("getAnnoncesSuccess called");
        console.log("Received data:", data);

        // Check if data is a string and parse it if necessary
        if (typeof data === "string") {
            console.log("Data is a string, attempting to parse...");
            data = $.parseXML(data);  // Convert string to XML document
        }

        const $xml = $(data);  // jQuery-wrapped XML document

        // Extract relevant elements from the XML document
        const setWanted = $xml.find("response setWanted");
        const capSource = $xml.find("response setWanted CapSource");
        const tunicSource = $xml.find("response setWanted TunicSource");
        const trousersSource = $xml.find("response setWanted TrousersSource");
        console.log("Found <set> elements:", setWanted.length);

        // If relevant data exists, populate the form with the received armor set details
        if (setWanted.length > 0 || capSource.length > 0 || tunicSource.length > 0 || trousersSource.length > 0) {
            setWanted.each(function () {
                const pkSet = $(this).find("id").text();  // Get the ID of the set
                const nom = $(this).find("name").text();  // Get the name of the set
                const capNom = $(this).find("cap_name").text();  // Get the cap name
                const tunicNom = $(this).find("tunic_name").text();  // Get the tunic name
                const trousersNom = $(this).find("trousers_name").text();  // Get the trousers name
                const description = $(this).find("description").text();  // Get the description
                const effet = $(this).find("effect").text();  // Get the effect
                const imageSet = $(this).find("image").text();  // Get the image URL

                console.log("Armor Set Data:", {
                    pkSet, nom, capNom, tunicNom, trousersNom, description, effet, imageSet
                });

                // Populate the form fields with the received data
                $("#armorName").val(nom);
                $("#armorCapName").val(capNom);
                $("#armorTunicName").val(tunicNom);
                $("#armorTrousersName").val(trousersNom);
                $("#armorEffect").val(effet);
                $("#armorDescription").val(description);

                // Set the source type dropdowns
                $("#armorCapSourceType").append($(this).find('cap_source').text());
                $("#armorTunicSourceType").append($(this).find('tunic_source').text());
                $("#armorTrousersSourceType").append($(this).find('trousers_source').text());

                // Show success toast notification
                Toastify({
                    text: "Armor set details loaded successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#33cc33"
                }).showToast();
            });

            // Handle each source (cap, tunic, trousers) to populate form fields and set dropdown options
            capSource.each(function () {
                const capSource = $(this).find("source").text();
                const capSourceType = $(this).find("type_source").text();
                $("#armorCapSource").val(capSource);
                $("#armorCapSourceType option").each(function () {
                    if ($(this).val() === capSourceType) {
                        $(this).prop("selected", true);  // Select the matching option
                    }
                });

                // Show success toast notification
                Toastify({
                    text: "Armor set details loaded successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#33cc33"
                }).showToast();
            });

            tunicSource.each(function () {
                const tunicSource = $(this).find("source").text();
                const tunicSourceType = $(this).find("type_source").text();
                $("#armorTunicSource").val(tunicSource);
                $("#armorTunicSourceType option").each(function () {
                    if ($(this).val() === tunicSourceType) {
                        $(this).prop("selected", true);  // Select the matching option
                    }
                });

                // Show success toast notification
                Toastify({
                    text: "Armor set details loaded successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#33cc33"
                }).showToast();
            });

            trousersSource.each(function () {
                const trousersSource = $(this).find("source").text();
                const trousersSourceType = $(this).find("type_source").text();
                $("#armorTrousersSource").val(trousersSource);
                $("#armorTrousersSourceType option").each(function () {
                    if ($(this).val() === trousersSourceType) {
                        $(this).prop("selected", true);  // Select the matching option
                    }
                });

                // Show success toast notification
                Toastify({
                    text: "Armor set details loaded successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#33cc33"
                }).showToast();
            });
        } else {
            // Show error toast if no data found
            Toastify({
                text: "No armor sets found",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ff3333"
            }).showToast();
        }
    }

    /**
     * Success callback for fetching source types from the server.
     * @param {XMLDocument} data - The XML response containing source types.
     */
    getSourceTypesSuccess(data) {
        console.log("getSourceTypesSuccess called");
        console.log("Received data:", data);

        // Check if data is a string and parse it if necessary
        if (typeof data === "string") {
            console.log("Data is a string, attempting to parse...");
            data = $.parseXML(data);  // Convert string to XML document
        }

        console.log("Parsed XML:", data);  // Log parsed XML to inspect its structure

        const $xml = $(data);  // jQuery-wrapped XML document

        // Find source types in the response
        const sourceTypes = $xml.find("response sourceTypes sourceType");

        console.log("Source Types Elements:", sourceTypes);

        if (sourceTypes.length > 0) {
            // Append each source type to the corresponding select elements
            sourceTypes.each(function () {
                const value = $(this).find("pk_type_source").text();
                const label = $(this).find("type").text();
                console.log("Source Type:", value, label);
                $("#armorCapSourceType").append(`<option value="${value}">${label}</option>`);
                $("#armorTunicSourceType").append(`<option value="${value}">${label}</option>`);
                $("#armorTrousersSourceType").append(`<option value="${value}">${label}</option>`);
            });

            // Show success toast notification
            Toastify({
                text: "Source types loaded successfully",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#33cc33"
            }).showToast();
        } else {
            // Show error toast if no source types found
            Toastify({
                text: "No source types found",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#ff3333"
            }).showToast();
        }
    }

    /**
     * Success callback for updating an armor set (add or modify).
     * @param {XMLDocument} response - The XML response from the server.
     */
    updateSetSuccess(response) {
        const successElement = $(response).find('success').text();

        if (successElement === "true") {
            // Show success toast notification for successful operation
            Toastify({
                text: "Armor set added successfully!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "green"
            }).showToast();
            document.getElementById('addArmorForm').reset();  // Reset the form after success
        } else {
            // Show error toast for failure
            Toastify({
                text: "Failed to modify armor set. Please try again.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "red"
            }).showToast();
        }
    }
        /**
    * Collects form data from the input fields and prepares it for submission.
    * @returns {FormData} - A FormData object containing all form data, including text inputs and file inputs.
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
     * Callback function to handle errors during an HTTP request.
     * Displays a Toast message to notify the user of the error.
     * @param {Object} request - The XMLHttpRequest object containing the error details.
     * @param {string} status - The status of the request (e.g., "error").
     * @param {string} error - The error message received from the request.
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
// Start method called after page load
$(document).ready(function () {
    // Initialize the controller object
    window.ctrl = new IndexCtrl();

    /**
     * Handles the login form submission.
     * Prevents the default form submission and sends login data via an HTTP request.
     * @param {Event} event - The submit event triggered when the form is submitted.
     */
    $("#loginForm").on("submit", function (event) {
        event.preventDefault();
        var email = $("#email").val();
        var password = $("#password").val();
        console.log("Form submitted");
        console.log("Sending email:", email, "and password:", password);
        window.ctrl.http.connect(email, password, window.ctrl.connectSuccess, window.ctrl.callbackError);
    });

    // If we're on the admin/client page with the armor select, load the armor names
    if ($("#armorNameSelect").length) {
        console.log("Loading armor names");
        window.ctrl.http.getArmorNames(window.ctrl.getArmorNamesSuccess, window.ctrl.callbackError);
    }

    // If we're on the page with source types, load them
    if ($("#armorCapSourceType").length) {
        console.log("Loading source types");
        window.ctrl.http.getSourceTypes(window.ctrl.getSourceTypesSuccess, window.ctrl.callbackError);
    }

    /**
     * Handles the click event on the "Add" button, navigating the user to the add.html page.
     */
    $("#addButton").on("click", function () {
        console.log("Add button clicked, navigating to add.html");
        window.location.href = "../views/add.html";
    });

    /**
     * Handles the click event on the "Modify" button, navigating the user to the modify.html page.
     */
    $("#modifyButton").on("click", function () {
        console.log("Modify button clicked, navigating to modify.html");
        window.location.href = "../views/modify.html";
    });
});
