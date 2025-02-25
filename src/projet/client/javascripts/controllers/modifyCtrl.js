class ModifyCtrl {
    constructor() {
        this.http = new servicesHttp(); // Initializes the HTTP service for communication with the server
        this.callbackError = this.callbackError.bind(this); // Binds the callbackError method to this context
        this.getSourceTypesSuccess = this.getSourceTypesSuccess.bind(this); // Binds the getSourceTypesSuccess method to this context
        this.collectFormData = this.collectFormData.bind(this); // Binds the collectFormData method to this context
    }

    /**
     * Callback function that handles the success response for fetching source types.
     * @param {string | XMLDocument} data - The data received from the server, either as a string or an XML document.
     */
    getSourceTypesSuccess(data) {
        console.log("getSourceTypesSuccess called");
        console.log("Received data:", data);
    
        // Check if data is a string and parse it if necessary
        if (typeof data === "string") {
            console.log("Data is a string, attempting to parse...");
            data = $.parseXML(data);  // Convert string to XML document
        }
    
        const $xml = $(data);  // jQuery-wrapped XML document
    
        // Log the entire parsed XML structure
        //console.log("Parsed XML:", $xml);
    
        // Find the deepest sourceTypes (those that contain pk_type_source and type)
        const sourceTypes = $xml.find("response sourceTypes sourceTypes sourceType");
    
        console.log("Found source types:", sourceTypes.length);
    
        if (sourceTypes.length > 0) {
            sourceTypes.each(function() {
                const value = $(this).find("pk_type_source").text();  // Get pk_type_source
                const label = $(this).find("type").text();  // Get type (label)
    
                console.log("Found source type:", label);
    
                // Populate each of the source type selects
                $("#armorCapSourceType").append(`<option value="${value}">${label}</option>`);
                $("#armorTunicSourceType").append(`<option value="${value}">${label}</option>`);
                $("#armorTrousersSourceType").append(`<option value="${value}">${label}</option>`);
            });
            Toastify({
                text: "Source types loaded successfully",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "#33cc33"
            }).showToast();
        } else {
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
     * Callback function that handles the success response for fetching armor set details.
     * @param {string | XMLDocument} data - The data received from the server, either as a string or an XML document.
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

        // Find all <set> elements inside the response
        const setWanted = $xml.find("response setWanted");
        const capSource = $xml.find("response setWanted CapSource");
        const tunicSource = $xml.find("response setWanted TunicSource");
        const trousersSource = $xml.find("response setWanted TrousersSource");

        console.log("Found <set> elements:", setWanted.length);

        // Check if there are any valid <set> elements
        if (setWanted.length > 0 || capSource.length > 0 || tunicSource.length > 0 || trousersSource.length > 0) {
            setWanted.each(function () {
                const pkSet = $(this).find("id").text();  // Get the id
                const nom = $(this).find("name").text();  // Get the name
                const capNom = $(this).find("cap_name").text();  // Get the cap_name
                const tunicNom = $(this).find("tunic_name").text();  // Get the tunic_name
                const trousersNom = $(this).find("trousers_name").text();  // Get the trousers_name
                const description = $(this).find("description").text();  // Get the description
                const effet = $(this).find("effect").text();  // Get the effect
                const imageSet = $(this).find("image").text();  // Get the image

                console.log("Armor Set Data:", {
                    pkSet, nom, capNom, tunicNom, trousersNom, description, effet, imageSet
                });

                localStorage.setItem('capSourceId', $(this).find('setWanted CapSource id').text());
                localStorage.setItem('tunicSourceId', $(this).find('setWanted TunicSource id').text());
                localStorage.setItem('trousersSourceId', $(this).find('setWanted TrousersSource id').text());

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

                // Show success toast
                Toastify({
                    text: "Armor set details loaded successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#33cc33"
                }).showToast();
            });

            capSource.each(function () {
                const capSource = $(this).find("source").text();  // Get the cap_name
                const capSourceType = $(this).find("type_source").text();  // Get the cap_name
                // Populate the form fields with the received data
                $("#armorCapSource").val(capSource);
                // Set the source type dropdowns
                $("#armorCapSourceType option").each(function () {
                    if ($(this).val() === capSourceType) {
                        $(this).prop("selected", true);  // Select the matching option
                    }
                });

                // Show success toast
                Toastify({
                    text: "Armor set details loaded successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#33cc33"
                }).showToast();
            });

            tunicSource.each(function () {
                const tunicSource = $(this).find("source").text();  // Get the cap_name
                const tunicSourceType = $(this).find("type_source").text();  // Get the cap_name
                // Populate the form fields with the received data
                $("#armorTunicSource").val(tunicSource);
                // Set the source type dropdowns
                $("#armorTunicSourceType option").each(function () {
                    if ($(this).val() === tunicSourceType) {
                        $(this).prop("selected", true);  // Select the matching option
                    }
                });

                // Show success toast
                Toastify({
                    text: "Armor set details loaded successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#33cc33"
                }).showToast();
            });

            trousersSource.each(function () {
                const trousersSource = $(this).find("source").text();  // Get the cap_name
                const trousersSourceType = $(this).find("type_source").text();  // Get the cap_name
                // Populate the form fields with the received data
                $("#armorTrousersSource").val(trousersSource);
                // Set the source type dropdowns
                $("#armorTrousersSourceType option").each(function () {
                    if ($(this).val() === trousersSourceType) {
                        $(this).prop("selected", true);  // Select the matching option
                    }
                });

                // Show success toast
                Toastify({
                    text: "Armor set details loaded successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "#33cc33"
                }).showToast();
            });
        } else {
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
     * Callback function that handles the success response after updating an armor set.
     * @param {string | XMLDocument} response - The response data received from the server.
     */
    updateSetSuccess(response) {
        const successElement = $(response).find('success').text();
        
        if (successElement === "true") {
            Toastify({
                text: "Armor set updated successfully!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "green"
            }).showToast();
            window.ctrl.resetForm();
        } else {
            Toastify({
                text: "Failed to update armor set. Please try again.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "red"
            }).showToast();
        }
    }

    /**
     * Handles the success response for deleting an armor set.
     * @param {XMLDocument|string} response - The server's response to the delete request, either as an XML document or string.
     */
    deleteSetSuccess(response) {
        console.log("Received data:", response);

        // Check if the response is a string and parse if necessary
        if (typeof response === "string") {
            console.log("Response is a string, attempting to parse...");
            response = $.parseXML(response);  // Convert string to XML document
        }

        console.log("Parsed XML:", $.parseXML(response)); // Check parsed XML structure directly
    
        const $xml = $(response);  // jQuery-wrapped XML document

        console.log("Wrapped XML:", $xml); // Log the jQuery-wrapped XML object


        // Log the success and message values
        const successElement = $xml.find('success').text();
        const messageElement = $xml.find('message').text();
    
        console.log("Success:", successElement);
        console.log("Message:", messageElement);
        console.log("Success Element:", successElement); // Check what the value of <success> is
        
        if (successElement === "true") {
            Toastify({
                text: "Armor set deleted successfully!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "green"
            }).showToast();
            window.ctrl.resetForm();
        } else {
            Toastify({
                text: "Failed to delete armor set. Please try again.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "red"
            }).showToast();
        }
    }

    /**
     * Collects form data and returns it as FormData object.
     * @returns {FormData} formData - The FormData object containing the collected form data.
     */
    collectFormData() {
        const formData = new FormData();  // Create FormData object
    
        // Collect data from form inputs
        formData.append("armorName", $("#armorName").val());
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
     * Handles errors during the HTTP request process.
     * @param {Object} request - The request object that caused the error.
     * @param {string} status - The status of the request.
     * @param {string} error - The error message or details.
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

    /**
     * Resets the form fields to their default values.
     */
    resetForm() {
        // Reset form fields
        $("#armorName").val("");
        $("#armorCapName").val("");
        $("#armorTunicName").val("");
        $("#armorTrousersName").val("");
        $("#armorEffect").val("");
        $("#armorDescription").val("");
    
        // Reset dropdowns
        $("#armorCapSourceType").empty();
        $("#armorTunicSourceType").empty();
        $("#armorTrousersSourceType").empty();
        
        // Reset file input
        $("#armorImage").val("");
    
        // Optionally reset image previews or any other UI elements
        $("#imagePreview").attr("src", "");  // Example for resetting image preview
    
        // Reset any other state or navigation
        localStorage.removeItem('selectedArmorId');
        window.location.href = "../views/admin.html"; // Redirect or reset to the main page
    }
}

$(document).ready(function () {
    window.ctrl = new ModifyCtrl();
    const selectedArmorId = localStorage.getItem('selectedArmorId');

    (async () => {
        try {
            // Wrap getSourceTypes in a promise to work with async/await
            const data = await new Promise((resolve, reject) => {
                window.ctrl.http.getSourceTypes(
                    (data) => resolve(data), // Success handler (resolve the promise)
                    (error) => reject(error)  // Error handler (reject the promise)
                );
            });
    
            if (selectedArmorId > 0) {
                console.log("Selected Armor ID:", selectedArmorId);
                window.ctrl.http.getAnnoncesForArmor(selectedArmorId, window.ctrl.getAnnoncesSuccess, window.ctrl.callbackError);
            } else {
                console.log("No armor selected");
                window.ctrl.resetForm();
                window.location.href = "../views/admin.html";
            }
        } catch (error) {
            window.ctrl.callbackError(error);
        }
    })();

    $("#cancelButton").on("click", function () {
        console.log("Cancel button clicked, navigating to admin.html");
        window.ctrl.resetForm();
    });

    $("#saveButton").on("click", function (event) {
        // Collect form data through your existing method
        const formData = window.ctrl.collectFormData(); 
        
        // Get values from localStorage
        const selectedArmorId = localStorage.getItem('selectedArmorId');
        const idCapSource = localStorage.getItem('capSourceId'); 
        const idTunicSource = localStorage.getItem('tunicSourceId'); 
        const idTrousersSource = localStorage.getItem('trousersSourceId');
        
        console.log("Data to be sent BEFORE id:", formData);
    
        // Validate required fields (similar to the previous approach)
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
    
        // Check if required data is present in localStorage
        if (selectedArmorId && idCapSource && idTunicSource && idTrousersSource) {
            // Prepare the variables for the updateSet function
            const armorName = formData.get("armorName");
            const armorCapName = formData.get("armorCapName");
            const armorCapSourceType = formData.get("armorCapSourceType");
            const armorCapSource = formData.get("armorCapSource");
            const armorTunicName = formData.get("armorTunicName");
            const armorTunicSourceType = formData.get("armorTunicSourceType");
            const armorTunicSource = formData.get("armorTunicSource");
            const armorTrousersName = formData.get("armorTrousersName");
            const armorTrousersSourceType = formData.get("armorTrousersSourceType");
            const armorTrousersSource = formData.get("armorTrousersSource");
            const armorEffect = formData.get("armorEffect");
            const armorDescription = formData.get("armorDescription");
    
            // Now, call the updateSet function with the extracted data
            window.ctrl.http.updateSet(
                armorName, armorCapName, armorCapSourceType, armorCapSource,
                armorTunicName, armorTunicSourceType, armorTunicSource,
                armorTrousersName, armorTrousersSourceType, armorTrousersSource,
                armorEffect, armorDescription, selectedArmorId, idCapSource,
                idTunicSource, idTrousersSource, window.ctrl.updateSetSuccess, window.ctrl.callbackError
            );
        } else {
            // If required data is missing in localStorage, show an error
            Toastify({
                text: "Required data is missing in localStorage. Please try again.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "red"
            }).showToast();
        }
    });
    

    $("#deleteButton").on("click", function () {
        const idSet = localStorage.getItem('selectedArmorId') || '';
        const idCapSource = localStorage.getItem('capSourceId') || ''; // Default to an empty string if null
        const idTunicSource = localStorage.getItem('tunicSourceId') || ''; // Default to an empty string if null
        const idTrousersSource = localStorage.getItem('trousersSourceId') || ''; // Default to an empty string if null

        console.log("Deleting Armor Set with the following IDs:");
        console.log("idSet:", idSet);
        console.log("idCapSource:", idCapSource);
        console.log("idTunicSource:", idTunicSource);
        console.log("idTrousersSource:", idTrousersSource);
        
        // Call deleteSet with the necessary data
        window.ctrl.http.deleteSet(
            idSet, 
            idCapSource, 
            idTunicSource, 
            idTrousersSource, 
            window.ctrl.deleteSetSuccess(),
            window.ctrl.callbackError
        );
    });
});
