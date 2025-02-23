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
        const successElement = $(response).find('success').text();
        
        if (successElement === "true") {
            Toastify({
                text: "Armor set deleted successfully!",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "green"
            }).showToast();
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
    
            // Process the data once getSourceTypes is complete
            window.ctrl.getSourceTypesSuccess(data);
    
            if (selectedArmorId) {
                console.log("Selected Armor ID:", selectedArmorId);
                window.ctrl.http.getAnnoncesForArmor(selectedArmorId, window.ctrl.getAnnoncesSuccess, window.ctrl.CallbackError);
            } else {
                console.log("No armor selected");
                window.location.href = "../views/admin.html";
            }
        } catch (error) {
            window.ctrl.callbackError(error);
        }
    })();
    

    $("#cancelButton").on("click", function () {
        console.log("Cancel button clicked, navigating to admin.html");
        window.location.href = "../views/admin.html"; 
    });

    $("#saveButton").on("click", function () {
        const data = window.ctrl.collectFormData();
        window.ctrl.http.updateSet(data, window.ctrl.updateSetSuccess, window.ctrl.callbackError);
    });

    $("#deleteButton").on("click", function () {
        const idSet = $("#armorId").val();
        const idCapSource = $("#armorCapSource").val();
        const idTunicSource = $("#armorTunicSource").val();
        const idTrousersSource = $("#armorTrousersSource").val();
    
        // Call deleteSet with the necessary data
        window.ctrl.http.deleteSet(
            idSet, 
            idCapSource, 
            idTunicSource, 
            idTrousersSource, 
            window.ctrl.deleteSetSuccess, 
            window.ctrl.callbackError
        );
    });
    
});