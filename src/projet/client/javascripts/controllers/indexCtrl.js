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
        Toastify({
            text: "User disconnected",
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "#33cc33"
        }).showToast();
        
        window.location.href = "../login.html";
    }

    getArmorNamesSuccess(data, text, jqXHR) {
        console.log("getArmorNamesSuccess called");
        console.log("Received data:", data);
        
        const $select = $('#armorNameSelect');
        $select.empty();
        $select.append('<option value="">Select an armor set...</option>');
        
        $(data).find("armor").each(function() {
            const id = $(this).find("id").text();
            const name = $(this).find("name").text();
            console.log("Adding armor:", id, name);
            $select.append(`<option value="${id}">${name}</option>`);
        });

        // Add change event listener to the select
        $select.off('change').on('change', function() {
            const selectedId = $(this).val();
            if (selectedId) {
                console.log("Selected armor set ID:", selectedId);

                 // Store the selected ID in localStorage
                localStorage.setItem('selectedArmorId', selectedId);

                window.ctrl.http.getAnnoncesForArmor(selectedId, window.ctrl.getAnnoncesSuccess, window.ctrl.CallbackError);
            } else {
                // Clear the form if no armor is selected
                $('#addArmorForm')[0].reset();
            }
        });
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
    
        console.log("Found <set> elements:", setWanted.length);
    
        // Check if there are any valid <set> elements
        if (setWanted.length > 0) {
            setWanted.each(function() {
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
                text: "Failed to modify armor set. Please try again.",
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
    
    CallbackError(request, status, error) {
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