class ModifyCtrl {
    constructor() {
        this.http = new servicesHttp();
        this.callbackError = this.callbackError.bind(this);
        this.getSourceTypesSuccess = this.getSourceTypesSuccess.bind(this);
        this.collectFormData = this.collectFormData.bind(this);
    }

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

    deleteSetSuccess(response) {
        console.log("Received data:", response);

        // Check if the response is a string and parse if necessary
        if (typeof response === "string") {
            console.log("Response is a string, attempting to parse...");
            response = $.parseXML(response);  // Convert string to XML document
        }
    
        const $xml = $(response);  // jQuery-wrapped XML document
    
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

    callbackError(request, status, error) {
        Toastify({
            text: "Error: " + error,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#ff3333"
        }).showToast();
    }

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

    $("#saveButton").on("click", function () {
        const data = window.ctrl.collectFormData();         
        const selectedArmorId = localStorage.getItem('selectedArmorId');
        const idSet = localStorage.getItem('selectedArmorId');
        const idCapSource = localStorage.getItem('capSourceId'); 
        const idTunicSource = localStorage.getItem('tunicSourceId'); 
        const idTrousersSource = localStorage.getItem('trousersSourceId');

        if (selectedArmorId && idSet && idCapSource && idTunicSource && idTrousersSource) {
            data.selectedArmorId = idSet; 
            data.idCapSource = idCapSource; 
            data.idTunicSource = idTunicSource; 
            data.idTrousersSource = idTrousersSource; 
        } else {
            console.error("Not all variables are found in localStorage");
        }

        console.log("Data to be sent:", data);

        window.ctrl.http.updateSet(data, window.ctrl.updateSetSuccess, window.ctrl.callbackError);
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
